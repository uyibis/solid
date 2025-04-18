<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slave Account Purchase Confirmation</title>
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
        .account-details {
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
        <h2>Successful Slave Account Purchase</h2>
        <p>Dear {{$slave_name}},</p>
        <p>Thank you for your purchase! Your Slave Account has been successfully created. Below are your account details:</p>
        <div class="account-details">
            <ul>
                <li>Slave ID: <strong>{{$slave_trader}}</strong></li>
                <li>Associated Master IDs:</li>
                <ul>
                    @foreach($masterIds as $masterId)
                        <li><strong>{{ $masterId }}</strong></li>
                    @endforeach
                </ul>
            </ul>
        </div>
        <p>Your account is now active and ready to use.</p>
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
