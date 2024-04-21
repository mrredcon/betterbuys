<?php
	session_start();
?>
<!DOCTYPE html>
<html>
	<head>
	</head>
	<body>
		<a href="index.php" class="menu-item">Return to Home page</a>
		<hr>
		<?php
			$pdo = require_once 'connect.php';
			
			$sql = 'SELECT physicalAddress FROM User WHERE id=' . $_SESSION['user_id'];
					
			$statement = $pdo->query($sql);
				
			$address = $statement->fetchColumn();
			
			if(!$address){
				?>
				<span style="color: red;">* Invalid or Missing Address. Please edit your profile to include a proper address</span>
				<br>
				<a href="edit_profile.php" class="menu-item">Go to edit Profile</a>
				<?php
			}
			
			if(filter_input(INPUT_POST, 'dCode')){
				$sql = 'SELECT id, usesRemaining FROM DiscountCode WHERE code="' .  filter_input(INPUT_POST, 'dCode') . '";';
				
				$statement = $pdo->query($sql);
				
				$code = $statement->fetch(PDO::FETCH_ASSOC);
				
				if($code['usesRemaining']){
					$sql = 'UPDATE DiscountCode SET usesRemaining = :usesRemaining WHERE id=:id';
					
					$statement = $pdo->prepare($sql);
					
					$statement->execute([
						':usesRemaining' => $code['usesRemaining'] - 1,
						':id' => $code['id']
					]);
				}
			}
			
			$sql = 'INSERT INTO Transaction (userId, purchaseDate, storeId, purchaseType, subtotal, tax, shippingFee, fulfilled) VALUES(:userId, :purchaseDate, :storeId, :purchaseType, :subtotal, :tax, :shippingFee, :fulfilled)';
			
			$statement = $pdo->prepare($sql);
			
			$statement->execute([
				':userId' => $_SESSION['user_id'],
				':purchaseDate' => date("Y/m/d"),
				':storeId' => 1,
				':purchaseType' => 'delivery',
				':subtotal' => sprintf('%.2f', filter_input(INPUT_POST, 'subtotal')),
				':tax' => sprintf('%.2f', filter_input(INPUT_POST, 'subtotal') * .0825),
				':shippingFee' => 5.00,
				':fulfilled' => 0
			]);
			
			$transaction_id = $pdo->lastInsertId();
			
			$product_array = json_decode($_COOKIE['shoppingCart'], true);
			
			foreach($product_array as $key => $value){
				$sql = 'INSERT INTO TransactionItem (transactionId, productId, quantity) VALUES(:transactionId, :productId, :quantity)';
			
				$statement = $pdo->prepare($sql);
			
				$statement->execute([
					':transactionId' => $transaction_id,
					':productId' => $key,
					':quantity' => $value
				]);
				
				$sql = 'SELECT quantity FROM Inventory WHERE productId=' . $key . ' AND storeId=1';
					
				$statement = $pdo->query($sql);
						
				$inventory = $statement->fetchColumn();
				
				$newQuantity = $inventory - $value;
				
				$sql = 'UPDATE Inventory SET quantity = :quantity WHERE productId=:productId AND storeId=:storeId';
				
				$statement = $pdo->prepare($sql);
				
				$statement->execute([
					':quantity' => $newQuantity,
					':productId' => $key,
					':storeId' => 1
				]);
			}
		?>
	</body>
</html>