<?php
	session_start();
	$pdo = require_once 'connect.php';
	
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
?>
<!DOCTYPE html>
<html>
	<head>
	</head>
	<body>
		<a href="index.php" class="menu-item">Return to Home page</a>
		<hr>
		Order Placed
	</body>
</html>