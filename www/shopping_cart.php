<?php
	$product_id = filter_input(INPUT_POST, 'product_id');
	$order_amount = filter_input(INPUT_POST, 'amount');
	if ($product_id){
		if(!isset($_COOKIE['shoppingCart'])){
			$data = array($product_id=>$order_amount);
			setcookie('shoppingCart', json_encode($data), time() + (86400 * 30), "/");
		}
		else{
			$data = json_decode($_COOKIE['shoppingCart'], true);
			if(in_array($product_id, array_keys($data))){
				$data[$product_id] = $data[$product_id]+$order_amount;
			}
			else{
				$data[$product_id] = $order_amount;
			}
			setcookie('shoppingCart', json_encode($data), time() + (86400 * 30), "/");
		}			
		header("Refresh:0");
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
			
			$pdo = require 'connect.php';
			
			$item_array = json_decode($_COOKIE['shoppingCart'], true);

			$sql = 'SELECT id, name, description, price, discount FROM Product WHERE `id` IN (' . implode(',',  array_keys($item_array)) . ')';
			
			$statement = $pdo->query($sql);
			
			$products = $statement->fetchAll(PDO::FETCH_ASSOC);
			
			$total = 0;
			
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
					echo "</tr>";
					
				} 
			echo "</table>";
		?>
		
		<br>
		<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
			<input type="text" id="dcode" name="code">
			<input type="submit" value="Enter Discount Code">
		</form>
		
		<?php
			echo '<h1>Total: $' . sprintf('%.2f', $total) . '</h1>';
		?>
		
		<form action="place_order.php" method="post">
			<input type="submit" value="Place Order">
		</form>
			
		<?php
			
			} else {
				echo 'Shopping Cart is currently empty';
			}
			
		?>
		
	</body>
</html>