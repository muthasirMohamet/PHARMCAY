<?php
session_start();
include 'db.php'; // Include database connection

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve customer and order details
    $customer_id = $_POST['customer_id'];
    $medicine_id = $_POST['medicine_id'];
    $quantity = $_POST['quantity'];
    $discount = $_POST['discount'] ?? 0;

    // Fetch medicine details
    $medicine_result = $conn->query("SELECT * FROM medicines WHERE id = $medicine_id");
    $medicine = $medicine_result->fetch_assoc();

    // Check stock availability
    if ($medicine['stock'] >= $quantity) {
        // Calculate total price
        $total_price = $medicine['selling_price'] * $quantity;
        $discounted_price = $total_price - ($total_price * $discount / 100);

        // Insert sales record into the database
        $conn->query("INSERT INTO sales (customer_id, medicine_id, quantity, total_price, discount, final_price) 
                      VALUES ($customer_id, $medicine_id, $quantity, $total_price, $discount, $discounted_price)");

        // Deduct stock
        $conn->query("UPDATE medicines SET stock = stock - $quantity WHERE id = $medicine_id");

        $message = "Sale completed! Invoice generated.";
    } else {
        $message = "Not enough stock available.";
    }

    // Fetch medicines
    $medicines = $conn->query("SELECT * FROM medicines");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dain App Pharmacy Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css">
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

        .sidebar.collapsed + .main {
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
        <a href="#" onclick="toggleSales()">
            <i class="bi bi-cash-stack"></i>
            <span>Sales</span>
        </a>
        <ul class="list-unstyled ps-4" id="salesList" style="display: none;">
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
            <h2 class="text-center">Sales Management</h2>
            <?php if (!empty($message)): ?>
                <div class="alert alert-info"><?= $message ?></div>
            <?php endif; ?>
            <form method="POST" action="sales.php">
                <div class="mb-3">
                    <label for="customer_id" class="form-label">Customer</label>
                    <select name="customer_id" class="form-select" required>
                        <option value="">Select Customer</option>
                        <?php
                        // Fetch customers from database
                        $customers = $conn->query("SELECT * FROM customers");
                        while ($customer = $customers->fetch_assoc()) {
                            echo "<option value='{$customer['id']}'>{$customer['name']}</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="medicine_id" class="form-label">Medicine</label>
                    <select name="medicine_id" class="form-select" required>
                        <option value="">Select Medicine</option>
                        <?php
                        // Fetch medicines from database
                        $medicines = $conn->query("SELECT * FROM medicines");
                        while ($medicine = $medicines->fetch_assoc()) {
                            echo "<option value='{$medicine['id']}'>{$medicine['name']} - {$medicine['selling_price']} USD</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="quantity" class="form-label">Quantity</label>
                    <input type="number" name="quantity" class="form-control" min="1" required>
                </div>

                <div class="mb-3">
                    <label for="discount" class="form-label">Discount (%)</label>
                    <input type="number" name="discount" class="form-control" min="0" max="100" value="0">
                </div>

                <button type="submit" class="btn btn-primary">Complete Sale</button>
            </form>

            <!-- Sales Table -->
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Customer</th>
                        <th>Medicine</th>
                        <th>Quantity</th>
                        <th>Total Price</th>
                        <th>Discount</th>
                        <th>Final Price</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Handle search query
                    $search_query = '';
                    if (isset($_GET['search']) && !empty($_GET['search'])) {
                        $search = $conn->real_escape_string($_GET['search']);
                        $search_query = " WHERE customers.name LIKE '%$search%' OR medicines.name LIKE '%$search%'";
                    }

                    // Fetch sales with optional search filter
                    $sales_query = "SELECT sales.*, customers.name AS customer_name, medicines.name AS medicine_name 
                        FROM sales 
                        JOIN customers ON sales.customer_id = customers.id 
                        JOIN medicines ON sales.medicine_id = medicines.id" . $search_query;
                    $sales_result = $conn->query($sales_query);

                    // Loop through and display sales data
                    if ($sales_result->num_rows > 0) {
                        while ($sale = $sales_result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>{$sale['id']}</td>";
                            echo "<td>{$sale['customer_name']}</td>";
                            echo "<td>{$sale['medicine_name']}</td>";
                            echo "<td>{$sale['quantity']}</td>";
                            echo "<td>{$sale['total_price']}</td>";
                            echo "<td>{$sale['discount']}%</td>";
                            echo "<td>{$sale['final_price']}</td>";
                            echo "<td>{$sale['sale_date']}</td>";  // Assuming 'sale_date' column exists in sales table
                            echo "<td>
                                <a href='edit_sale.php?id={$sale['id']}' class='btn btn-warning btn-sm'>Edit</a>
                                <a href='delete_sale.php?id={$sale['id']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to delete this sale?\")'>Delete</a>
                            </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='9' class='text-center'>No sales found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>

        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSales() {
            var salesList = document.getElementById('salesList');
            salesList.style.display = salesList.style.display === 'none' ? 'block' : 'none';
        }
    </script>
</body>

</html>
