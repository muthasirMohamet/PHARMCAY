<?php
session_start();
include 'db.php';

// Check if customer ID is provided
if (!isset($_GET['id'])) {
    header("Location: add_customer.php");
    exit();
}

$id = $_GET['id'];

// Fetch the customer's current data
$stmt = $conn->prepare("SELECT id, name, contact FROM customers WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$customer = $result->fetch_assoc();
$stmt->close();

// Handle form submission for editing
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $contact = $_POST['contact'];

    // Update the customer in the database
    $stmt = $conn->prepare("UPDATE customers SET name = ?, contact = ? WHERE id = ?");
    $stmt->bind_param("ssi", $name, $contact, $id);
    $stmt->execute();
    $stmt->close();

    // Redirect back to the customer list page
    header("Location: add_customer.php");
    exit();
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
            /* Make the sidebar fill the full height of the viewport */
            position: fixed;
            /* Fix the sidebar to the left */
            top: 0;
            /* Make sure it starts at the top */
            left: 0;
            width: 250px;
            background-color: #2c3e50;
            height: 100vh;
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

        .sidebar .header {
            font-size: 1.5rem;
            text-align: center;
            margin-bottom: 20px;
            font-weight: bold;
            color: white;
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
    <div class="container mt-5">
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

            <!-- Inventory List that will be toggled -->
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

                <h2 class="text-center">Edit Customer</h2>
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="name" class="form-label">Customer Name</label>
                        <input type="text" name="name" class="form-control"
                            value="<?php echo htmlspecialchars($customer['name']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="contact" class="form-label">Contact</label>
                        <input type="text" name="contact" class="form-control"
                            value="<?php echo htmlspecialchars($customer['contact']); ?>" required>
                    </div>
                    <button type="submit" class="btn btn-success">Update Customer</button>
                    <a href="add_customer.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>


        <!-- Scripts -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            // Sidebar Toggle Function
            function toggleSidebar() {
                const sidebar = document.getElementById('sidebar');
                sidebar.classList.toggle('collapsed');
            }

            // Toggle Sales List
            function toggleSales() {
                const salesList = document.getElementById('salesList');
                if (salesList.style.display === "none") {
                    salesList.style.display = "block"; // Show Sales list
                } else {
                    salesList.style.display = "none"; // Hide Sales list
                }
            }
        </script>

</body>

</html>