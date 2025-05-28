<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>New Contact Message</title>
</head>

<body
    style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8fafc; padding: 30px; color: #0f172a;">
    <table width="100%" cellpadding="0" cellspacing="0"
        style="max-width: 600px; margin: auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
        <tr>
            <td style="background-color: #06b6d4; padding: 20px 30px;">
                <h2 style="margin: 0; color: #ffffff; font-size: 24px;">📬 New Contact Message</h2>
            </td>
        </tr>
        <tr>
            <td style="padding: 30px;">
                <p style="font-size: 16px; margin-bottom: 10px;"><strong>Name:</strong> {{ $name }}</p>
                <p style="font-size: 16px; margin-bottom: 10px;"><strong>Email:</strong> <a href="mailto:{{ $email }}"
                        style="color: #06b6d4;">{{ $email }}</a></p>
                <p style="font-size: 16px; margin-top: 30px; margin-bottom: 5px;"><strong>Message:</strong></p>
                <div
                    style="padding: 15px; background-color: #f1f5f9; border-left: 4px solid #06b6d4; font-size: 16px; line-height: 1.6; white-space: pre-line;">
                    {{ $bodyMessage }}
                </div>
            </td>
        </tr>
        <tr>
            <td
                style="padding: 20px 30px; text-align: center; background-color: #f8fafc; font-size: 13px; color: #64748b;">
                This message was sent from the contact form on <a href="{{ url('/') }}"
                    style="color: #06b6d4; text-decoration: none;">wikukarno.com</a>
            </td>
        </tr>
    </table>
</body>

</html>