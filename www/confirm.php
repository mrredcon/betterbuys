<?php

session_start();
require 'connect.php';

// Check if the confirmation code is provided
if (isset($_GET['code'])) {
    $confirmation_code = $_GET['code'];

    // Update user status to confirmed in the database
    $sql = "UPDATE `User` SET `status` = 'confirmed' WHERE `confirmationCode` = :confirmation_code";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':confirmation_code', $confirmation_code, PDO::PARAM_STR);
    $stmt->execute();

    echo "Email address confirmed successfully!";
} else {
    echo "Confirmation code not provided.";
}
?>
