<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Email Verified</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        .message-box {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .message-box h2 {
            color: #28a745;
        }
        .message-box a {
            margin-top: 20px;
            display: inline-block;
            background-color: #28a745;
            color: #fff;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
        }
        .message-box a:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="message-box">
        <h2>Email Verified Successfully!</h2>
        <p>Thank you for verifying your email address.</p>
        <a href="{{ url('/login') }}">Click here to log in</a>
    </div>
</body>
</html>
