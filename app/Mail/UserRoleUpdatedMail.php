<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserRoleUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public string $portalId;
    public string $plainPassword;
    public string $roleLabel;

    public function __construct(User $user, string $portalId, string $plainPassword, string $roleLabel)
    {
        $this->user = $user;
        $this->portalId = $portalId;
        $this->plainPassword = $plainPassword;
        $this->roleLabel = $roleLabel;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Villasis State Polytechnic University — Official Role & Portal Credentials Update',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.role-updated',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
