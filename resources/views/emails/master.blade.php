<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Master Trader Account Created</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            padding: 10px;
        }
        .header img {
            max-width: 150px;
        }
        .content {
            padding: 20px;
            text-align: center;
        }
        .content h2 {
            color: #333;
        }
        .content p {
            color: #555;
            font-size: 16px;
        }
        .master-list {
            text-align: left;
            padding: 10px 20px;
            background: #f9f9f9;
            border-radius: 5px;
            margin: 10px 0;
        }
        .footer {
            text-align: center;
            padding: 10px;
            font-size: 14px;
            color: #777;
        }
        .social-links a {
            margin: 0 10px;
            text-decoration: none;
            color: #1a73e8;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <img src="{{asset('processed_image-removebg-preview.png')}}" alt="{{env('APP_NAME')}} logo">
    </div>
    <div class="content">
        <p>Dear {{ $userTrader->name }},</p>
        <p>Your Master Trader account has been successfully created. Below are your Master Trader IDs:</p>
        <div class="master-list">
            <ul>
                @foreach ($masterIds as $id)
                    <li>Master ID: <strong>{{ $id }}</strong></li>
                @endforeach
            </ul>
        </div>

        <p>Use these IDs to manage your trading accounts efficiently.</p>
        <p>If you have any questions, feel free to contact our support team.</p>
    </div>
    <div class="footer">
        <p>Stay connected with us:</p>
        <div class="social-links">
            <a href="https://www.instagram.com/bullinbeario?igsh=NTc4MTIwNjQ2YQ==">Instagram</a> |
            <a href="https://x.com/bullinbeario?s=21">Twitter</a> |
            <a href="https://www.tiktok.com/@bullinbeario?_t=ZT-8vAtLNc69fC&_r=1">TikTok</a>
        </div>
        <p>&copy; 2024 YourCompany. All Rights Reserved.</p>
    </div>
</div>
</body>
</html>
