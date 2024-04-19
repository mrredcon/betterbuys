<?php
$RESULTS_PER_PAGE = 20;
$page = filter_input(INPUT_GET, 'page', FILTER_DEFAULT, array("options" => array("default" => 1)));
$offset = ($page - 1) * $RESULTS_PER_PAGE;

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


// Get total count
$sql = 'SELECT COUNT(id) FROM Product
	WHERE name LIKE CONCAT("%", :search_terms1, "%") OR description LIKE CONCAT("%", :search_terms2, "%")';

$statement = $pdo->prepare($sql);

$statement->execute([
	':search_terms1' => $search_terms,
	':search_terms2' => $search_terms,
]);

$count = $statement->fetchColumn();

// Retrieve matching products and their primary image but also limit the amount of results
// TODO: When a store selector is implemented, add a parameter to "inv.storeId = X"
$sql = 'SELECT p.id, p.name, p.description, p.price, p.discount, img.filepath AS imagePath, IFNULL(inv.quantity, 0) AS quantity
	FROM Product p
	LEFT JOIN ProductImage img ON p.id = img.productId AND img.priority = 0
	LEFT JOIN Inventory inv ON p.id = inv.productId AND inv.storeId = 1
	WHERE p.name LIKE CONCAT("%", :search_terms1, "%") OR p.description LIKE CONCAT("%", :search_terms2, "%")
	ORDER BY p.' . $sort_by . ' ' . $direction . '
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

	echo json_encode(array(
		"entriesPerPage" => (int)$RESULTS_PER_PAGE,
		"currentPage" => (int)$page,
		"total" => (int)$count,
		"data" => $products
	));
} else {
	// no products found, return error
	// header('Status: '.$httpStatusCode.' '.$httpStatusMsg);
	// 404
	header('Status: 404 Not Found');
}
?> 
