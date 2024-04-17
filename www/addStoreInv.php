<?php
$pdo = require_once 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize input data
    $storeId = filter_input(INPUT_POST, 'storeId', FILTER_SANITIZE_NUMBER_INT);
    $productId = filter_input(INPUT_POST, 'productId', FILTER_SANITIZE_NUMBER_INT);
    $quantity = filter_input(INPUT_POST, 'quantity', FILTER_SANITIZE_NUMBER_INT);

    // Validate the sanitized input
    if (empty($storeId) || empty($productId) || empty($quantity)) {
        echo "All fields are required and must be valid numbers.";
        exit; // Stop further execution if validation fails
    }

    // Ensure the quantity is a positive integer
    if ($quantity < 1) {
        echo "Quantity must be at least 1.";
        exit;
    }

    // Prepare the SQL statement to prevent SQL injection
    $sql = "INSERT INTO Inventory (storeId, productId, quantity) VALUES (:storeId, :productId, :quantity)";
    $stmt = $pdo->prepare($sql);

    // Execute the prepared statement with bound parameters
    if ($stmt->execute([':storeId' => $storeId, ':productId' => $productId, ':quantity' => $quantity])) {
        echo "Inventory added successfully!";
    } else {
        // Log the error to troubleshoot if the insert fails
        error_log('Error in inserting inventory: ' . implode(', ', $stmt->errorInfo()), 0);
        echo "Failed to add inventory.";
    }
} else {
    echo "Invalid request method.";
}
?>

</html>
