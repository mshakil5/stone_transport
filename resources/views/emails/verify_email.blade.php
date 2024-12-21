<!DOCTYPE html>
<html>
<head>
    <title>Verify Your Email Address</title>
</head>
<body>
    <h1>Hello {{ $name }},</h1>
    <p>Thank you for registering! Please click the link below to verify your email address:</p>
    <p>
        <a href="{{ $verificationUrl }}" style="display: inline-block; padding: 10px 20px; color: #fff; background-color: #007bff; text-decoration: none; border-radius: 5px;">
            Verify Email
        </a>
    </p>
    <p>This link will expire in 5 minutes.</p>
    <p>If you did not register for an account, please ignore this email.</p>
</body>
</html>
