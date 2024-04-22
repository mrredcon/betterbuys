<?php
session_start();

// Include the database connection
$pdo = require_once 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['user_id'])) {
    try {
        $user_id = $_GET['user_id'];

        // Delete user account from the database
        $sql = "DELETE FROM `User` WHERE `id` = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        // Logout the user 
        $_SESSION = array();
        session_destroy();

        // Redirect to the homepage after deletion with success message
        $_SESSION['success_message'] = "Account successfully deleted.";
        header("Location: index.php");
        exit();
    } catch (PDOException $e) {
        // Database error occurs
        $_SESSION['error_message'] = "Database error: " . $e->getMessage();
        header("Location: index.php");
        exit();
    }
} else {
    $_SESSION['error_message'] = "Invalid request.";
    header("Location: index.php");
    exit();
}
?>
