<?php
	$pdo = require_once 'connect.php';
	
	// insert a single product
	$sql = 'INSERT INTO Product (Name, Description) VALUES(:name, :desc)';
	
	$statement = $pdo->prepare($sql);
	
	$statement->execute([
		':name' => $_POST["name"],
		':desc' => $_POST["description"]
	]);
	
	$product_id = $pdo->lastInsertId();
	
	echo 'The product id ' . $product_id . ' was inserted!';
?> 
