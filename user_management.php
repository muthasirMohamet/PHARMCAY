<?php
session_start();
include 'db.php'; // Include database connection

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve user details
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    $status = $_POST['status'];

    // Check if the email already exists in the database
    $result = $conn->query("SELECT * FROM users WHERE email = '$email'");
    if ($result->num_rows > 0) {
        // Email already exists, show an error message
        echo "<div class='alert alert-danger text-center' role='alert'>
                The email address is already registered. Please use a different one.
              </div>";
    } else {
        // Insert user record into the database
        $conn->query("INSERT INTO users (username, email, password, role, status) 
                      VALUES ('$username', '$email', '$password', '$role', '$status')");
        echo "User added successfully!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dain App Pharmacy Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css">
    <style>
        /* Sidebar */
        .sidebar {
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            background-color: #2c3e50;
            padding-top: 20px;
            transition: width 0.3s;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            padding: 10px 20px;
            color: white;
            text-decoration: none;
            margin: 10px 0;
            font-size: 1rem;
            gap: 15px;
            transition: background-color 0.3s;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background-color: #d35400;
        }

        .sidebar i {
            font-size: 1.5rem;
        }

        .sidebar.collapsed {
            width: 80px;
        }

        .sidebar.collapsed a span {
            display: none;
        }

        /* Main Content */
        .main {
            margin-left: 250px;
            padding: 20px;
            transition: margin-left 0.3s ease;
        }

        .sidebar.collapsed+.main {
            margin-left: 80px;
        }

        .widget {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <a href="/pharmacy_management/dashboard.php" class="active">
            <i class="bi bi-house-door"></i>
            <span>Dashboard</span>
        </a>
        <a href="/pharmacy_management/inventory.php">
            <i class="bi bi-box"></i>
            <span>Inventory</span>
        </a>
        <a href="#" onclick="toggleSales()"> <!-- Added onclick event -->
            <i class="bi bi-cash-stack"></i>
            <span>Sales</span>
        </a>
        <ul class="list-unstyled ps-4" id="salesList" style="display: none;"> <!-- Initially hidden -->
            <li><a href="/pharmacy_management/sales.php">Sales</a></li>
            <li><a href="/pharmacy_management/generate_invoice.php">Generate Invoice</a></li>
            <li><a href="/pharmacy_management/add_customer.php">Add Customers</a></li>
        </ul>
        <a href="/pharmacy_management/user_management.php">
            <i class="bi bi-person"></i>
            <span>User Management</span>
        </a>
        <a href="#">
            <i class="bi bi-gear"></i>
            <span>Settings</span>
        </a>
    </div>

    <!-- Main Content -->
    <div class="main">
        <div class="container mt-5">
            <h2 class="text-center">User Management</h2>

            <!-- Add User Form -->
            <form method="POST" action="user_management.php">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="text" name="password" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="role" class="form-label">Role</label>
                    <select name="role" class="form-select" required>
                        <option value="Admin">Admin</option>
                        <option value="salesStaff">Sales Staff</option>
                        <option value="Pharmacist">Pharmacist</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" class="form-select" required>
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Add User</button>
            </form>

            <!-- User Table -->
            <table class="table table-bordered table-striped mt-4">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Password</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch all users
                    $users = $conn->query("SELECT * FROM users");

                    // Loop through and display user data
                    if ($users->num_rows > 0) {
                        while ($user = $users->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>{$user['id']}</td>";
                            echo "<td>{$user['username']}</td>";
                            echo "<td>{$user['email']}</td>";
                            echo "<td>{$user['password']}</td>";
                            echo "<td>{$user['role']}</td>";
                            echo "<td>{$user['status']}</td>";
                            echo "<td>
                                    <a href='edit_user.php?id={$user['id']}' class='btn btn-warning btn-sm'>Edit</a>
                                    <a href='delete_user.php?id={$user['id']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to delete this user?\")'>Delete</a>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7' class='text-center'>No users found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Sidebar Toggle Function
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('collapsed');
        }

        // Toggle Inventory List
        function toggleSales() {
            const salesList = document.getElementById('salesList');
            if (salesList.style.display === "none") {
                salesList.style.display = "block"; // Show inventory list
            } else {
                salesList.style.display = "none"; // Hide inventory list
            }
        }
    </script>
</body>

</html>