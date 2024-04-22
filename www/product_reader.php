<?php
session_start();
ob_start();

$pdo = require_once 'connect.php';

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: login.php");
    exit();
}

$csvFilePath = '/var/www/html/products.csv';

try {
    $pdo->beginTransaction(); 
    $productStmt = $pdo->prepare("INSERT INTO Product (name, description, price, discount, quantity) VALUES (?, ?, ?, ?, ?)");
    $inventoryStmt = $pdo->prepare("INSERT INTO Inventory (productId, storeId, quantity) VALUES (?, 1, ?)");
    $imageStmt = $pdo->prepare("INSERT INTO ProductImage (filepath, productId, priority) VALUES (?, ?, 1)");

    if (!file_exists($csvFilePath) || !is_readable($csvFilePath)) {
        echo "File not found or not readable.";
        exit;
    }

    if (($handle = fopen($csvFilePath, "r")) !== FALSE) {
        fgetcsv($handle);  // Skip the header row
        while (($data = fgetcsv($handle, 10000, ",")) !== FALSE) {
            $productStmt->execute([$data[1], $data[2], $data[3], $data[4], $data[5]]);
            $productId = $pdo->lastInsertId();
            $inventoryStmt->execute([$productId, $data[5]]);
            $imageStmt->execute([$data[6], $productId]);
        }
        fclose($handle);
        $pdo->commit();
        echo "CSV data imported successfully.<br>";
    } else {
        throw new Exception("Failed to open the file.");
    }
} catch (PDOException $e) {
    $pdo->rollback();
    echo "Database error: " . $e->getMessage() . "<br>";
    exit;
} catch (Exception $e) {
    $pdo->rollback();
    echo "Error: " . $e->getMessage() . "<br>";
    exit;
}

ob_end_flush();
?>

