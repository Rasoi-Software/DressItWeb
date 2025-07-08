<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $data['subject'] ?? 'OTP Email' }}</title>
</head>
<body>
    <h2>{{ $data['subject'] ?? 'OTP Email' }}</h2>

    <p>Hello {{ $data['name'] ?? 'User' }},</p>

    <p>Your OTP is: <strong>{{ $data['otp'] }}</strong></p>

    <p>This OTP is valid until: {{ \Carbon\Carbon::parse($data['expires_at'])->format('d M Y H:i') }}</p>

    <br>
    <p>Thank you,<br>dressitweb</p>
</body>
</html>
