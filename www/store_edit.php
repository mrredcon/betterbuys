<?php
session_start(); // Start session at the beginning of your script
ob_start(); // Start output buffering

// Include your database connection and other necessary setup
$pdo = require_once 'connect.php';

// Check and retrieve store details
$store_id = filter_input(INPUT_POST, 'store_id', FILTER_SANITIZE_NUMBER_INT);

if ($store_id) {
    $sql = "SELECT * FROM Store WHERE id = :store_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['store_id' => $store_id]);
    $store = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    echo "No store selected.";
    exit;
}

// Handle form submission to update the store
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $physicalAddress = $_POST['physicalAddress'] ?? '';
    $latitude = filter_input(INPUT_POST, 'latitude', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $longitude = filter_input(INPUT_POST, 'longitude', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $onlineOnly = filter_input(INPUT_POST, 'onlineOnly', FILTER_SANITIZE_NUMBER_INT);
    $storeNumber = $_POST['storeNumber'] ?? '';

    if (!empty($name) && !empty($physicalAddress)) {
        $sql = "UPDATE Store SET name = :name, physicalAddress = :physicalAddress, latitude = :latitude, longitude = :longitude, onlineOnly = :onlineOnly, storeNumber = :storeNumber WHERE id = :store_id";
        $stmt = $pdo->prepare($sql);
        $success = $stmt->execute([
            ':name' => $name,
            ':physicalAddress' => $physicalAddress,
            ':latitude' => $latitude,
            ':longitude' => $longitude,
            ':onlineOnly' => $onlineOnly,
            ':storeNumber' => $storeNumber,
            ':store_id' => $store_id
        ]);

        if ($success) {
            $_SESSION['updateSuccess'] = 'Store updated successfully!';
            header('Location: admin.php');
            exit;
        } else {
            echo "Failed to update store.";
            $errorInfo = $stmt->errorInfo();
            echo "Error: " . $errorInfo[2];
        } 
    }
}
?>


<!DOCTYPE html>
<html>

<hr>
<h1>Stores</h1><hr><br>

<head>
    <h2>Edit Store</h2>
</head>
<body>
    <a href="admin.php">Admin page</a>
    <h1>Edit Store</h1>
    <form id="editForm" action="store_edit.php" method="post">
        <input type="hidden" name="store_id" value="<?php echo htmlspecialchars($store_id); ?>">
        <label>Name:</label><br>
        <input type="text" name="name" value="<?php echo htmlspecialchars($store['name'] ?? ''); ?>"><br>
        <label>Physical Address:</label><br>
        <input type="text" name="physicalAddress" value="<?php echo htmlspecialchars($store['physicalAddress'] ?? ''); ?>"><br>
        <label>Latitude:</label><br>
        <input type="text" name="latitude" value="<?php echo htmlspecialchars($store['latitude'] ?? ''); ?>"><br>
        <label>Longitude:</label><br>
        <input type="text" name="longitude" value="<?php echo htmlspecialchars($store['longitude'] ?? ''); ?>"><br>
        <label>Online Only:</label><br>
        <select name="onlineOnly">
            <option value="0" <?php echo ($store['onlineOnly'] ?? 0) == 0 ? 'selected' : ''; ?>>No</option>
            <option value="1" <?php echo ($store['onlineOnly'] ?? 0) == 1 ? 'selected' : ''; ?>>Yes</option>
        </select><br>
        <label>Store Number:</label><br>
        <input type="text" name="storeNumber" value="<?php echo htmlspecialchars($store['storeNumber'] ?? ''); ?>"><br>
        <input type="submit" value="Update Store">
    </form>

    <script>
        document.getElementById('editForm').addEventListener('submit', function() {
            // Optionally we can add a confirmation here if needed
            if (confirm('Are you sure you want to update this store?')) {
                sessionStorage.setItem('editSuccess', 'true');
            }
        });
    </script>
</body>
</html>


