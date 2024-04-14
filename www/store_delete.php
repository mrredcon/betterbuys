<?php
$pdo = require_once 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['store_id'])) {
    $sql = "DELETE FROM Store WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $success = $stmt->execute([':id' => $_POST['store_id']]);

    if ($success) {
        header('Location: admin.php?delete=success');
        exit;
    } else {
        echo "Failed to delete store.";
    }
} else {
    echo "Invalid request";
}
?>
