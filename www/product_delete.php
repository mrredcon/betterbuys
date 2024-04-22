<?php
	$pdo = require_once 'connect.php';
	$product_id = $_POST['product_id'];
	
	// delete a single product
	$sql = 'DELETE FROM Product WHERE id=' . $product_id . ';';
	
	try {
		if ($pdo->exec($sql) == 1) {
			header('Location: admin.php?page=products');
		} else {
			echo '<p>Product id ' . $product_id . ' failed to be deleted.  (Does it exist?)</p>';
		}
	} catch(PDOException $e) {
		echo $e->getMessage();
	}
?>
