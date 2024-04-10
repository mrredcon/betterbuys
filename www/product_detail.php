<!DOCTYPE html>
<html>
<head>
</head>

<body>
	<a href="index.php">Back to homepage</a>

	<!--DB Logic start-->
	<?php
		$pdo = require_once 'connect.php';

		/* ** Uncomment to temporarily seed database with a single record if this is your first time executing this page.
		   
			** Some variables are hardcoded for the sake of displaying data until homepage data can be retrieved.

		$pdo->exec("INSERT INTO Store VALUES ( 2, 1, '17414 La Cantera, San Antonio, TX 78257', 29.6056067, -98.5986546, 0, 'Best Buy Rim', 0);");
		echo "Store created successfully.";
		
		$pdo->exec("INSERT INTO Inventory VALUES ( 1, 1, 10);");
		echo "Inventory created successfully.";

		$pdo->exec("INSERT INTO ProductImage VALUES ( 1, 'images\gudetama.jpg', 1, 0);");
		echo "ProductImage created successfully.";
		*/

		$product_id = '1'; //filter_input(INPUT_POST, 'product_id');
		
		# TODO: Compute store for store id
		$store_id = '1'; //$_POST['store_id'];
		
		// Retrieve product record from Product and ProductImage
		$sql = 'SELECT * FROM Product p INNER JOIN ProductImage pi ON p.id = pi.productId WHERE p.id = ' . $product_id . ';';

		$statement = $pdo->query($sql);
	
		$product = $statement->fetch(PDO::FETCH_ASSOC);

		$product_name = $product['name'];
		$product_description = $product['description'];
		$product_price = '300'; //$product['price'];
		$product_discount = '10'; //$product['discount'];
		$product_image = $product['filepath'];
		$product_priority = $product['priority'];
		$product_quantity = '';
	
		
		// Retrieve store record from Store and Inventory
		$sql = 'SELECT * FROM Store s INNER JOIN Inventory i ON s.id = i.storeId INNER JOIN Product p ON p.id = i.productId WHERE s.id = ' . $store_id . ';';

		$statement = $pdo->query($sql);
	
		$store = $statement->fetch(PDO::FETCH_ASSOC);
		
		$store_name = $store['name'];
		$store_pickup = $store['onlineOnly'];
		$store_inventory = 1;//$store['quantity'];
        // TODO: Retrieve store details to compute nearest store in separate .php file
		$nearest_store = '1'; // Retrieve id from computeNearestStore();

		# Begin display
		echo '<h1>' . $product_name . '</h1>';
		
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
			echo '<tr>';
				echo '<td><img src="' . $product_image . '"</td>';
		//}
	?>
	</div> <!--Product Image end-->

	<hr>

	<div> <!--Product Description start-->
		<?php
			echo '<table>';
				// Discount?
				if ($product_discount) {
					/* **Change for when discount is provided
					if ($discount_type == 'rate') {
						$discount_price = $product_price * $product_discount;
					}
					else if ($discount_type == 'flat') {
						$discount_price = $product_price - $product_discount;
					}
					echo '<td>'	. $discount_price . '</td>';
					echo '<tr>';
						echo '<td> Originally <s>' . $product_price . '</s></td>';
					echo '</tr>';
					*/
				}
				else {
					echo '<td>' . $product_price . '</td>';
				}
				echo '<tr>';
					echo '<th>Item Description</th>';
				echo '</tr>';
				echo '<tr>';
					echo '<td>' . $product_description . '</td>';
				echo '</tr>';
			echo '</table>';
		?>
	</div> <!--Product Description end-->
	
    <section> <!--Availability Selection start-->
        <div>
			<h3>Availability</h3>
            <?php 
                if(!isset($_POST['submit'])) {
					// Set selected product quantity
					$product_quantity = isset($_POST['amount']) ? $_POST['amount'] : '';
            ?>

            <form method="post" action="shopping_cart.php" id="availability-form" onsubmit="return validateForm(<?php echo $store_inventory; ?>)">
				<input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
						
				<div class="row">
                    <!--Delivery Option start-->
                    <div class="col-sm-6"> 
                        <div class="form-group" id="delivery">
                            <input type="radio" name="availability" value="delivery" id="delivery">
                            <label for="delivery">Delivery</label>
                        </div>
                    </div>
                    <!--Delivery Option end-->

					<?php
						// Check if pickup option is available
						if ($store_pickup == 0) {
					?>

                    <!--Pick Up Option start-->
                    <div class="col-sm-6">
                        <div class="form-group" id="pickup">
                            <input type="radio" name="availability" value="pickup" id="pickup">
                            <label for="pickup">Pick Up</label>
                        </div>
                    </div>
                    <!--Pick Up Option end-->
                </div>

				<div>
					<?php
						}
						echo '<table>';
							// $store_name is currently set to product name since both Product and Store have 'name' attributes
							echo '<th>' . $store_name . '</th>';
							echo '<tr>';
							echo '<td>Only ' . $store_inventory . ' left</td>';
							echo '</tr>';
						echo '</table>';
					?>
				</div>

				<div>
					<label for="amount">Enter an amount</label>
					<input type="text" id="amount" name="amount">
				</div>
				<!--Validate amount inputted-->
				<script>
					function validateForm(max) {
						var numberInput = document.getElementById("amount").value;
						var number = parseInt(numberInput);

						if (isNaN(number)) {
							alert('Please enter an integer number for desired amount.');
							return false;
						} 
						else if (number < 0) {
							alert('Cannot enter a negative number.'); 
							return false;
						}
						else if (number > max) {
							alert('Please enter an amount less than or equal to available stock.');
							return false;
						}
						return true;
					}
				</script>
				<!--End validation-->
				
                <div class="row">
                    <div class="col-sm-6">
                        <input type="submit" name="submit" value="Add to cart">
                    </div>
                </div>
            </form>

            <?php
				}
                else {
                    echo $product_quantity . $_POST['product_name'] . 'successfully added to shopping cart!';
				}
			}
            ?>
    </section> <!--Availability Selection end-->
</body>
</html>