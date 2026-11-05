<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Submitted — Villasis State Polytechnic University</title>
</head>
<body style="margin: 0; padding: 0; background-color: #0E0E0E; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #D6D1C7;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #0E0E0E; padding: 40px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" style="max-width: 600px; background-color: #161616; border: 1px solid #2B2824; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
                    {{-- Header --}}
                    <tr>
                        <td style="padding: 36px 36px 24px; text-align: center; border-bottom: 1px solid #2B2824; background: linear-gradient(180deg, #1D1A16 0%, #161616 100%);">
                            <div style="font-size: 26px; font-weight: 700; letter-spacing: 0.15em; color: #C8A45D; text-transform: uppercase; font-family: 'Cinzel', 'Times New Roman', Georgia, serif;">
                                VSPU
                            </div>
                            <div style="font-size: 13px; font-weight: 600; letter-spacing: 0.2em; color: #F5F1E8; text-transform: uppercase; margin-top: 6px;">
                                Villasis State Polytechnic University
                            </div>
                            <div style="font-size: 11px; color: #8F8A80; letter-spacing: 0.08em; text-transform: uppercase; margin-top: 2px;">
                                Office of Admissions & Institutional Records
                            </div>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding: 36px;">
                            <div style="display: inline-block; background-color: rgba(200, 164, 93, 0.12); border: 1px solid rgba(200, 164, 93, 0.3); color: #C8A45D; font-size: 11px; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; padding: 4px 10px; border-radius: 4px; margin-bottom: 16px;">
                                Registration Received
                            </div>

                            <h1 style="font-size: 22px; font-weight: 700; color: #F5F1E8; margin: 0 0 14px; font-family: 'Times New Roman', Georgia, serif;">
                                Registration Received, {{ $user->name }}
                            </h1>

                            <p style="font-size: 14px; line-height: 1.6; color: #B3ADA3; margin: 0 0 20px;">
                                Thank you for registering for an institutional account at <strong>Villasis State Polytechnic University</strong>. Your enrollment record has been created and forwarded to University Administration for verification.
                            </p>

                            {{-- Summary Box --}}
                            <table role="presentation" width="100%" style="background-color: #1A1A1A; border: 1px solid #33302A; border-radius: 8px; margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <table role="presentation" width="100%">
                                            <tr>
                                                <td style="padding: 6px 0; font-size: 12px; color: #8F8A80; text-transform: uppercase; letter-spacing: 0.05em; width: 40%;">Assigned Student ID:</td>
                                                <td style="padding: 6px 0; font-size: 14px; color: #C8A45D; font-weight: 700; font-family: monospace;">{{ $user->student_id }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 6px 0; font-size: 12px; color: #8F8A80; text-transform: uppercase; letter-spacing: 0.05em;">Institutional Email:</td>
                                                <td style="padding: 6px 0; font-size: 14px; color: #F5F1E8; font-weight: 600;">{{ $user->email }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 6px 0; font-size: 12px; color: #8F8A80; text-transform: uppercase; letter-spacing: 0.05em;">Status:</td>
                                                <td style="padding: 6px 0; font-size: 13px; color: #C8A45D; font-weight: 600;">Pending Administrative Approval</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <p style="font-size: 13px; line-height: 1.6; color: #B3ADA3; margin: 0;">
                                As soon as the Administrator approves your application, you will receive an official activation dispatch confirming your login clearance. Once cleared, you can access your enrolled courses and update your security credentials whenever needed.
                            </p>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="padding: 24px 36px; text-align: center; border-top: 1px solid #2B2824; font-size: 11px; color: #6E6960; line-height: 1.6;">
                            Villasis State Polytechnic University • Learning Management System<br>
                            This is an automated enrollment notification. Please do not reply directly.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
