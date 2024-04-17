<?php
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
		$dCode = filter_input(INPUT_POST, 'code');
		$expDate = filter_input(INPUT_POST, 'expiration');
		
		$pdo = require_once 'connect.php';
	
		// insert a single code
		$sql = 'INSERT INTO DiscountCode (productId, usesRemaining, userId, flatReduction, multiplierReduction, reductionType, code, startDate, expireDate, enabled) VALUES(:productId, :usesRemaining, :userId, :flatReduction, :multiplierReduction, :reductionType, :code, :startDate, :expireDate, :enabled)';
		
		$statement = $pdo->prepare($sql);
		
		$statement->execute([
			':productId' => $prodID,
			':usesRemaining' => $usesLeft,
			':userId' => $userID,
			':flatReduction' => $flatRed,
			':multiplierReduction' => $multRed,
			':reductionType' => $reductionType,
			':code' => $dCode,
			':startDate' => date('Y-m-d H:i:s'),
			':expireDate' => $expDate,
			':enabled' => 1
		]);
	}
?>
<!DOCTYPE html>
<html>
	<head>
	</head>
	<body>
		<a href="admin.php" class="menu-item">Return to Admin page</a>
	
		<h1>Create Discount Code</h1>

		<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
			<label for="dcode">Code:</label><br>
			<input type="text" id="dcode" name="code"><br><br>
			
			<label for="damount">Discount Amount:</label><br>
			<input type="text" id="damount" name="amount"><br>
			
			<select name="redtype">  
				<option value='Flat'>Flat Reduction</option>  
				<option value='Multiplier'>Multiplier Reduction</option>  
			</select>
			<br><br>

			<label for="expiredate">Expiration Date:</label><br>
			<input type="date" id="expiredate" name="expiration"><br>
			
			<br>
			<span style="font-size: 20px;">Optional:</span>
			<br><br>
			
			<label for="dproduct">Product ID:</label><br>
			<input type="text" id="dproduct" name="productId"><br>
			
			<label for="duser">User ID:</label><br>
			<input type="text" id="duser" name="userId"><br>
			
			<label for="duses">Total Uses:</label><br>
			<input type="text" id="duses" name="totalUses"><br><br>

			<input type="submit" name="submitted" value="Create Discount Code">

			<br>
			<br>
		</form>
	</body>
</html>