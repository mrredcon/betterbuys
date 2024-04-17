<?php
$RESULTS_PER_PAGE = 20;
$page = filter_input(INPUT_GET, 'page', FILTER_DEFAULT, array("options" => array("default" => 0)));
$offset = $page * $RESULTS_PER_PAGE;

$search_terms = filter_input(INPUT_GET, 'query', FILTER_DEFAULT, array("options" => array("default" => '')));

$sort_by = filter_input(INPUT_GET, 'sortBy', FILTER_DEFAULT, array('options' => array('default' => 'name')));

// Sort by name by default if we're given invalid input
if (strcmp($sort_by, "name") != 0 && strcmp($sort_by, "price") != 0) {
	$sort_by = "name";
}

$direction = filter_input(INPUT_GET, 'direction', FILTER_DEFAULT, array('options' => array('default' => 'ASC')));

// Make ascending the default value for sort direction
if (strcmp($direction, "asc") != 0 && strcmp($direction, "desc") != 0) {
	$direction = "asc";
}

$pdo = require_once 'connect.php';

$sql = 'SELECT a.id, a.name, a.description, a.price, b.filepath AS imagePath
	FROM Product a LEFT JOIN ProductImage b ON a.id = b.productId AND b.priority = 0
	WHERE a.name LIKE CONCAT("%", :search_terms1, "%") OR a.description LIKE CONCAT("%", :search_terms2, "%")
	ORDER BY a.' . $sort_by . ' ' . $direction . '
	LIMIT ' . $offset . ', ' . $RESULTS_PER_PAGE;

$statement = $pdo->prepare($sql);

$statement->execute([
	':search_terms1' => $search_terms,
	':search_terms2' => $search_terms,
]);

// get all products
$products = $statement->fetchAll(PDO::FETCH_ASSOC);

if ($products) {
	// we found products
	header('Content-Type: application/json; charset=utf-8');
	$count = sizeof($products);

	echo json_encode(array(
		"entriesPerPage" => $RESULTS_PER_PAGE,
		"currentPage" => $page,
		"total" => $count,
		"data" => $products
	));
} else {
	// no products found, return error
	// header('Status: '.$httpStatusCode.' '.$httpStatusMsg);
	// 404
	header('Status: 404 Not Found');
}
?> 
