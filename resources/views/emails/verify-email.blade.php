<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email Address</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f1f5f9; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased;">

<table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="background-color: #f1f5f9;">
    <tr>
        <td align="center" style="padding: 40px 15px;">

            <table width="600" cellpadding="0" cellspacing="0" role="presentation"
                   style="width: 100%; max-width: 600px; background-color: #ffffff; border-radius: 14px; overflow: hidden; box-shadow: 0 5px 20px rgba(15, 23, 42, 0.08);">

                <!-- Header -->
                <tr>
                    <td style="background-color: #1e3a8a; padding: 32px 35px; text-align: left;">
                        <h1 style="margin: 0; color: #ffffff; font-size: 24px; font-weight: 700; letter-spacing: -0.5px;">
                            {{ config('app.name') }}
                        </h1>
                        <p style="margin: 6px 0 0; color: #bfdbfe; font-size: 14px; font-weight: 400;">
                            Account Security & Verification
                        </p>
                    </td>
                </tr>

                <!-- Content Body -->
                <tr>
                    <td style="padding: 40px 35px;">

                        <h2 style="margin: 0 0 16px; color: #0f172a; font-size: 20px; font-weight: 600;">
                            Hello {{ $user->first_name ?? $user->name ?? 'there' }},
                        </h2>

                        <p style="margin: 0 0 24px; color: #475569; font-size: 15px; line-height: 1.6;">
                            Thank you for registering with <strong>{{ config('app.name') }}</strong>! Please verify your email address to complete your registration and activate your account.
                        </p>

                        <!-- CTA Button -->
                        <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin: 28px 0;">
                            <tr>
                                <td align="center">
                                    <a href="{!! $url !!}" target="_blank"
                                       style="background-color: #2563eb; color: #ffffff; display: inline-block; padding: 14px 32px; font-size: 15px; font-weight: 600; text-decoration: none; border-radius: 8px; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);">
                                        Verify Email Address
                                    </a>
                                </td>
                            </tr>
                        </table>

                        <!-- Expiry Note -->
                        <div style="background-color: #eff6ff; border-left: 4px solid #2563eb; padding: 16px; border-radius: 6px; margin-bottom: 28px;">
                            <p style="margin: 0; color: #1e3a8a; font-size: 13px; line-height: 1.5;">
                                <strong>Note:</strong> This verification link will expire in 60 minutes. If you did not create an account, no further action is required.
                            </p>
                        </div>

                        <!-- Fallback Link -->
                        <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 28px 0 20px;">
                        <p style="margin: 0 0 8px; color: #64748b; font-size: 12px; line-height: 1.5;">
                            If you're having trouble clicking the "Verify Email Address" button, copy and paste the URL below into your web browser:
                        </p>
                        <p style="margin: 0; word-break: break-all; font-size: 12px; line-height: 1.5;">
                            <a href="{!! $url !!}" style="color: #2563eb; text-decoration: underline;">{!! $url !!}</a>
                        </p>

                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td style="background-color: #f8fafc; border-top: 1px solid #e2e8f0; padding: 24px 35px; text-align: center;">
                        <p style="margin: 0 0 8px; color: #64748b; font-size: 13px;">
                            Thank you for using {{ config('app.name') }}.
                        </p>
                        <p style="margin: 0; color: #94a3b8; font-size: 12px;">
                            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                        </p>
                    </td>
                </tr>

            </table>

        </td>
    </tr>
</table>

</body>
</html>
