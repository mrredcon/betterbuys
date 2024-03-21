<!DOCTYPE html>

<html>
<head>
</head>
<body>
<a href="admin.php">Admin page</a>


	<?php
		$pdo = require_once 'connect.php';
		$product_id = $_POST['product_id'];

		// retrieve a single product
		$sql = 'SELECT name, description FROM Product WHERE id=' . $product_id . ';';
		
		$statement = $pdo->query($sql);
		
		//get a product
		$product = $statement->fetch(PDO::FETCH_ASSOC);

		$user_wants_to_save = filter_input(INPUT_POST, 'user_wants_to_save');

		$new_product_name = filter_input(INPUT_POST, 'new_product_name');
		$new_product_desc = filter_input(INPUT_POST, 'new_product_desc');

		$product_display_name = '';
		if (isset($new_product_name))
		{
			$product_display_name = $new_product_name;
		}
		else
		{
			$product_display_name = $product["name"];
		}

		$product_display_desc = '';
		if (isset($new_product_desc))
		{
			$product_display_desc = $new_product_desc;
		}
		else
		{
			$product_display_desc = $product["description"];
		}

	?>
	
	<form action="product_edit.php" method="post">
		<label for="textProductName">Name:</label><br>
		<input type="text" id="textProductName" name="new_product_name" <?php echo 'value="' . $product_display_name . '"'; ?> >
	
		<br>
	
		<label for="textProductDescription">Description:</label><br>
		<input type="text" id="textProductDescription" name="new_product_desc" <?php echo 'value="' . $product_display_desc . '"'; ?> >
		<input type="hidden" name="product_id" <?php echo 'value="' . $product_id . '"'; ?> >
		<input type="hidden" name="user_wants_to_save" value="true">

		<input type="submit" value="Commit changes to database">
	</form>

	<?php
		if($_SERVER['REQUEST_METHOD'] == "POST")
		{

			if (isset($user_wants_to_save) and strlen($new_product_name) == 0)
			{
				echo '<h1>Product name cannot be blank.</h1>';
			}
			else if (isset($user_wants_to_save))
			{
				// update the product's name
				$sql = 'UPDATE Product SET name = :name, description = :desc WHERE id=:id';
				
				$statement = $pdo->prepare($sql);
				
				$statement->execute([
					':name' => $new_product_name,
					':desc' => $new_product_desc,
					':id' => $product_id
				]);
				
				echo 'The product id ' . $product_id . ' was edited!';
			}
		}
	?>
</body>
</html>

