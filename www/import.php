<?php
	// id,name,description,price,discount,quantity,imagePath
	$pdo = require_once 'connect.php';
	$pdo->query('DELETE FROM TransactionItem');
	$pdo->query('DELETE FROM Transaction');
	$pdo->query('DELETE FROM Product');
	$pdo->query('DELETE FROM Inventory');
	$pdo->query('DELETE FROM Store');
	$pdo->query('DELETE FROM ProductImage');

	$pdo->query('INSERT INTO Store (id, name) VALUES (1, "The First Store");');

	$row = 1;
	if (($handle = fopen("/var/www/html/products.csv", "r")) !== FALSE) {
		while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

  			// `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  			// `name` VARCHAR(255) NOT NULL,
  			// `description` MEDIUMTEXT NULL,
  			// `price` DECIMAL(14,2) NOT NULL,
  			// `discount` DECIMAL(14,2) NULL,
  			// `quantity` INT UNSIGNED NOT NULL,
			//
			// echo "ID: {$data[0]}<br>";
			// echo "Name: {$data[1]}<br>";
			// echo "Description: {$data[2]}<br>";
			// echo "Price: {$data[3]}<br>";
			// echo "Discount: {$data[4]}<br>";
			// echo "Quantity: {$data[5]}<br>";
			//

			$pdo->query("INSERT INTO Product VALUES ({$data[0]}, '{$data[1]}', '{$data[2]}', {$data[3]}, {$data[4]}, {$data[5]});");

  			// `productId` INT UNSIGNED NOT NULL,
  			// `storeId` INT UNSIGNED NOT NULL,
  			// `quantity` INT NOT NULL,
			$pdo->query("INSERT INTO Inventory VALUES ({$data[0]}, 1, {$data[5]});");

  			// `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  			// `filepath` VARCHAR(255) NOT NULL,
  			// `productId` INT UNSIGNED NOT NULL,
  			// `priority` INT NULL,
			$pdo->query("INSERT INTO ProductImage (filepath, productId, priority) VALUES ('{$data[6]}', {$data[0]}, 0);");

			echo 'Done adding product id=' . $data[0] . '.';

			// $num = count($data);
			// echo "<p> $num fields in line $row: <br /></p>\n";
			// $row++;
			// for ($c=0; $c < $num; $c++) {
			// 	echo $data[$c] . "<br />\n";
			// }
		}

		fclose($handle);
	}    
?>
