<?php
// Include database connection
include('db.php');

// Get the sale ID from the URL
if (isset($_GET['id'])) {
    $sale_id = $_GET['id'];

    // Delete the sale record from the database
    $delete_query = "DELETE FROM sales WHERE id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param('i', $sale_id);
    
    if ($stmt->execute()) {
        echo "Sale deleted successfully!";
        header("Location: sales.php"); // Redirect to sales list
        exit;
    } else {
        echo "Error deleting sale!";
    }
} else {
    echo "Invalid sale ID!";
}
?>
