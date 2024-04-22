<?php
	session_start();

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

	$pdo = require 'connect.php';
	
	if(isset($_COOKIE['shoppingCart']) && sizeof(json_decode($_COOKIE['shoppingCart'], true)) === 0){
		setcookie('shoppingCart', 0, time() - 3600, '/');
		header("Refresh:0");
		exit;
	}
	
	$discountType = 0.0;
	$discountAmount = NULL;
	$dCode = NULL;
	if(filter_input(INPUT_POST, 'code')){
		$dCode = filter_input(INPUT_POST, 'code');
		
		$item_array = json_decode($_COOKIE['shoppingCart'], true);

		$sql = 'SELECT reductionType, flatReduction, multiplierReduction, startDate, expireDate, productId, usesRemaining, userId, enabled FROM DiscountCode WHERE code="' . $dCode . '";';
		
		$statement = $pdo->query($sql);
			
		$discount = $statement->fetch(PDO::FETCH_ASSOC);
		
		if($discount){
			$correctUser = !$discount['userId'] || $discount['userId'] == $_SESSION['user_id'];
			$correctProduct = !$discount['productId'] || in_array($discount['productId'], array_keys($item_array));
			$usesRemain = !$discount['usesRemaining'] || $discount['usesRemaining'] > 0;
			if($correctUser && $correctProduct && $usesRemain && $discount['expireDate'] > date("Y-m-d H:i:s") && $discount['enabled']){
				$discountType = $discount['reductionType'];
				if($discountType === 'Flat'){
					$discountAmount = $discount['flatReduction'];
				}
				else{
					$discountAmount = floatval($discount['multiplierReduction']);
				}
			}
			else{
				$dCode = 'ERROR';
			}
		}
		else{
			$dCode = 'ERROR';
		}
	}
	else{
		if(filter_input(INPUT_POST, 'remove')){
			$remove_id = filter_input(INPUT_POST, 'remove');
			$data = json_decode($_COOKIE['shoppingCart'], true);
			unset($data[$remove_id]);
			setcookie('shoppingCart', json_encode($data), time() + (86400 * 30), "/");
			header("Refresh:0");
			exit;
		}
		else if(filter_input(INPUT_POST, 'save')){
			$product_id = filter_input(INPUT_POST, 'save');
			$order_amount = filter_input(INPUT_POST, 'samount');
			if(!is_numeric($order_amount)){
				header("Location: shopping_cart.php?error=quantityNotAnInteger");
				exit;
			}
			
			$sql = 'SELECT quantity FROM Inventory WHERE productId=' . $product_id . ' AND storeId=1';
			
			$statement = $pdo->query($sql);
				
			$inventory = $statement->fetchColumn();
			
			$data = json_decode($_COOKIE['shoppingCart'], true);
			if($order_amount > $inventory){
				$order_amount = $inventory;
				$data[$product_id] = $order_amount;
				setcookie('shoppingCart', json_encode($data), time() + (86400 * 30), "/");
				header("Location: shopping_cart.php?error=quantityTooHigh");
				exit;
			}
			else if($order_amount == 0){
				unset($data[$product_id]);
			}
			else{
				$data[$product_id] = $order_amount;
			}
			setcookie('shoppingCart', json_encode($data), time() + (86400 * 30), "/");
			
			header("Refresh:0");
			exit;
		}
		else{
			$product_id = filter_input(INPUT_POST, 'product_id');
			$order_amount = filter_input(INPUT_POST, 'amount');
			if(!$order_amount || $order_amount < 1){
				$order_amount = 1;
			}
		
			if ($product_id){
				$sql = 'SELECT quantity FROM Inventory WHERE productId=' . $product_id . ' AND storeId=1';
			
				$statement = $pdo->query($sql);
				
				$inventory = $statement->fetchColumn();
			
				if(!isset($_COOKIE['shoppingCart'])){
					$data = array($product_id=>$order_amount);
					setcookie('shoppingCart', json_encode($data), time() + (86400 * 30), "/");
				}
				else{
					$data = json_decode($_COOKIE['shoppingCart'], true);
					if(in_array($product_id, array_keys($data))){
						if($data[$product_id]+$order_amount > $inventory){
							$order_amount = $inventory;
						}
						else{
							$order_amount = $data[$product_id]+$order_amount;
						}
						$data[$product_id] = $order_amount;
					}
					else{
						if($order_amount > $inventory){
							$order_amount = $inventory;
						}
						$data[$product_id] = $order_amount;
					}
					
					setcookie('shoppingCart', json_encode($data), time() + (86400 * 30), "/");
				}			
				header("Refresh:0");
				exit;
			}
		}
	
	}
?>

