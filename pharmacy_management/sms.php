<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send SMS</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .form-container {
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        .form-container h2 {
            margin-bottom: 20px;
            font-size: 24px;
            text-align: center;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        .form-group textarea {
            resize: none;
            height: 100px;
        }
        .btn-submit {
            width: 100%;
            background-color: #007bff;
            color: #ffffff;
            border: none;
            padding: 10px;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }
        .btn-submit:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Send SMS</h2>
        <form action="send_sms.php" method="POST">
            <div class="form-group">
                <label for="senderName">Sender Name</label>
                <input type="text" id="senderName" name="senderName" placeholder="Enter sender name" required>
            </div>
            <div class="form-group">
                <label for="receiverNumber">Receiver's Phone Number</label>
                <input type="text" id="receiverNumber" name="receiverNumber" placeholder="Enter phone number" required>
            </div>
            <div class="form-group">
                <label for="messageBody">Message</label>
                <textarea id="messageBody" name="messageBody" placeholder="Enter your message" required></textarea>
            </div>
            <button type="submit" class="btn-submit">Send SMS</button>
        </form>
    </div>
</body>
</html>
