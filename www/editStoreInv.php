<?php
$pdo = require_once 'connect.php';

// Initialize message variable
$message = '';

// Fetch stores for dropdown
try {
    $stores = $pdo->query("SELECT id, name FROM Store")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Determine selected store
$selectedStoreId = $_POST['storeId'] ?? ($stores[0]['id'] ?? null);

// Fetch inventory for the selected store
if ($selectedStoreId) {
    try {
        $sql = "SELECT Inventory.productId, Product.name as productName, Inventory.quantity
                FROM Inventory
                JOIN Product ON Product.id = Inventory.productId
                WHERE Inventory.storeId = :storeId";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':storeId' => $selectedStoreId]);
        $inventoryItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $message = "Failed to retrieve inventory: " . $e->getMessage();
    }
}

// Handle POST request to update inventory
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $productId = $_POST['productId'];
    $quantity = $_POST['quantity'];

    try {
        $sql = "UPDATE Inventory SET quantity = :quantity WHERE productId = :productId AND storeId = :storeId";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':quantity' => $quantity, ':productId' => $productId, ':storeId' => $selectedStoreId]);
        $message = "Inventory updated successfully!";
    } catch (PDOException $e) {
        $message = "Failed to update inventory: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Inventory</title>
    <link href="minimal-table.css" rel="stylesheet" type="text/css">
</head>
<body>
    <h1>Edit Inventory Item</h1>
    <?php if (!empty($message)) echo "<p>$message</p>"; ?>

    <form action="editStoreInv.php" method="post">
        <label for="storeId">Select Store:</label>
        <select name="storeId" id="storeId" onchange="this.form.submit()">
            <?php foreach ($stores as $store): ?>
                <option value="<?= $store['id'] ?>" <?= $store['id'] == $selectedStoreId ? 'selected' : '' ?>>
                    <?= htmlspecialchars($store['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>

    <table>
        <tr>
            <th>Product Name</th>
            <th>Quantity</th>
            <th>Actions</th>
        </tr>
        <?php if (!empty($inventoryItems)): ?>
            <?php foreach ($inventoryItems as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['productName']) ?></td>
                    <td>
                        <form action="editStoreInv.php" method="post">
                            <input type="hidden" name="productId" value="<?= $item['productId'] ?>">
                            <input type="hidden" name="storeId" value="<?= $selectedStoreId ?>">
                            <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="0">
                            <button type="submit" name="update">Update</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="3">No inventory found for this store.</td>
            </tr>
        <?php endif; ?>
    </table>
</body>
</html>
