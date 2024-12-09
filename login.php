<?php
session_start();
include 'db.php'; // Ensure your db connection file is secure

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND is_active = 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $user['password'])) {
            // Regenerate session ID for security
            session_regenerate_id();

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on role
            switch ($user['role']) {
                case 'Admin':
                    header("Location: dashboard.php");
                    break;
                case 'Pharmacist':
                    header("Location: pharmacist_dashboard.php");
                    break;
                case 'SalesStaff':
                    header("Location: dashboard_staff.php");
                    break;
                default:
                    header("Location: o.php");
                    break;
            }
            exit();
        } else {
            echo "Invalid username or password.";
        }
    } else {
        echo "Invalid username or password.";
    }
    $stmt->close();
}
?>
