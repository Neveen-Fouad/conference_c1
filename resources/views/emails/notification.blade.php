<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $mailSubject }}</title>
</head>

<body style="margin: 0; padding: 0; background-color: #f1f5f9; font-family: Arial, sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" role="presentation">
    <tr>
        <td align="center" style="padding: 40px 15px;">

            <table width="600" cellpadding="0" cellspacing="0"
                   role="presentation"
                   style="width: 100%; max-width: 600px; background-color: #ffffff;
                          border-radius: 14px; overflow: hidden;
                          box-shadow: 0 5px 20px rgba(15, 23, 42, 0.08);">

                <tr>
                    <td style="background-color: #1e3a8a; padding: 28px 35px;">
                        <h1 style="margin: 0; color: #ffffff; font-size: 24px;">
                            {{ config('app.name') }}
                        </h1>

                        <p style="margin: 8px 0 0; color: #bfdbfe; font-size: 14px;">
                            Travel notification
                        </p>
                    </td>
                </tr>

                <tr>
                    <td style="padding: 40px 35px;">

                        <h2 style="margin: 0 0 20px; color: #0f172a;
                                   font-size: 22px;">
                            {{ $mailSubject }}
                        </h2>

                        <p style="margin: 0 0 25px; color: #475569;
                                  font-size: 16px; line-height: 1.7;">
                            {{ $notificationMessage }}
                        </p>

                        <div style="background-color: #eff6ff;
                                    border-left: 4px solid #2563eb;
                                    padding: 16px 18px;
                                    border-radius: 6px;">

                            <p style="margin: 0; color: #1e3a8a;
                                      font-size: 14px; line-height: 1.6;">
                                This is an automated notification about activity
                                on your account.
                            </p>
                        </div>

                    </td>
                </tr>

                <tr>
                    <td style="background-color: #f8fafc;
                               border-top: 1px solid #e2e8f0;
                               padding: 24px 35px;
                               text-align: center;">

                        <p style="margin: 0 0 8px; color: #64748b;
                                  font-size: 13px;">
                            Thank you for using {{ config('app.name') }}.
                        </p>

                        <p style="margin: 0; color: #94a3b8; font-size: 12px;">
                            © {{ date('Y') }} {{ config('app.name') }}.
                            All rights reserved.
                        </p>

                    </td>
                </tr>

            </table>

        </td>
    </tr>
</table>

</body>
</html>
