<!DOCTYPE html>
<html>
	<head>
	</head>
	<body>
		<h1>Welcome to Better Buys!</h1>
		<a href="admin.php">Admin page</a>
		<hr>
		<?php

		$pdo = require 'connect.php';
		
		$sql = 'SELECT id, name, description FROM Product';
		
		$statement = $pdo->query($sql);
		
		// get all publishers
		$products = $statement->fetchAll(PDO::FETCH_ASSOC);
		
		if ($products) {
			echo "<table>";
				echo "<tr>";
					echo "<th>Id</th>";
					echo "<th>Name</th>";
					echo "<th>Description</th>";
				echo "</tr>";

				// show the products as a table
				foreach ($products as $product) {
					echo "<tr>";
						echo "<td>" . $product['id'] . '</td>';
						echo "<td>" . $product['name'] . '</td>';
						echo "<td>" . $product['description'] . '</td>';
					echo "</tr>";
				}
			echo "</table>";
		} else {
			echo "No products found!";
		}
		?> 
	</body>
</html>
