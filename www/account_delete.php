<?php

session_start();
require 'connect.php';

if($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['user_id'])){
    $user_id = $_GET['user_id'];

    // Delete user account from the database
    $sql = "DELETE FROM `User` WHERE `id` = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    // Redirect to the homepage after deletion
    header("Location: index.php");
    exit();
}
?>
