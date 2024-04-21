<!DOCTYPE html>
<html>
<head>
	<link href="assets/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
	<a href="index.php">Back to homepage</a>

	<!--DB Logic start-->
	<?php
		$pdo = require_once 'connect.php';

		/* 
		** Uncomment to temporarily seed database with a single record if this is your first time executing this page.
		   
		** Some variables are hardcoded for the sake of displaying data until homepage data can be retrieved.

		$pdo->exec("INSERT INTO Store VALUES ( 1, 1, '17414 La Cantera, San Antonio, TX 78257', 29.6056067, -98.5986546, 0, 'Best Buy Rim', 0);");
		echo "Store created successfully.";
		
		$pdo->exec("INSERT INTO Inventory VALUES ( 1, 1, 10);");
		echo "Inventory created successfully.";
		*/

		$product_id = '1';//filter_input(INPUT_POST, 'product_id');
		$store_id = '1';

		// Retrieve product record from Product and ProductImage
		$sql = 'SELECT * FROM Product p INNER JOIN ProductImage pi ON p.id = pi.productId WHERE p.id = ' . $product_id . ';';

		$statement = $pdo->query($sql);
	
		$product = $statement->fetch(PDO::FETCH_ASSOC);

		$product_name = $product['name'];
		$product_description = $product['description'];
		$product_price = $product['price'];
		$product_discount = $product['discount'];
		$product_image = $product['filepath'];
		$product_priority = $product['priority'];
		$product_quantity = '';
		
		// Retrieve store record from Store and Inventory
		$sql = 'SELECT s.name, onlineOnly, storeId, productId, i.quantity FROM Store s INNER JOIN Inventory i ON s.id = i.storeId INNER JOIN Product p ON p.id = i.productId WHERE s.id = ' . $store_id . ';';

		$statement = $pdo->query($sql);
	
		$store = $statement->fetch(PDO::FETCH_ASSOC);
		
		$store_name = $store['name'];
		$store_pickup = $store['onlineOnly'];
		$store_inventory = $store['quantity'];

		// TEMP: Check and set discount
		if ($product_discount) {
			$discount_type = 'percentage';
		}

		# Begin display
		echo '<h1>' . $product_name . '</h1>';

		echo '<div class="row">';
		echo '<h2>SALE: ' . $product_discount . ' off!</h2>';
		echo '</div>';
		
		# In stock?
		if ($store_inventory < 1) {
			echo '<h3>Not in stock</h3>';
			echo '<h4>We\'re sorry, this item is currently not in stock</h4>';
		}
		else {
			echo '<h3>In stock</h3>';
		
	?>
	<!--DB Logic end-->

	<hr>

	<div> <!--Product Image start-->
	<?php
        # TODO: Add carousel functionality if there are multiple images
        # Note: Single image should have priority 0 default; Other, display image 0
		//foreach ($product_image as $image) {
			echo '<td><img src="' . $product_image . '"</td>';
		//}
	?>
	</div> <!--Product Image end-->

	<hr>

	<div> <!--Product Description start-->
		<?php
				// Sale?
				if ($product_discount && $product_price >= $product_discount) {
					if ($discount_type == 'percentage') {
						$discount_price = $product_price * $product_discount;
						echo '<div class="row">';
						echo '<div class="col-sm-6">';
							echo '<h2>$'	. $discount_price . '</h2>';
						echo '</div>';
						
						echo '<div class="col-sm-6">';
							echo 'Originally <s>$' . $product_price . '</s>';
						echo '</div>';
						echo '</div>';
					}
					else if ($discount_type == 'flat') {
						$discount_price = $product_price - $product_discount;
						echo '<div class="row">';
							echo '<h2>$' . $discount_price . '</h2>';
						echo '</div>';
						echo '<div class="row">';
							echo 'Originally <s>$' . $product_price . '</s>';
						echo '</div>';
					}
				}
				else {
					echo '<div class="row">';
						echo '<b>$' . $product_price . '</b>';
					echo '</div">';
				}
				echo '<div class="row">';
					echo 'Item Description';
				echo '</div>';
				echo '<div class="row">';
					echo '<div class="col-sm-6">' . $product_description . '</div>';
				echo '</div>';
		?>
	</div> <!--Product Description end-->
	
    <section> <!--Availability Selection start-->
        <div>
			<h3>Availability</h3>
						
			<form method="post" action="shopping_cart.php" id="availability-form">	

				<!--Delivery Option start-->
				<div class="row">
					<div class="form-group" id="delivery">
						<input type="radio" name="availability" value="delivery" id="delivery" required>
						<label for="delivery">Delivery</label>
					</div>
                    <!--Delivery Option end-->

					<?php
						// Check if pickup option is available
						if ($store_pickup == 0) {
							// Pick Up Option start
							echo '<div class="form-group" id="pickup">';
								echo '<input type="radio" name="availability" value="pickup" id="pickup">';
								echo '<label for="pickup">Pick Up</label>';
							echo '</div>';
							// Pick Up Option end
						}
					?>
                </div>

				<div>
					<?php
						echo '<table>';
							echo '<th>' . $store_name . '</th>';
							echo '<tr>';
							echo '<td>Only ' . $store_inventory . ' left</td>';
							echo '</tr>';
						echo '</table>';
					?>
				</div>
				
				<!--Validate amount inputted-->
				<script>
					function validateForm(max) {
						var numberInput = document.getElementById("amount").value;
						var number = parseInt(numberInput);
						
						if (number > max) {
							alert('Please enter an amount less than or equal to available stock.');
							return false;
						}
						else {
							document.getElementById("availability-form").submit();
							alert('Successfully added to shopping cart!');
							return true;
						}
					}
				</script>
				<!--End validation-->

				<!--Enter desired amount-->
				<div class="form-group">
					<label for="amount">Enter an amount: </label>
					<input type="number" id="amount" name="amount" min="0" required>
				</div>
				<!--Enter amount end-->

				<?php 
				// Set selected product quantity
				$product_quantity = isset($_POST['amount']) ? $_POST['amount'] : '';
            
					echo '<div class="row">';
						echo '<div class="form-group">';
						echo '<input type="hidden" name="product_id" value="' . $product['id'] . '">';
						echo '<input type="submit" value="Add to cart" onclick="return validateForm(' . $store_inventory . ');">';
						echo '</div>';
					echo '</div>';
				echo '</form>';
				}
            ?>

		</div>
    </section> <!--Availability Selection end-->
</body>
</html>