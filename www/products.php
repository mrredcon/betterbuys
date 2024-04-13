<?php

$RESULTS_PER_PAGE = 20;
$page = filter_input(INPUT_GET, 'page', FILTER_DEFAULT, array("options" => array("default" => 0)));
$offset = $page * $RESULTS_PER_PAGE;

$search_terms = filter_input(INPUT_GET, 'query');

$pdo = require_once 'connect.php';

$sql = 'SELECT a.id, a.name, a.description, a.price, b.filepath AS imagePath
	FROM Product a LEFT JOIN ProductImage b ON a.id = b.productId AND b.priority = 0
	WHERE a.name LIKE "%' . $search_terms . '%" OR a.description LIKE "%' . $search_terms . '%"
	LIMIT ' . $offset . ', ' . $RESULTS_PER_PAGE;

$statement = $pdo->query($sql);
 
// get all products
$products = $statement->fetchAll(PDO::FETCH_ASSOC);

if ($products) {
	// we found products
	header('Content-Type: application/json; charset=utf-8');
	echo json_encode($products);
} else {
	// no products found, return error
	// header('Status: '.$httpStatusCode.' '.$httpStatusMsg);
	// 404
	header('Status: 404 Not Found');
}
?> 
