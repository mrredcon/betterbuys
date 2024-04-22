<?php
	session_start();

	$pdo = require_once 'connect.php';

	$is_logged_in=false;
	$username='';
	if(array_key_exists('login', $_SESSION)) {
	    $is_logged_in=true;
	    $username=$_SESSION['login'];
	}

	$is_admin=false;
        if(array_key_exists('is_admin', $_SESSION) && $_SESSION['is_admin']) {
	    $is_admin=true;
        }

	$product_id = filter_input(INPUT_GET, 'product_id');
	$store_id = '1';

	// Retrieve product record from Product and ProductImage
	$sql = "SELECT p.id, p.name, p.description, p.price, p.discount, pi.filepath, inv.quantity as quantity
		FROM Product p
		LEFT JOIN ProductImage pi ON p.id = pi.productId AND pi.priority = 0
		LEFT JOIN Inventory inv ON inv.productId = p.id AND inv.storeId = 1
		WHERE p.id = :product_id;";

	$statement = $pdo->prepare($sql);
	$statement->execute([':product_id' => $product_id]);

	$product = $statement->fetch(PDO::FETCH_ASSOC);

	$product_name = $product['name'];
	$product_description = $product['description'];
	$product_price = $product['price'];
	$product_discount = $product['discount'];

	// Product is not on sale.
	if ($product_discount == 0) {
		$product_discount = null;
	}

	$product_image = $product['filepath'];
	
	// Retrieve store record from Store
	$sql = "SELECT name, onlineOnly FROM Store WHERE id=:store_id";
	$statement = $pdo->prepare($sql);
	$statement->execute([':store_id' => $store_id]);

	$store = $statement->fetch(PDO::FETCH_ASSOC);
	
	$store_name = $store['name'];
	$store_pickup = $store['onlineOnly'];
	$store_inventory = $product['quantity'];
?>
<!DOCTYPE html>
<html>
<head>
	<link href="assets/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
	<nav class="navbar navbar-expand-lg bg-body-tertiary">
		<div class="container-fluid">
			<a class="navbar-brand" href="/" id="btnLogo">Better Buys</a>
			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>

			<div class="collapse navbar-collapse" id="navbarSupportedContent">
				<ul class="navbar-nav me-auto mb-2 mb-lg-0">
					<li class="nav-item"><a class="nav-link" aria-current="page" href="/" id="btnHome">Home</a></li>
					<li class="nav-item"><a class="nav-link" href="shopping_cart.php">Shopping Cart</a></li>

					<?php
						if ($is_logged_in) {
							if ($is_admin) {
								echo '<li class="nav-item"><a class="nav-link text-danger" href="admin.php">Admin panel</a></li>';
							} else {
								echo '<li class="nav-item"><a class="nav-link" href="profile.php">Profile</a></li>';
							}

							echo '<li class="nav-item"><a class="nav-link" href="logout.php">Log Out</a></li>';
						} else {
							echo '<li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>';
							echo '<li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>';
						}
					?>
				</ul>

				<?php
					if ($is_logged_in) {
						echo '<p class="my-2 me-2">You' . "'" . 're logged in as: ' . $_SESSION['login'] . '</p>';
					}
				?>
			</div>
		</div>
	</nav>

	<div class="container">
		<div class="row mt-3">
			<div class="col-6 d-flex justify-content-center">
				<?php
        				# TODO: Add carousel functionality if there are multiple images
        				# Note: Single image should have priority 0 default; Other, display image 0
						//foreach ($product_image as $image) {
						if ($product_image) {
							echo '<img class="w-100 h-auto p-3" src="' . $product_image . '">';
						} else {
							echo '<h1 class="text-center align-self-center">No image available!</h1>';
						}
				?>
			</div>

			<div class="col-6">
				<!--DB Logic start-->
				<?php
					# Begin display
					echo '<h1>' . $product_name . '</h1>';
	#
					# In stock?
					if ($store_inventory < 1) {
						echo '<h3 class="text-danger">Out of stock</h3>';
						echo '<h4>We\'re sorry, this item is currently unavailable.</h4>';
					} else {
						echo '<h3>In stock</h3>';
					}

					// Sale?
					if ($product_discount) {
						$discount_price = $product_price - $product_discount;

						echo '<h2 class="text-primary"><b>SALE: $' . $product_discount . ' off!</b></h2>';
						echo 'Originally <s>$' . $product_price . '</s><br>';
						
						echo '<h2>$' . number_format((float)$discount_price, 2, '.', '') . '</h2><br>';
					} else {
						echo '<h2><b>$' . $product_price . '</b></h2>';
					}

					echo 'Item Description:';
					echo '<div class="col-sm-6">' . $product_description . '</div>';
				?>
				<!--Product Description end-->

				<?php
					if ($store_inventory >= 1) {
				?>
						<h4 class="mt-5">Availability</h4>

						<div class="mb-1"><?= $store_inventory ?> left at <b><?= $store_name ?></b></div>

						<form method="post" action="shopping_cart.php" id="availability-form" class="row">
							<input type="hidden" name="product_id" value="<?= $product['id'] ?>">

							<!--Delivery Option start-->
							<div class="mb-3" id="delivery">
								<input type="radio" name="availability" value="delivery" id="delivery" required>
								<label for="delivery" class="me-3">Delivery</label>

								<?php
									// Check if pickup option is available
									if ($store_pickup == 0) {
								?>
										<input type="radio" name="availability" value="pickup" id="pickup">
										<label for="pickup">Pick Up</label>
								<?php
									}
								?>

							</div>
                    					<!--Delivery Option end-->


							<div class="mb-3">
								<label for="inputAmount" class="form-label">Desired product quantity:</label>
								<input type="number" class="form-control" id="inputAmount" name="amount" value="1" required>
							</div>

							<button type="submit" class="btn btn-primary mb-2" value="Add to cart" onclick="return validateForm(<?= $store_inventory ?>);">Add to cart</button>
						</form>
				<?php
					}
				?>

				
				<!--Validate amount inputted-->
				<script>
					function validateForm(max) {
						var numberInput = document.getElementById("inputAmount").value;
						var number = parseInt(numberInput);
						
						if (number <= 0) {
							alert('Please enter an amount greater than zero.');
							return false;
						} else if (number > max) {
							alert('Please enter an amount less than or equal to available stock.');
							return false;
						} else {
							document.getElementById("availability-form").submit();
							return true;
						}
					}
				</script>
				<!--End validation-->
			</div>
		</div>
	</div>
</body>
</html>
