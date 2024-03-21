<?php
	$pdo = require_once 'connect.php';
	
	// insert a single product
	$sql = 'INSERT INTO Category (name, parentCategory) VALUES(:name, :parentId)';

	$parent_id = $_POST["category_parent_id"];
	if (strlen($parent_id) == 0)
	{
		$parent_id = null;
	}
	
	$statement = $pdo->prepare($sql);
	
	$statement->execute([
		':name' => $_POST["category_name"],
		':parentId' => $parent_id
	]);
	
	$category_id = $pdo->lastInsertId();
	
	echo 'The category id ' . $category_id . ' was inserted!';
?> 
