<?php
session_start();
include 'db.php'; // Ensure this file contains a valid connection to your database

$error = "";
$success = "";

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Check if token is valid and not expired
    $stmt = $conn->prepare("SELECT * FROM users WHERE reset_token = ? AND token_expiry > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Handle form submission for password reset
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $newPassword = htmlspecialchars(trim($_POST['new_password']));
            $confirmPassword = htmlspecialchars(trim($_POST['confirm_password']));

            if (!empty($newPassword) && !empty($confirmPassword)) {
                if ($newPassword === $confirmPassword) {
                    // Hash the new password and update the database
                    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

                    $stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, token_expiry = NULL WHERE reset_token = ?");
                    $stmt->bind_param("ss", $hashedPassword, $token);
                    $stmt->execute();

                    $success = "Your password has been successfully reset. You can now log in.";
                } else {
                    $error = "Passwords do not match.";
                }
            } else {
                $error = "Please fill in both password fields.";
            }
        }
    } else {
        $error = "Invalid or expired token.";
    }
} else {
    $error = "No token provided.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
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
        <h1 class="text-center">Reset Password</h1>
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
                <label for="new_password" class="form-label">New Password</label>
                <input type="password" class="form-control" id="new_password" name="new_password" required>
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Reset Password</button>
        </form>
    </div>
</body>
</html>
