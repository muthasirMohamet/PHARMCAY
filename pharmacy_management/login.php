<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "123";
$dbname = "pharmacy_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Start the session
session_start();

$error = ""; // Initialize error variable

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = htmlspecialchars(trim($_POST['username']));
    $password = htmlspecialchars(trim($_POST['password']));

    if (!empty($username) && !empty($password)) {
        try {
            // Prepare SQL statement to prevent SQL injection
            $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND is_active = 1");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();

                // Verify the entered password against the hashed password in the database
                if (password_verify($password, $user['password'])) {
                    session_regenerate_id(true); // Secure session ID regeneration
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['role'] = $user['role'];

                    // Redirect based on role
                    $redirectMap = [
                        'Admin' => 'dashboard.php',
                        'Pharmacist' => 'pharmacist_dashboard.php',
                        'SalesStaff' => 'dashboard_staff.php',
                    ];
                    header("Location: " . ($redirectMap[$user['role']] ?? 'o.php'));
                    exit();
                } else {
                    $error = "Invalid credentials. Please check your password.";
                }
            } else {
                $error = "Invalid credentials. User not found or inactive.";
            }
        } catch (Exception $e) {
            $error = "Error during login: " . $e->getMessage();
        }
    } else {
        $error = "Please fill in all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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

        .login-container {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        .login-container h1 {
            margin-bottom: 20px;
        }

        .form-control {
            height: 45px;
        }

        .btn-primary {
            height: 45px;
        }

        .form-text {
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1 class="text-center">Login</h1>
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        <form action="" method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
            <p class="form-text text-center mt-3">Forgot your password? <a href="reset_password.php">Reset it</a></p>
        </form>
    </div>
</body>
</html>
