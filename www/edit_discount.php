<?php
	$pdo = require_once 'connect.php';
	$discountCodeId = filter_input(INPUT_POST, 'discountCodeId');
	
	if(filter_input(INPUT_POST, 'submitted')){
		$prodID = (filter_input(INPUT_POST, 'productId')) ? filter_input(INPUT_POST, 'productId') : NULL;
		$usesLeft = (filter_input(INPUT_POST, 'totalUses')) ? filter_input(INPUT_POST, 'totalUses') : NULL;
		$userID = (filter_input(INPUT_POST, 'userId')) ? filter_input(INPUT_POST, 'userId') : NULL;
		$reductionType = filter_input(INPUT_POST, 'redtype');
		if($reductionType === 'Flat'){
			$flatRed = filter_input(INPUT_POST, 'amount');
			$multRed = NULL;
		}
		else {
			$multRed = filter_input(INPUT_POST, 'amount');
			$flatRed = NULL;
		}
		$dCode = (!filter_input(INPUT_POST, 'code')) ? filter_input(INPUT_POST, 'oldCode') : filter_input(INPUT_POST, 'code');
		$expDate = filter_input(INPUT_POST, 'expiration');
		
		$sql = 'UPDATE DiscountCode SET productId = :productId, usesRemaining = :usesRemaining, userId = :userId, flatReduction = :flatReduction, multiplierReduction = :multiplierReduction, reductionType = :reductionType, code = :code, expireDate = :expireDate, enabled = :enabled WHERE id=:id';
		
		$statement = $pdo->prepare($sql);
		
		$statement->execute([
			':productId' => $prodID,
			':usesRemaining' => $usesLeft,
			':userId' => $userID,
			':flatReduction' => $flatRed,
			':multiplierReduction' => $multRed,
			':reductionType' => $reductionType,
			':code' => $dCode,
			':expireDate' => $expDate,
			':enabled' => (isset($_POST['checkbox']) ? 1 : 0),
			':id' => $discountCodeId
		]);
	}

	// retrieve a single code
	$sql = 'SELECT productId, usesRemaining, userId, flatReduction, multiplierReduction, reductionType, code, startDate, expireDate, enabled FROM DiscountCode WHERE id=' . $discountCodeId . ';';
		
	$statement = $pdo->query($sql);
		
	//get a code
	$discountCode = $statement->fetch(PDO::FETCH_ASSOC);
	
	$amount = ($discountCode['reductionType'] === 'Flat') ? $discountCode['flatReduction'] : $discountCode['multiplierReduction'];
	$isEnabled = ($discountCode['enabled']) ? 'yes' : 'no';
?>

<!DOCTYPE html>
<html>
	<head>
	</head>
	<body>
		<a href="admin.php" class="menu-item">Return to Admin page</a>
		
		<h1>Edit Discount Code</h1>

		<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
			<label for="dcode">Code:</label><br>
			<input type="text" id="dcode" name="code" value=<?php echo $discountCode['code']; ?>><br><br>
			
			<label for="damount">Discount Amount:</label><br>
			<input type="text" id="damount" name="amount" value=<?php echo $amount; ?>><br>
			
			<select name="redtype">  
				<option value='Flat'>Flat Reduction</option>  
				<option value='Multiplier'  <?php if($discountCode['reductionType'] === 'Multiplier') echo 'selected="selected"';?>>Multiplier Reduction</option>  
			</select>
			<br><br>

			<label for="expiredate">Expiration Date:</label><br>
			<input type="date" id="expiredate" name="expiration" value=<?php echo $discountCode['expireDate']; ?>><br><br>
			
			<label for="cbox">Enabled:</label><br>
			<input type="checkbox" id="cbox" name="checkbox" value="yes" <?php echo ($discountCode['enabled']==1 ? 'checked' : '');?>><br>
			
			<br>
			<span style="font-size: 20px;">Optional:</span>
			<br><br>
			
			<label for="dproduct">Product ID:</label><br>
			<input type="text" id="dproduct" name="productId" value=<?php echo $discountCode['productId']; ?>><br>
			
			<label for="duser">User ID:</label><br>
			<input type="text" id="duser" name="userId" value=<?php echo $discountCode['userId']; ?>><br>
			
			<label for="duses">Total Uses:</label><br>
			<input type="text" id="duses" name="totalUses" value=<?php echo $discountCode['usesRemaining']; ?>><br><br>

			<input type="submit" name="submitted" value="Edit Discount Code">
			
			<input type="hidden" name="discountCodeId" value=<?php echo $discountCodeId; ?>>
			<input type="hidden" name="oldCode" value=<?php echo $discountCode['code']; ?>>

			<br>
			<br>
		</form>
	</body>
</html>