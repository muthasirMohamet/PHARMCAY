<?php
// Include database connection
include('db.php');

// Get the sale ID from the URL
if (isset($_GET['id'])) {
    $sale_id = $_GET['id'];

    // Fetch sale details from the database
    $sale_query = "SELECT sales.*, customers.name AS customer_name, medicines.name AS medicine_name 
                   FROM sales 
                   JOIN customers ON sales.customer_id = customers.id 
                   JOIN medicines ON sales.medicine_id = medicines.id
                   WHERE sales.id = ?";
    $stmt = $conn->prepare($sale_query);
    $stmt->bind_param('i', $sale_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $sale = $result->fetch_assoc();
    } else {
        echo "Sale not found!";
        exit;
    }
} else {
    echo "Invalid sale ID!";
    exit;
}

// Handle form submission to update sale
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $quantity = $_POST['quantity'];
    $total_price = $_POST['total_price'];
    $discount = $_POST['discount'];
    $final_price = $_POST['final_price'];

    // Update sale details in the database
    $update_query = "UPDATE sales SET quantity = ?, total_price = ?, discount = ?, final_price = ? WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param('iddii', $quantity, $total_price, $discount, $final_price, $sale_id);
    
    if ($stmt->execute()) {
        echo "Sale updated successfully!";
        header("Location: sales.php"); // Redirect to sales list
        exit;
    } else {
        echo "Error updating sale!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Sale</title>
    <!-- Include your CSS files here -->
</head>
<body>
    <h1>Edit Sale</h1>
    <form method="POST">
        <label for="customer">Customer:</label>
        <input type="text" id="customer" name="customer" value="<?php echo $sale['customer_name']; ?>" disabled><br><br>

        <label for="medicine">Medicine:</label>
        <input type="text" id="medicine" name="medicine" value="<?php echo $sale['medicine_name']; ?>" disabled><br><br>

        <label for="quantity">Quantity:</label>
        <input type="number" id="quantity" name="quantity" value="<?php echo $sale['quantity']; ?>"><br><br>

        <label for="total_price">Total Price:</label>
        <input type="number" step="0.01" id="total_price" name="total_price" value="<?php echo $sale['total_price']; ?>"><br><br>

        <label for="discount">Discount (%):</label>
        <input type="number" id="discount" name="discount" value="<?php echo $sale['discount']; ?>"><br><br>

        <label for="final_price">Final Price:</label>
        <input type="number" step="0.01" id="final_price" name="final_price" value="<?php echo $sale['final_price']; ?>"><br><br>

        <button type="submit">Update Sale</button>
    </form>
</body>
</html>
