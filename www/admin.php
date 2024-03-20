<!DOCTYPE html>
<html>
	<head>
	</head>
	<body>
		<a href="/index.html">Back to home page</a>
		<hr>

		<form action="db_create.php" method="post">
			<input type="submit" value="Create database">
		</form>

		<hr>

		<form action="db_delete.php" method="post">
			<input type="submit" value="Delete database">
		</form>

		<hr>

		<form action="product_insert.php" method="post">
			<label for="pname">Name:</label><br>
			<input type="text" id="pname" name="name"><br>

			<label for="pdesc">Description:</label><br>
			<input type="text" id="pdesc" name="description">

			<input type="submit" value="Add product">
		</form>

		<hr>

		<form action="category_insert.php" method="post">
			<label for="cname">Category name:</label><br>
			<input type="text" id="cname" name="category_name"><br>

			<input type="submit" value="Add category">
		</form>

		<hr>
		<form action="upload.php" method="post" enctype="multipart/form-data">
			Select image to upload:<br>
			<input type="file" name="fileToUpload" id="fileToUpload"><br>
			<br>
			<input type="submit" value="Upload Image" name="submit"><br>
		</form>
		<hr>
		
		<h1>Products</h1>

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

						echo '<form method="post" action="product_edit.php">';
						echo '<input type="hidden" name="product_id" value="'. $product['id'].'">';
						echo '<td>' . $product['id'] . '</td>';
						echo '<td>' . $product['name'] . '</td>';
						echo '<td>' . $product['description'] . '</td>';
						echo '<td>' .'<input type="submit" value="edit">'. '</td>';	
						echo '</form>';
					
						echo "</tr>";
				}
			echo "</table>";
		} else {
			echo "No products found!";
		}
	?>
	</form>


		<hr>
		<h1>Categories</h1>
		<?php

		//$pdo = require 'connect.php';
		
		$sql = 'SELECT id, name FROM Category';
		
		$statement = $pdo->query($sql);
		
		// get all categories
		$categories = $statement->fetchAll(PDO::FETCH_ASSOC);
		
		if ($categories) {
			echo "<table>";
				echo "<tr>";
					echo "<th>Id</th>";
					echo "<th>Name</th>";
				echo "</tr>";

				// show the categories as a table
				foreach ($categories as $category) {
					echo "<tr>";
						echo "<td>" . $category['id'] . '</td>';
						echo "<td>" . $category['name'] . '</td>';
					echo "</tr>";
				}
			echo "</table>";
		} else {
			echo "No categories found!";
		}
		?> 



	</body>
</html>
