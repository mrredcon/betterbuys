<?php
	session_start();
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
	</head>
	<body>
		<a href="/index.html">Back to home page</a>
		<hr>
		
		<h1>Shopping Cart</h1>
		
		<?php
			if(isset($_COOKIE['shoppingCart'])){
			
			$item_array = json_decode($_COOKIE['shoppingCart'], true);

			$sql = 'SELECT id, name, description, price, discount FROM Product WHERE `id` IN (' . implode(',',  array_keys($item_array)) . ')';
			
			$statement = $pdo->query($sql);
			
			$products = $statement->fetchAll(PDO::FETCH_ASSOC);
			
			$total = 0.0;
			
			echo "<table>";
				echo "<tr>";
					echo "<th>Id</th>";
					echo "<th>Name</th>";
					echo "<th>Description</th>";
					echo "<th>Quantity</th>";
					echo "<th>Price</th>";
				echo "</tr>";
			
				foreach ($products as $product){
					echo "<tr>";
						echo '<td>' . $product['id'] . '</td>';
						echo '<td>' . $product['name'] . '</td>';
						echo '<td>' . $product['description'] . '</td>';
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
			echo "</table>";
			
			$error = filter_input(INPUT_GET, 'error');
			if($error === 'quantityTooHigh'){
				echo '<span style="color: blue;">* Quantity Too High. Setting To Max.</span>';
			}
			else if($error === 'quantityNotAnInteger'){
				echo '<span style="color: red;">* Quantity Must Be Number.</span>';
			}
			
			echo '<br>';
			
			if(isset($_SESSION['user_id'])){
		?>
		
		<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
			<input type="text" id="dcode" name="code">
			<input type="submit" value="Enter Discount Code">
		</form>
		
		<?php
			}
			else{
				echo 'Please login to enter a discount code';
			}
		
			if($dCode === 'ERROR'){
				echo '<span style="color: red;">* Invalid Discount Code</span>';
				$dCode = NULL;
			}
		
			if($discountType === 'Flat'){
				echo '<h2>Subtotal: <del>$' . sprintf('%.2f', $total) . '</del> $' . sprintf('%.2f', $total - $discountAmount) . '</h2>';
				$total = $total - $discountAmount;
			}
			else if($discountType === 'Multiplier'){
				echo '<h2>Subtotal: <del>$' . sprintf('%.2f', $total) . '</del> $' . sprintf('%.2f', $total * (1 - $discountAmount)) . '</h2>';
				$total = $total * (1 - $discountAmount);
			}
			else {
				echo '<h2>Subtotal: $' . sprintf('%.2f', $total) . '</h2>';
			}
			
			echo 'Tax: $' . sprintf('%.2f', $total * .0825) . '<br>';
			echo 'Shipping Fee: $5.00';
			echo '<h1>Total: = $' . sprintf('%.2f', $total) + sprintf('%.2f', $total * .0825) + 5.00 . '</h1>';
			
			if(!isset($_SESSION['user_id'])){
    		    echo 'Please login to place an order';
				exit;
    		}
			if($_SESSION['user_id'] == 0){
    		    echo 'Main admin cannot place orders';
				exit;
    		}
		?>
		
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
		
	</body>
</html>