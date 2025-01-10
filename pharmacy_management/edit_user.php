<?php
// Include your database connection
include 'db.php';
session_start();

// Generate CSRF token if not already set
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Fetch user details for editing
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        die("User not found!");
    }
} else {
    die("Invalid or missing ID.");
}

// Update user details
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Invalid CSRF token.");
    }

    // Get form data
    $name = $_POST['username'];
    $email = $_POST['email'];
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : $user['password'];
    $role = $_POST['role'];
    $is_active = isset($_POST['is_active']) ? $_POST['is_active'] : $user['is_active'];
    $status = $_POST['status'];

    // Check if email already exists (excluding current user)
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $stmt->bind_param("si", $email, $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<div class='alert alert-danger'>Error: The email is already in use by another user.</div>";
    } else {
        // Update query
        $stmt = $conn->prepare("UPDATE users SET username=?, email=?, password=?, role=?, is_active=?, status=? WHERE id=?");
        $stmt->bind_param("ssssiis", $name, $email, $password, $role, $is_active, $status, $id);

        if ($stmt->execute()) {
            echo "<script>alert('User updated successfully!'); window.location.href = 'user_management.php';</script>";
            exit;
        } else {
            echo "<div class='alert alert-danger'>Error updating user: " . $stmt->error . "</div>";
        }
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h3>Edit User</h3>
        <form action="edit_user.php?id=<?php echo $user['id']; ?>" method="POST">
            <!-- CSRF token -->
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            
            <!-- Username -->
            <div class="mb-3">
                <label for="username" class="form-label">Name</label>
                <input type="text" class="form-control" id="username" name="username" value="<?php echo $user['username']; ?>" required>
            </div>

            <!-- Email -->
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo $user['email']; ?>" required>
            </div>

            <!-- Password -->
            <div class="mb-3">
                <label for="password" class="form-label">Password (Leave blank to keep current password)</label>
                <input type="password" class="form-control" id="password" name="password">
            </div>

            <!-- Role -->
            <div class="mb-3">
                <label for="role" class="form-label">Role</label>
                <select class="form-select" id="role" name="role" required>
                    <option value="Admin" <?php if ($user['role'] == 'Admin') echo 'selected'; ?>>Admin</option>
                    <option value="Pharmacist" <?php if ($user['role'] == 'Pharmacist') echo 'selected'; ?>>Pharmacist</option>
                    <option value="SalesStaff" <?php if ($user['role'] == 'SalesStaff') echo 'selected'; ?>>SalesStaff</option>
                </select>
            </div>

            <!-- Active Status -->
            <div class="mb-3">
                <label for="is_active" class="form-label">Active Status</label>
                <select class="form-select" id="is_active" name="is_active">
                    <option value="1" <?php if ($user['is_active'] == 1) echo 'selected'; ?>>Active</option>
                    <option value="0" <?php if ($user['is_active'] == 0) echo 'selected'; ?>>Inactive</option>
                </select>
            </div>

            <!-- Status -->
            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <input type="text" class="form-control" id="status" name="status" value="<?php echo $user['status']; ?>">
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary">Update User</button>
        </form>
    </div>
</body>
</html>
