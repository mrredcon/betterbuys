<!DOCTYPE html>

<html>
<head>
</head>
<body>
<a href="admin.php">Admin page</a>
	<?php
		$pdo = require_once 'connect.php';
		$product_id = filter_input(INPUT_POST, 'product_id');

		// retrieve a single product
		$sql = 'SELECT name, description, price, discount, quantity FROM Product WHERE id=' . $product_id . ';';
		
		$statement = $pdo->query($sql);
		
		//get a product
		$product = $statement->fetch(PDO::FETCH_ASSOC);

		$user_wants_to_save = filter_input(INPUT_POST, 'user_wants_to_save');

		$new_product_name = filter_input(INPUT_POST, 'new_product_name');
		$new_product_desc = filter_input(INPUT_POST, 'new_product_desc');
		$new_product_price = filter_input(INPUT_POST, 'new_product_price');
		$new_product_discount = filter_input(INPUT_POST, 'new_product_discount');
		$new_product_quantity = filter_input(INPUT_POST, 'new_product_quantity');

		$product_display_name = isset($new_product_name) ? $new_product_name : $product['name'];
		$product_display_desc = isset($new_product_desc) ? $new_product_desc : $product['description'];
		$product_display_price = isset($new_product_price) ? $new_product_price : $product['price'];
		$product_display_discount = isset($new_product_discount) ? $new_product_discount : $product['discount'];
		$product_display_quantity = isset($new_product_quantity) ? $new_product_quantity : $product['quantity'];
	?>
	
	<form action="product_edit.php" method="post">
		<label for="textProductName">Name:</label><br>
		<input type="text" id="textProductName" name="new_product_name" <?php echo 'value="' . $product_display_name . '"'; ?> >
		<br>
	
		<label for="textProductDescription">Description:</label><br>
		<input type="text" id="textProductDescription" name="new_product_desc" <?php echo 'value="' . $product_display_desc . '"'; ?> >
		<br>

		<label for="textProductPrice">Price:</label><br>
		<input type="text" id="textProductPrice" name="new_product_price" <?php echo 'value="' . $product_display_price . '"'; ?> >
		<br>

		<label for="textProductDiscount">Discount:</label><br>
		-&nbsp;<input type="text" id="textProductDiscount" name="new_product_discount" <?php echo 'value="' . $product_display_discount . '"'; ?> > (will be subtracted from the base price)
		<br>

		<label for="textProductQuantity">Quantity in stock:</label><br>
		<input type="text" id="textProductQuantity" name="new_product_quantity" <?php echo 'value="' . $product_display_quantity . '"'; ?> >
		<br>
		<br>

		<input type="hidden" name="product_id" <?php echo 'value="' . $product_id . '"'; ?> >
		<input type="submit" name="user_wants_to_save" value="Commit changes to database">
	</form>

<?php
		if($_SERVER['REQUEST_METHOD'] == "POST")
		{
			if (isset($user_wants_to_save)) {
				$discount_error = false;

				if ($new_product_discount == null || strlen($new_product_discount) == 0) {
					// Product is not on sale
					$new_product_discount = null;
				 	echo '<p>Discount removed.</p>';
				} else {
					// Product may or may not be on sale, let's double check
					
					// Check if user set the discount to 0
					if (bccomp($new_product_discount, 0) == 0) {
						// If the discount is 0, the product is not on sale.
						$new_product_discount = null;
				 		echo '<p>Discount removed.</p>';
					} else {
						// But if it is on sale, let's make sure the user put in something reasonable

						// Do base_price - discount
						// if result is 0 or negative, error out
						// Otherwise, we are good to go, go ahead and set discount in the DB to the difference
						$difference = bcsub($new_product_price, $new_product_discount);

						// Discount would make the product's new price either free or negative, error out
						if (bccomp($difference, "0") <= 0) {
							$discount_error = true;
						} else {
							// Base price - discount was greater than $0.00, we are good
							echo '<h1>Effective price: ' . $difference . '</h1>';
						}
					}
				}

				if ($discount_error) {
					echo '<h1>Given discount was too high.  Discount cannot be greater than or equal to the base price.</h1>';
				} else if (strlen($new_product_name) == 0) {
					echo '<h1>Product name cannot be blank.</h1>';
				} else {
					$sql = 'UPDATE Product SET name = :name, description = :desc, price = :price, discount = :discount, quantity = :quantity WHERE id=:id';

					$statement = $pdo->prepare($sql);


					$statement->execute([
						':name' => $new_product_name,
						':desc' => $new_product_desc,
						':price' => $new_product_price,
						':discount' => $new_product_discount,
						':quantity' => $new_product_quantity,
						':id' => $product_id
					]);

					echo 'The product id ' . $product_id . ' was edited!';
				}
			}


		}
?>
</body>
</html>

