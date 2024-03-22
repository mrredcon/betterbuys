<?php
	$pdo = require_once 'connect.php';
	
	// insert a single category
	$sql = 'INSERT INTO Category (name) VALUES(:name)';

	$statement = $pdo->prepare($sql);
	
	$statement->execute([
		':name' => $_POST["category_name"],
	]);
	
	$category_id = $pdo->lastInsertId();
	
	echo 'The category id ' . $category_id . ' was inserted!';
?> 
