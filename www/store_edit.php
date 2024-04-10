<?php

ob_start();

$pdo = require_once 'connect.php';
if (!$pdo) {
    die("Failed to connect to the database.");
}
ini_set('display_errors', 1);
error_reporting(E_ALL);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";


    // Handle the form submission to update the store
    $sql = "UPDATE Store SET name = :name, physicalAddress = :physicalAddress, latitude = :latitude, longitude = :longitude, onlineOnly = :onlineOnly, storeNumber = :storeNumber WHERE id = :id";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':name' => $_POST['name'],
        ':physicalAddress' => $_POST['physicalAddress'],
        ':latitude' => $_POST['latitude'],
        ':longitude' => $_POST['longitude'],
        ':onlineOnly' => $_POST['onlineOnly'],
        ':storeNumber' => $_POST['storeNumber'],
        ':id' => $_POST['id']
    ]);
    
    header('Location: admin.php');
    exit;
} else {

        $stmt = $pdo->prepare("SELECT * FROM Store WHERE id = :id");
        $stmt->execute([':id' => $_GET['id']]);
        $store = $stmt->fetch(PDO::FETCH_ASSOC);

        echo "<pre>"; // This will make the output more readable
        var_dump($store);
        echo "</pre>";


    /*
    Display the edit form with existing data
    $sql = "SELECT * FROM Store WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $_GET['id']]);
    $store = $stmt->fetch(PDO::FETCH_ASSOC);
    */ 
    if (!$store) {
        echo "Store not found!";
        exit;
    }

}
?>

<?php if (!empty($store)): ?>
<h2>Edit Store:</h2>
		<form action="store_edit.php" method="post">
			<label for="snumber">Store Number:</label><br>
			<input type="text" id="snumber" name="storeNumber"><br>
			
			<label for="sname">Name:</label><br>
			<input type="text" id="sname" name="name"><br>

			<label for="saddress">Physical Address:</label><br>
			<input type="text" id="saddress" name="physicalAddress"><br>

			<label for="slatitude">Latitude:</label><br>
			<input type="text" id="slatitude" name="latitude"><br>

			<label for="slongitude">Longitude:</label><br>
			<input type="text" id="slongitude" name="longitude"><br>

			<label for="sonlineOnly">Online Only:</label><br>
			<select id="sonlineOnly" name="onlineOnly">
				<option value="0">No</option>
				<option value="1">Yes</option>
			</select><br>

			<input type="submit" value="Fix Typos Button">

            </form>

 <?php else: ?>
    <p>Store not found!</p> 
<?php endif; 
    ob_end_clean();?>