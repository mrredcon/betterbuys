<!DOCTYPE html>
<html>
	<head>
		<link href="minimal-table.css" rel="stylesheet" type="text/css">
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

		<h1>Product images</h1>
		<form action="upload.php" method="post" enctype="multipart/form-data">
			Select image to upload:<br>
			<input type="file" name="fileToUpload" id="fileToUpload"><br>
			<br>
			<input type="submit" value="Upload Image" name="submit"><br>
		</form>

		<hr>
		
		<h1>Products</h1>

		<form action="product_insert.php" method="post">
			<label for="pname">Name:</label><br>
			<input type="text" id="pname" name="name"><br>

			<label for="pdesc">Description:</label><br>
			<input type="text" id="pdesc" name="description">

			<br>

			<input type="submit" value="Add product">

			<br>
			<br>
		</form>

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
						echo '<td>' . $product['id'] . '</td>';
						echo '<td>' . $product['name'] . '</td>';
						echo '<td>' . $product['description'] . '</td>';

						echo '<form method="post" action="product_edit.php">';
							echo '<input type="hidden" name="product_id" value="'. $product['id'].'">';
							echo '<td>' .'<input type="submit" value="Edit">'. '</td>';	
						echo '</form>';

						echo '<form method="post" action="product_delete.php">';
							echo '<input type="hidden" name="product_id" value="'. $product['id'].'">';
							echo '<td>' .'<input type="submit" value="Delete">'. '</td>';	
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

		<form action="category_insert.php" method="post">
			<label for="cname">Category name:</label><br>
			<input type="text" id="cname" name="category_name"><br>

			<label for="cparentid">Category parent id:</label><br>
			<input type="text" id="cparentid" name="category_parent_id"><br>

			<input type="submit" value="Add category">
		</form>

		<?php

		// SELECT
		// 	a.name,
		// 	a.id
		// FROM
		// 	Category a
		// 		LEFT JOIN
		// 	Category b ON a.parentCategory = b.id
		// ORDER BY a.name;
		//
		
		$sql  = 'SELECT a.name, a.id, b.name AS parentCategoryName ';
		$sql .= 'FROM Category a LEFT JOIN Category b ON a.parentCategory = b.id ';
		$sql .= 'ORDER BY a.id';

		// echo $sql;

		//$sql = 'SELECT id, name FROM Category';
		
		$statement = $pdo->query($sql);
		
		// get all categories
		$categories = $statement->fetchAll(PDO::FETCH_ASSOC);
		
		if ($categories) {
			echo "<table>";
				echo "<tr>";
					echo "<th>Id</th>";
					echo "<th>Name</th>";
					echo "<th>Parent Category</th>";
				echo "</tr>";

				// show the categories as a table
				foreach ($categories as $category) {
					$parentCategoryName = $category['parentCategoryName'];

					if ($parentCategoryName == null) {
						$parentCategoryName = 'N/A';
					}
						

					echo "<tr>";
						echo "<td>" . $category['id'] . '</td>';
						echo "<td>" . $category['name'] . '</td>';
						echo "<td>" . $parentCategoryName . '</td>';

						echo '<form method="post" action="category_edit.php">';
							echo '<input type="hidden" name="category_id" value="'. $category['id'].'">';
							echo '<td>' .'<input type="submit" value="Edit">'. '</td>';	
						echo '</form>';

						echo '<form method="post" action="category_delete.php">';
							echo '<input type="hidden" name="category_id" value="'. $category['id'].'">';
							echo '<td>' .'<input type="submit" value="Delete">'. '</td>';	
						echo '</form>';
					echo "</tr>";
				}
			echo "</table>";
		} else {
			echo "No categories found!";
		}
		?> 



	</body>
</html>
