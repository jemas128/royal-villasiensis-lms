<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification Code — Villasis State Polytechnic University</title>
</head>
<body style="margin: 0; padding: 0; background-color: #0E0E0E; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #D6D1C7;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #0E0E0E; padding: 40px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" style="max-width: 580px; background-color: #161616; border: 1px solid #2B2824; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
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
                                Identity Verification & Admissions Gateway
                            </div>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding: 36px; text-align: center;">
                            <div style="display: inline-block; background-color: rgba(200, 164, 93, 0.12); border: 1px solid rgba(200, 164, 93, 0.3); color: #C8A45D; font-size: 11px; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; padding: 4px 10px; border-radius: 4px; margin-bottom: 16px;">
                                Security Verification Code
                            </div>

                            <h1 style="font-size: 22px; font-weight: 700; color: #F5F1E8; margin: 0 0 12px; font-family: 'Times New Roman', Georgia, serif;">
                                Hello, {{ $name }}
                            </h1>

                            <p style="font-size: 14px; line-height: 1.6; color: #B3ADA3; margin: 0 0 28px; text-align: left;">
                                Thank you for beginning your registration for the <strong>Villasis State Polytechnic University Academic Learning Portal</strong>. To confirm this email address and finalize your submission for administrative clearance, please enter the confirmation code below:
                            </p>

                            {{-- Code Display Box --}}
                            <div style="background: #101010; border: 1px dashed #C8A45D; border-radius: 8px; padding: 20px; margin: 0 auto 28px; max-width: 340px;">
                                <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.15em; color: #8F8A80; margin-bottom: 8px;">
                                    Your 6-Digit Confirmation Code
                                </div>
                                <div style="font-family: 'Courier New', monospace; font-size: 34px; font-weight: 700; letter-spacing: 0.35em; color: #C8A45D; padding-left: 0.35em;">
                                    {{ $code }}
                                </div>
                                <div style="font-size: 11px; color: #6E6960; margin-top: 8px;">
                                    Valid for 15 minutes
                                </div>
                            </div>

                            <p style="font-size: 13px; line-height: 1.5; color: #8F8A80; margin: 0; text-align: left;">
                                If you did not initiate this registration request, please disregard this email. Do not share this code with anyone.
                            </p>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="padding: 24px 36px; text-align: center; border-top: 1px solid #2B2824; font-size: 11px; color: #6E6960; line-height: 1.6;">
                            Villasis State Polytechnic University • Automated SMTP Verification<br>
                            This transmission is confidential and intended solely for the recipient.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
