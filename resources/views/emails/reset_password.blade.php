<!DOCTYPE html>
<html>
<head>
    <title>Password Reset</title>
</head>
<body>
    <p>Hello,</p>
    <p>We received a request to reset your password. Click the link below to reset it:</p>
    <p><a href="{{ url('reset-password/'.$token.'/'.$email) }}">Reset Password</a></p>
    <p>If you did not request this, please ignore this email.</p>
    <p>Thanks!</p>
</body>
</html>
