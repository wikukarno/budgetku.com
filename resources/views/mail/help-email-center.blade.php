<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>New Help Request from {{ $name }}</title>
</head>

<body
    style="margin: 0; padding: 30px; background-color: #f4f6f8; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif; color: #1e293b;">
    <table width="100%" cellpadding="0" cellspacing="0"
        style="max-width: 640px; margin: auto; background-color: #ffffff; border-radius: 10px; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.06); overflow: hidden;">
        <tr>
            <td style="background-color: #0f172a; padding: 24px 32px;">
                <h2 style="margin: 0; font-size: 22px; color: #ffffff;">🛠️ Help Center Request</h2>
                <p style="margin: 4px 0 0; font-size: 14px; color: #cbd5e1;">Someone has submitted a request for help.
                </p>
            </td>
        </tr>

        <tr>
            <td style="padding: 32px;">
                <p style="margin: 0 0 16px; font-size: 16px;"><strong>Name:</strong> {{ $name }}</p>
                <p style="margin: 0 0 16px; font-size: 16px;">
                    <strong>Email:</strong> <a href="mailto:{{ $email }}"
                        style="color: #0ea5e9; text-decoration: none;">{{ $email }}</a>
                </p>

                <p style="margin: 30px 0 10px; font-size: 16px;"><strong>Message:</strong></p>
                <div
                    style="background-color: #f1f5f9; padding: 18px 20px; border-left: 4px solid #0ea5e9; border-radius: 6px; font-size: 15px; line-height: 1.6; white-space: pre-line;">
                    {{ $bodyMessage }}
                </div>
            </td>
        </tr>

        <tr>
            <td
                style="background-color: #f8fafc; text-align: center; padding: 20px 32px; font-size: 13px; color: #64748b;">
                This request was submitted through the <strong>Help Center</strong> on
                <a href="{{ url('/') }}" style="color: #0ea5e9; text-decoration: none;">budgetku.com</a>.
            </td>
        </tr>
    </table>
</body>

</html>