<!DOCTYPE html>
<html>
	<head>
    		<link href="assets/css/bootstrap.min.css" rel="stylesheet">
    		<link rel="stylesheet" href="assets/css/font-awesome.min.css">
		<title>Better Buys: Shopping Cart</title>
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
						<li class="nav-item"><a class="nav-link active" href="shopping_cart.php">Shopping Cart</a></li>

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
			<div class="row">
				<div class="col">
					<h1>Shopping Cart</h1>
				</div>
			</div>

			<hr>

			<div class="row">
				<div class="col">
					<?php
						if(isset($_COOKIE['shoppingCart'])){
						
						$item_array = json_decode($_COOKIE['shoppingCart'], true);

						$sql = 'SELECT id, name, description, price, discount FROM Product WHERE `id` IN (' . implode(',',  array_keys($item_array)) . ')';
						
						$statement = $pdo->query($sql);
						
						$products = $statement->fetchAll(PDO::FETCH_ASSOC);
						
						$total = 0.0;
						
						echo '<table class="table">';
							echo '<thead>';
								echo "<tr>";
									echo "<th>Name</th>";
									echo "<th>Quantity</th>";
									echo "<th>Price</th>";
									echo "<th>Remove?</th>";
									echo "<th>Edit quantity</th>";
								echo "</tr>";
							echo '</thead>';
						
							echo '<tbody>';
							foreach ($products as $product){
								echo "<tr>";
									echo '<td><a href="/product_detail.php?product_id=' . $product['id'] . '">' . $product['name'] . '</a></td>';
									echo '<td>' . $item_array[$product['id']] . '</td>';
									if($product['discount']){
										echo '<td><del>' . sprintf('%.2f', $product['price'] * $item_array[$product['id']]) . '</del> ' . sprintf('%.2f', ($product['price'] - $product['discount']) * $item_array[$product['id']]) . '</td>';
										$total += ($product['price'] - $product['discount']) * $item_array[$product['id']];
									}
									else{
										echo '<td>' . sprintf('%.2f', $product['price'] * $item_array[$product['id']]) . '</td>';
										$total += $product['price'] * $item_array[$product['id']];
									}
									?>
									<td>
										<form method="post">
											<input type="hidden" name="remove" value=<?php echo $product['id']?>>
											<input type="submit" value="Remove">
										</form>
									</td>
									<td>
										<form method="post">
											<input type="hidden" name="save" value=<?php echo $product['id']?>>
											<input type="submit" value="Save">
											<input type="text" name="samount">
										</form>
									</td>
									<?php
								echo "</tr>";
								
							} 
							echo '</tbody>';
						echo "</table>";
						
						$error = filter_input(INPUT_GET, 'error');
						if($error === 'quantityTooHigh'){
							echo '<span style="color: blue;">* Quantity Too High. Setting To Max.</span>';
						}
						else if($error === 'quantityNotAnInteger'){
							echo '<span style="color: red;">* Quantity Must Be Number.</span>';
						}
					?>
				</div>
			</div>
					
			<div class="row">
				<div class="col">
					<?php
						if(isset($_SESSION['user_id'])) {
					?>
							<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post" class="mb-3">
								<input type="text" id="dcode" name="code">
								<input type="submit" value="Enter Discount Code">
							</form>
					<?php
						} else {
							echo 'Please login to enter a discount code';
						}
					
						if($dCode === 'ERROR') {
							echo '<span style="color: red;">* Invalid Discount Code</span><br>';
							$dCode = NULL;
						}
					
						if($discountType === 'Flat') {
							echo '<h2>Subtotal: <del>$' . sprintf('%.2f', $total) . '</del> $' . sprintf('%.2f', $total - $discountAmount) . '</h2>';
							$total = $total - $discountAmount;
						}
						else if($discountType === 'Multiplier') {
							echo '<h2>Subtotal: <del>$' . sprintf('%.2f', $total) . '</del> $' . sprintf('%.2f', $total * (1 - $discountAmount)) . '</h2>';
							$total = $total * (1 - $discountAmount);
						} else {
							echo 'Subtotal: $' . sprintf('%.2f', $total) . '<br>';
						}
						
						echo 'Tax: $' . sprintf('%.2f', $total * .0825) . '<br>';
						echo 'Shipping Fee: $5.00<br><br>';
						echo '<h1>Total: $' . sprintf('%.2f', $total) + sprintf('%.2f', $total * .0825) + 5.00 . '</h1>';
						
						if(!isset($_SESSION['user_id'])) {
							echo 'Please login to place an order';
							exit;
						}

						if($_SESSION['user_id'] == 0) {
							echo 'Main admin cannot place orders';
							exit;
						}
					?>
				</div>
			</div>

			<hr>

			<div class="row">
				<div class="col">
					<form action="place_order.php" method="post">
						<input type="hidden" name="subtotal" value=<?php echo sprintf('%.2f', $total)?>>
						<input type="hidden" name="dCode" value=<?php echo $dCode?>>
						<input type="submit" value="Place Order">
					</form>
			
					<?php
						} else {
							echo 'Shopping Cart is currently empty';
						}
					?>
				</div>
			</div>
		</div>

		<script src="assets/js/bootstrap.bundle.min.js"></script>
		<script src="assets/js/jquery-3.7.1.js"></script>
	</body>
</html>
