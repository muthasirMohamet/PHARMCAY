<?php
session_start();
include 'db.php'; // Ensure this file contains a valid connection to your database

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = htmlspecialchars(trim($_POST['email']));

    if (!empty($email)) {
        try {
            // Check if email exists in the database
            $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND is_active = 1");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();

                // Generate a unique token for resetting password
                $token = bin2hex(random_bytes(50));
                $expiry = date("Y-m-d H:i:s", strtotime("+1 hour"));

                // Store the token and its expiry time in the database
                $stmt = $conn->prepare("UPDATE users SET reset_token = ?, token_expiry = ? WHERE email = ?");
                $stmt->bind_param("sss", $token, $expiry, $email);
                $stmt->execute();

                // Send password reset email
                $resetLink = "http://yourdomain.com/reset_password.php?token=$token";
                $subject = "Password Reset Request";
                $message = "Click on the following link to reset your password: $resetLink";
                mail($email, $subject, $message);

                $success = "Password reset link has been sent to your email.";
            } else {
                $error = "No user found with this email address.";
            }
        } catch (Exception $e) {
            $error = "Error: " . $e->getMessage();
        }
    } else {
        $error = "Please enter your email address.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        .container h1 {
            margin-bottom: 20px;
        }

        .form-control {
            height: 45px;
        }

        .btn-primary {
            height: 45px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center">Forgot Password</h1>
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
            <div class="alert alert-success" role="alert">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>
        <form action="" method="POST">
            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Send Reset Link</button>
        </form>
    </div>
</body>
</html>
