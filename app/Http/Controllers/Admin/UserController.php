<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AccountApprovedMail;
use App\Mail\AccountCreatedMail;
use App\Mail\UserRoleUpdatedMail;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::with('department')->latest();

        $pendingCount = User::where('is_active', false)->count();

        if ($request->filled('status')) {
            if ($request->status === 'pending') {
                $query->where('is_active', false);
            } elseif ($request->status === 'active') {
                $query->where('is_active', true);
            }
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('student_id', 'like', "%{$search}%")
                  ->orWhere('employee_id', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users', 'pendingCount'));
    }

    public function approve(User $user): RedirectResponse
    {
        $user->update(['is_active' => true]);

        $plainPassword = null;
        if (!empty($user->initial_password_encrypted)) {
            try {
                $plainPassword = \Illuminate\Support\Facades\Crypt::decryptString($user->initial_password_encrypted);
            } catch (\Exception $e) {
                $plainPassword = null;
            }
        }

        // Send approval email (portal ID + password) via Laravel SMTP
        try {
            Mail::to($user->email)
                ->send(new AccountApprovedMail($user, $plainPassword));
        } catch (\Exception $e) {
            Log::warning("Could not send approval email to {$user->email}: " . $e->getMessage());
        }

        return back()->with('success', "Institutional account for {$user->name} has been approved and activated. An official clearance email with portal details has been dispatched.");
    }

    public function changeRole(Request $request, User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot modify your own administrator role.');
        }

        $validated = $request->validate([
            'role' => 'required|in:admin,teacher,student',
        ]);

        $newRole = $validated['role'];
        $year = date('Y');

        $roleLabels = [
            'admin' => 'University Administrator',
            'teacher' => 'Faculty Teacher',
            'student' => 'Student Scholar',
        ];

        $currentId = $user->student_id ?? ($user->employee_id ?? '');
        $seqSuffix = null;
        if (preg_match('/^[A-Z]+-(\d{4}-\d+)$/', $currentId, $matches)) {
            $seqSuffix = $matches[1];
        }

        $updateData = ['role' => $newRole];

        if ($newRole === 'teacher') {
            // Generate portal ID starting with FAC
            $candidate = $seqSuffix ? ('FAC-' . $seqSuffix) : null;
            if ($candidate && !User::where('employee_id', $candidate)->where('id', '!=', $user->id)->exists()) {
                $portalId = $candidate;
            } else {
                $next = User::where('employee_id', 'like', 'FAC-%')->count() + 1;
                do {
                    $portalId = 'FAC-' . $year . '-' . str_pad($next++, 4, '0', STR_PAD_LEFT);
                } while (User::where('employee_id', $portalId)->where('id', '!=', $user->id)->exists());
            }

            $updateData['employee_id'] = $portalId;
            $updateData['student_id'] = null;
            // Department stays untouched (preserve whatever user already has)
        } elseif ($newRole === 'admin') {
            // Generate portal ID starting with EMP
            $candidate = $seqSuffix ? ('EMP-' . $seqSuffix) : null;
            if ($candidate && !User::where('employee_id', $candidate)->where('id', '!=', $user->id)->exists()) {
                $portalId = $candidate;
            } else {
                $next = User::where('employee_id', 'like', 'EMP-%')->count() + 1;
                do {
                    $portalId = 'EMP-' . $year . '-' . str_pad($next++, 4, '0', STR_PAD_LEFT);
                } while (User::where('employee_id', $portalId)->where('id', '!=', $user->id)->exists());
            }

            $updateData['employee_id'] = $portalId;
            $updateData['student_id'] = null;
            // Erase department for admin accounts
            $updateData['department_id'] = null;
        } else {
            // Role is student: start with STU
            $candidate = $seqSuffix ? ('STU-' . $seqSuffix) : null;
            if ($candidate && !User::where('student_id', $candidate)->where('id', '!=', $user->id)->exists()) {
                $portalId = $candidate;
            } else {
                $next = User::whereNotNull('student_id')->count() + 1;
                do {
                    $portalId = 'STU-' . $year . '-' . str_pad($next++, 4, '0', STR_PAD_LEFT);
                } while (User::where('student_id', $portalId)->where('id', '!=', $user->id)->exists());
            }

            $updateData['student_id'] = $portalId;
            $updateData['employee_id'] = null;
        }

        // Generate a new secure institutional temporary password
        $plainPassword = 'VSPU#' . strtoupper(\Illuminate\Support\Str::random(4)) . mt_rand(100, 999);
        $updateData['password'] = Hash::make($plainPassword);
        $updateData['initial_password_encrypted'] = \Illuminate\Support\Facades\Crypt::encryptString($plainPassword);
        $updateData['is_active'] = true;

        $user->update($updateData);
        $user->refresh();
        $user->load('department');

        // Send email with new portal id and password to user's registered email
        $emailDispatched = false;
        try {
            Mail::to($user->email)->send(new UserRoleUpdatedMail(
                $user,
                $portalId,
                $plainPassword,
                $roleLabels[$newRole]
            ));
            $emailDispatched = true;
        } catch (\Exception $e) {
            Log::warning("Could not dispatch role update email to {$user->email}: " . $e->getMessage());
        }

        $feedbackMsg = "{$user->name} is now assigned as {$roleLabels[$newRole]}. New Portal ID: {$portalId}.";
        if ($emailDispatched) {
            $feedbackMsg .= " New Portal ID and credentials have been dispatched to {$user->email}.";
        } else {
            $feedbackMsg .= " Temporary Password: {$plainPassword} (Note: automated email failed to send, please provide credentials manually).";
        }

        return back()->with('success', $feedbackMsg);
    }

    public function toggleActive(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot deactivate your own administrative account.');
        }

        $user->update(['is_active' => ! $user->is_active]);

        $statusStr = $user->is_active ? 'activated' : 'suspended';
        return back()->with('success', "Account for {$user->name} is now {$statusStr}.");
    }

    public function create(): View
    {
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        return view('admin.users.create', compact('departments'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'role' => 'required|in:admin,teacher,student',
            'department_id' => 'nullable|exists:departments,id',
            'is_active' => 'boolean',
        ]);

        $role = $validated['role'];
        $year = date('Y');

        // Automatically generate unique Portal ID
        if ($role === 'student') {
            $next = User::whereNotNull('student_id')->count() + 1;
            do {
                $portalId = 'STU-' . $year . '-' . str_pad($next++, 4, '0', STR_PAD_LEFT);
            } while (User::where('student_id', $portalId)->exists());

            $validated['student_id'] = $portalId;
            $validated['employee_id'] = null;
        } else {
            $prefix = $role === 'admin' ? 'EMP-' : 'FAC-';
            $next = User::whereNotNull('employee_id')->count() + 1;
            do {
                $portalId = $prefix . $year . '-' . str_pad($next++, 4, '0', STR_PAD_LEFT);
            } while (User::where('employee_id', $portalId)->exists());

            $validated['employee_id'] = $portalId;
            $validated['student_id'] = null;
        }

        if ($role === 'admin') {
            $validated['department_id'] = null;
        }

        // Automatically generate secure institutional temporary password (e.g. VSPU#7K9X482)
        $plainPassword = 'VSPU#' . strtoupper(\Illuminate\Support\Str::random(4)) . mt_rand(100, 999);

        $validated['password'] = Hash::make($plainPassword);
        $validated['initial_password_encrypted'] = \Illuminate\Support\Facades\Crypt::encryptString($plainPassword);
        $validated['is_active'] = $request->boolean('is_active', true);

        $user = User::create($validated);
        $user->load('department');

        // Render full institutional HTML email
        $htmlEmail = view('emails.account-created', [
            'user' => $user,
            'plainPassword' => $plainPassword,
        ])->render();

        // Dispatch credentials email to the new user via Laravel SMTP
        $emailDispatched = false;
        try {
            Mail::to($user->email)
                ->send(new AccountCreatedMail($user, $plainPassword));
            $emailDispatched = true;
        } catch (\Exception $e) {
            Log::warning("Could not dispatch credentials email to {$user->email}: " . $e->getMessage());
        }

        session()->flash('newly_created_user', [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'portal_id' => $portalId,
            'temporary_password' => $plainPassword,
            'email_sent' => $emailDispatched,
            'email_notice' => $emailDispatched ? null : "Could not automatically dispatch email to {$user->email}. Please share credentials directly.",
        ]);

        $feedbackMsg = "User account for {$user->name} was provisioned! Portal ID: {$portalId} | Temporary Password: {$plainPassword}.";
        if ($emailDispatched) {
            $feedbackMsg .= " Official credentials dispatched directly to {$user->email}.";
        } else {
            $feedbackMsg .= " Note: Automated email could not be sent to {$user->email}. Please provide credentials manually or configure SMTP.";
        }

        return redirect()->route('admin.users.index')->with('success', $feedbackMsg);
    }

    public function resendCredentials(User $user): RedirectResponse
    {
        $plainPassword = $user->initial_password;
        if (!$plainPassword) {
            return back()->with('error', "No initial temporary password on record for {$user->name}. You may assign a new password directly below.");
        }

        $htmlEmail = view('emails.account-created', [
            'user' => $user,
            'plainPassword' => $plainPassword,
        ])->render();

        $sent = false;
        try {
            Mail::to($user->email)
                ->send(new AccountCreatedMail($user, $plainPassword));
            $sent = true;
        } catch (\Exception $e) {
            Log::warning("Could not resend credentials to {$user->email}: " . $e->getMessage());
        }

        if ($sent) {
            return back()->with('success', "Credentials email dispatched successfully to {$user->email}.");
        }

        return back()->with('warning', "Could not automatically deliver email to {$user->email}. (Please configure SMTP credentials in .env or verify your custom domain). You can copy the credentials directly above.");
    }

    public function edit(User $user): View
    {
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        return view('admin.users.edit', compact('user', 'departments'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$user->id,
            'role' => 'required|in:admin,teacher,student',
            'department_id' => 'nullable|exists:departments,id',
            'student_id' => 'nullable|string|max:20|unique:users,student_id,'.$user->id,
            'employee_id' => 'nullable|string|max:20|unique:users,employee_id,'.$user->id,
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => [Password::defaults()]]);
            $validated['password'] = Hash::make($request->password);
            $validated['initial_password_encrypted'] = \Illuminate\Support\Facades\Crypt::encryptString($request->password);
        }

        $validated['is_active'] = $request->boolean('is_active');

        if ($validated['role'] === 'admin') {
            $validated['department_id'] = null;
        }

        $user->update($validated);

        return redirect()->route('admin.users.index')->with('success', 'User account updated successfully.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own administrative account.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User account deleted successfully.');
    }
}
