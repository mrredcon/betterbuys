<?php

$PRODUCTS_TO_GENERATE = 200;

$pdo = require_once 'connect.php';

$words = file("/words.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
if (!$words) {
	die('Wordlist missing. Make sure it exists as a file with each word separated by newlines placed at /words.txt');
}
$words_last_idx = sizeof($words) - 1;

for ($i = 0; $i < $PRODUCTS_TO_GENERATE; $i++) {
	$name = $words[rand(0, $words_last_idx)] . ' ' . $words[rand(0, $words_last_idx)] . ' ' . $words[rand(0, $words_last_idx)];
	$description = $words[rand(0, $words_last_idx)] . ' ' . $words[rand(0, $words_last_idx)] . ' ' . $words[rand(0, $words_last_idx)] . ' ' . $words[rand(0, $words_last_idx)] . ' ' . $words[rand(0, $words_last_idx)];
	$price = rand(2, 1000);

	// If we get a nat 20, then set a discount.
	$dice = rand(1, 20);
	if ($dice === 20) {
		$discount = rand(1, $price);
	} else {
		$discount = null;
	}

	$quantity = rand(1, 1000);

	$sql = 'INSERT INTO Product (name, description, price, discount, quantity) VALUES(:name, :desc, :price, :discount, :quantity)';
	$statement = $pdo->prepare($sql);

	$statement->execute([
		':name' => $name,
		':desc' => $description,
		':price' => $price,
		':discount' => $discount,
		':quantity' => $quantity
	]);

	$product_id = $pdo->lastInsertId();
	echo 'The product id ' . $product_id . ' was inserted!<br>';
}
?>
