<?php
include 'db.php';

// Fetch customers for dropdown
$customers_query = "SELECT id, name FROM customers";
$customers_result = $conn->query($customers_query);

// Handle form submission for generating invoice
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['customer_id']) && !empty($_POST['customer_id'])) {
    $customer_id = $_POST['customer_id'];

    // Fetch customer details
    $customer_query = $conn->prepare("SELECT * FROM customers WHERE id = ?");
    $customer_query->bind_param("i", $customer_id);
    $customer_query->execute();
    $customer_result = $customer_query->get_result();
    $customer = $customer_result->fetch_assoc();

    // Fetch sales/purchase details for the selected customer
    $sales_query = $conn->prepare("
        SELECT s.id, s.quantity, s.total_price, s.discount, s.final_price, s.sale_date, m.name AS product_name, m.selling_price
        FROM sales s
        JOIN medicines m ON s.medicine_id = m.id
        WHERE s.customer_id = ?");
    $sales_query->bind_param("i", $customer_id);
    $sales_query->execute();
    $sales_result = $sales_query->get_result();

    // Calculate total price for the invoice
    $total_price = 0;
    while ($sale = $sales_result->fetch_assoc()) {
        $total_price += $sale['total_price'];
    }

    // Calculate the discount and final price
    $discount = 10.00; // Example discount of 10
    $final_price = $total_price - $discount;

    // Insert invoice data into the database
    $invoice_query = $conn->prepare("INSERT INTO invoices (customer_id, total_price, discount, final_price, invoice_date) VALUES (?, ?, ?, ?, NOW())");
    $invoice_query->bind_param("iddd", $customer_id, $total_price, $discount, $final_price);
    $invoice_query->execute();
    $invoice_id = $conn->insert_id; // Get the generated invoice ID

    // Generate the invoice HTML (same as before)
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Invoice</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
            .invoice-table th,
            .invoice-table td {
                text-align: center;
            }

            .no-print {
                display: none;
            }

            @media print {
                .no-print {
                    display: none;
                }
            }
        </style>
    </head>

    <body>
        <div class="container my-4">
            <h2>Invoice</h2>
            <div class="customer-info">
                <h5>Bill to:</h5>
                <p><strong><?php echo htmlspecialchars($customer['name']); ?></strong></p>
                <p>Phone: <?php echo htmlspecialchars($customer['contact']); ?></p>
            </div>

            <div class="invoice-details">
                <h3>Invoice Details</h3>
                <table class="table table-bordered invoice-table">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th>Qty</th>
                            <th>Unit Price</th>
                            <th>Discount</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Reset total price for final calculations
                        $total_price = 0;
                        while ($sale = $sales_result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($sale['product_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($sale['quantity']) . "</td>";
                            echo "<td>$" . number_format($sale['selling_price'], 2) . "</td>";
                            echo "<td>" . htmlspecialchars($sale['discount']) . "%</td>";
                            echo "<td>$" . number_format($sale['total_price'], 2) . "</td>";
                            echo "</tr>";
                            $total_price += $sale['total_price'];
                        }
                        ?>
                    </tbody>
                </table>

                <div class="invoice-footer">
                    <p><strong>Subtotal:</strong> $<?php echo number_format($total_price, 2); ?></p>
                    <p><strong>Discount (10%):</strong> $<?php echo number_format($total_price * 0.10, 2); ?></p>
                    <p><strong>Total:</strong> $<?php echo number_format($total_price - $total_price * 0.10, 2); ?></p>
                </div>
            </div>

            <!-- Button to print the invoice -->
            <button onclick="window.print()" class="btn btn-success no-print">Print Invoice</button>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>

    </html>
    <?php
    // Close the database connection
    $conn->close();
    exit();
}
?>

<!-- Frontend Form -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medicine Transaction</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container my-4">
        <h2>Transaction Form</h2>
        <form method="POST" action="generate_invoice.php">
            <div class="mb-3">
                <label for="customer" class="form-label">Customer</label>
                <select class="form-select" id="customer" name="customer_id" required>
                    <option value="">Select a Customer</option>
                    <?php while ($customer = $customers_result->fetch_assoc()): ?>
                        <option value="<?php echo $customer['id']; ?>"><?php echo $customer['name']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Generate Invoice</button>
        </form>
    </div>
</body>

</html>
