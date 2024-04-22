<?php
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
    return "No store selected.";
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
            header('Location: admin.php?page=stores');
            exit;
        } else {
            $errorInfo = $stmt->errorInfo();
            return "Failed to update store. Error: " . $errorInfo[2];
        } 
    }
}

$clean_name = htmlspecialchars($store['name']) ?? '';
$clean_address = htmlspecialchars($store['physicalAddress']) ?? '';
$clean_latitude = htmlspecialchars($store['latitude']) ?? '';
$clean_longitude = htmlspecialchars($store['longitude']) ?? '';
$clean_storenum = htmlspecialchars($store['storeNumber']) ?? '';
$no_selected = ($store['onlineOnly'] ?? 0) == 0 ? 'selected' : '';
$yes_selected = ($store['onlineOnly'] ?? 0) == 1 ? 'selected' : '';

$html = <<<EOD
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
	<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-0 border-bottom">
		<h1 class="h2">Edit Store</h1>
	</div>

	<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
		<form id="editForm" method="post">
        		<input type="hidden" name="store_id" value="$store_id">

			<div class="mb-3">
				<label for="inputStoreName" class="form-label">Name</label>
				<input type="text" class="form-control" id="inputStoreName" name="name" value="$clean_name">
			</div>
	
			<div class="mb-3">
				<label for="inputStoreAddress" class="form-label">Physical Address</label>
				<input type="text" class="form-control" id="inputStoreAddress" name="physicalAddress" value="$clean_address">
			</div>
	
			<div class="mb-3">
				<label for="inputStoreLatitude" class="form-label">Latitude</label>
				<input type="text" class="form-control" id="inputStoreLatitude" name="latitude" value="$clean_latitude">
			</div>
	
			<div class="mb-3">
				<label for="inputStoreLongitude" class="form-label">Longitude</label>
				<input type="text" class="form-control" id="inputStoreLongitude" name="longitude" value="$clean_longitude">
			</div>
	
			<div class="mb-3">
				<label for="selectStoreOnlineOnly" class="form-label">Online Only</label>
				<select class="form-select" id="selectStoreOnlineOnly" name="onlineOnly">
            				<option value="0" $no_selected >No</option>
            				<option value="1" $yes_selected >Yes</option>
				</select>
			</div>
	
			<div class="mb-3">
				<label for="inputStoreNumber" class="form-label">Store Number</label>
				<input type="text" class="form-control" id="inputStoreNumber" name="storeNumber" value="$clean_storenum">
			</div>
	
			<button type="submit" class="btn btn-primary mb-2" value="Update Store">Update Store</button>
		</form>
	</div>
</main>
EOD;

return $html;
?>
