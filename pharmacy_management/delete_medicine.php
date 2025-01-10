<?php
// Include the database connection file
include 'db.php';

// Check if an ID is provided
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Start a transaction
    $conn->begin_transaction();

    try {
        // Check if related sales records exist
        $queryCheck = "SELECT COUNT(*) FROM sales WHERE medicine_id = ?";
        $stmtCheck = $conn->prepare($queryCheck);
        $stmtCheck->bind_param("i", $id);
        $stmtCheck->execute();
        $stmtCheck->bind_result($count);
        $stmtCheck->fetch();
        $stmtCheck->close();

        if ($count > 0) {
            // Delete related records in the sales table
            $querySales = "DELETE FROM sales WHERE medicine_id = ?";
            $stmtSales = $conn->prepare($querySales);
            $stmtSales->bind_param("i", $id);
            $stmtSales->execute();
            $stmtSales->close();
        }

        // Delete the medicine record
        $queryMedicine = "DELETE FROM medicines WHERE id = ?";
        $stmtMedicine = $conn->prepare($queryMedicine);
        $stmtMedicine->bind_param("i", $id);
        $stmtMedicine->execute();
        $stmtMedicine->close();

        // Commit the transaction
        $conn->commit();

        // Redirect to the inventory list
        header('Location: inventory.php');
        exit();
    } catch (Exception $e) {
        // Rollback the transaction on error
        $conn->rollback();
        echo "Error deleting medicine: " . $e->getMessage();
    }
}

$conn->close();
?>
