<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $data['subject'] ?? 'Email Verification OTP' }}</title>
</head>
<body>
    <h2>{{ $data['subject'] ?? 'Email Verification OTP' }}</h2>

    <p>Hello {{ $data['name'] ?? 'User' }},</p>

    <p>Your One-Time Password (OTP) for verifying your email address is:</p>

    <h1 style="letter-spacing: 4px; color: #3490dc;">{{ $data['otp'] }}</h1>

    <p>This OTP is valid for <strong>{{ $data['expires_at'] ?? '10 minutes' }}</strong>. Please enter this code in the app to complete your verification.</p>

    <p>If you didn’t request this, please ignore this email.</p>

    <br>
    <p>Thank you,<br>dressitweb</p>
</body>
</html>
