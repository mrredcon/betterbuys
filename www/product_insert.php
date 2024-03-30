<?php
	$pdo = require_once 'connect.php';
	
	// insert a single product
	$sql = 'INSERT INTO Product (name, description, price, quantity) VALUES(:name, :desc, :price, :quantity)';
	
	$statement = $pdo->prepare($sql);
	
	$statement->execute([
		':name' => $_POST["name"],
		':desc' => $_POST["description"],
		':price' => $_POST["price"],
		':quantity' => $_POST["quantity"]
	]);
	
	$product_id = $pdo->lastInsertId();
	
	echo 'The product id ' . $product_id . ' was inserted!';
?> 
