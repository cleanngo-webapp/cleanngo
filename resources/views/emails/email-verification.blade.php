<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification - Clean Saver</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #047857;
            margin-bottom: 10px;
        }
        .otp-code {
            background-color: #f0f9ff;
            border: 2px solid #0ea5e9;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
        }
        .otp-number {
            font-size: 32px;
            font-weight: bold;
            color: #0c4a6e;
            letter-spacing: 5px;
            margin: 10px 0;
        }
        .warning {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 14px;
        }
        .button {
            display: inline-block;
            background-color: #047857;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">Clean Saver</div>
            <h1>Email Verification Required</h1>
        </div>

        @if($userName)
            <p>Hello {{ $userName }},</p>
        @else
            <p>Hello,</p>
        @endif

        <p>Thank you for registering with Clean Saver! To complete your account setup, please verify your email address using the verification code below:</p>

        <div class="otp-code">
            <p style="margin: 0 0 10px 0; font-weight: bold;">Your verification code is:</p>
            <div class="otp-number">{{ $otpCode }}</div>
            <p style="margin: 10px 0 0 0; font-size: 14px; color: #6b7280;">This code will expire in {{ $expiryMinutes }} minutes</p>
        </div>

        <div class="warning">
            <strong>Important:</strong> This code is valid for {{ $expiryMinutes }} minutes only. If you didn't request this verification, please ignore this email.
        </div>

        <p>Please enter this code in the verification form to complete your registration and start using your Clean Saver account.</p>

        <p>If you have any questions or need assistance, please don't hesitate to contact our support team.</p>

        <div class="footer">
            <p>Best regards,<br>The Clean Saver Team</p>
            <p style="font-size: 12px; color: #9ca3af;">
                This is an automated message. Please do not reply to this email.
            </p>
        </div>
    </div>
</body>
</html>
