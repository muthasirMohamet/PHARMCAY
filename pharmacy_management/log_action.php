<?php
include 'db_connection.php';

$user_id = $_SESSION['user_id'];
$action = "Added new stock"; // Example action

// Log the action
$stmt = $pdo->prepare("INSERT INTO user_actions (user_id, action) VALUES (:user_id, :action)");
$stmt->execute([
    'user_id' => $user_id,
    'action' => $action
]);

echo "Action logged.";
?>
