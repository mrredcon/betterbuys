<?php
$pdo = require_once 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $sql = "DELETE FROM Store WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $_GET['id']]);
    
    header('Location: admin.php');
    exit;
} else {
    echo "Invalid request";
}