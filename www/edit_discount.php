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

            	header('Location: /admin.php?page=discountcodes');
            	exit;
	}

	// retrieve a single code
	$sql = 'SELECT productId, usesRemaining, userId, flatReduction, multiplierReduction, reductionType, code, startDate, expireDate, enabled FROM DiscountCode WHERE id=' . $discountCodeId . ';';
		
	$statement = $pdo->query($sql);
		
	//get a code
	$discountCode = $statement->fetch(PDO::FETCH_ASSOC);
	
	$amount = ($discountCode['reductionType'] === 'Flat') ? $discountCode['flatReduction'] : $discountCode['multiplierReduction'];
	$isEnabled = ($discountCode['enabled']) ? 'yes' : 'no';

	$multiplier_selected = $discountCode['reductionType'] === 'Multiplier' ? 'selected' : '';
	$discount_enabled = $discountCode['enabled'] == 1 ? 'checked' : '';

	$html = <<<EOD

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
	<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
		<h1 class="h2">Discount Codes</h1>
	</div>


	<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
		<form method="post">
			<div class="mb-3">
				<label for="inputCode" class="form-label">Code</label>
				<input type="text" class="form-control" id="inputCode" aria-describedby="helpCode" name="code" value="{$discountCode['code']}">
				<div id="helpCode" class="form-text">This is the code that the customer needs to enter to access the discount.</div>
			</div>

			<div class="mb-3">
				<label for="inputDiscountAmount" class="form-label">Discount Amount</label>
				<input type="text" class="form-control" id="inputDiscountAmount" name="amount" value={$amount}>
			</div>

			<div class="mb-3">
				<label for="selectReductionType" class="form-label">Reduction Type</label>
				<select class="form-select" id="selectReductionType" name="redtype">
					<option value='Flat'>Flat Reduction</option>  
					<option value='Multiplier' {$multiplier_selected} >Multiplier Reduction</option>  
				</select>
			</div>

			<div class="mb-3">
				<label for="inputEnabled" class="form-label">Enabled</label>
				<input type="checkbox" id="inputEnabled" name="checkbox" {$discount_enabled}>
			</div>

			<div class="mb-3">
				<label for="inputExpirationDate" class="form-label">Expiration Date</label>
				<input type="date" id="inputExpirationDate" name="expiration" value={$discountCode['expireDate']}><br><br>
			</div>

			<h4>Optional settings</h4>

			<div class="mb-3">
				<label for="inputProductId" class="form-label">Product ID</label>
				<input type="text" class="form-control" id="inputProductId" name="productId" value={$discountCode['productId']}>
			</div>


			<div class="mb-3">
				<label for="inputUserId" class="form-label">User ID</label>
				<input type="text" class="form-control" id="inputUserId" name="userId" value={$discountCode['userId']}>
			</div>

			<div class="mb-3">
				<label for="inputUses" class="form-label">Total Uses</label>
				<input type="text" class="form-control" id="inputUses" name="totalUses" value={$discountCode['usesRemaining']}>
			</div>

			<input type="hidden" name="discountCodeId" value={$discountCodeId}>
			<input type="hidden" name="oldCode" value={$discountCode['code']}>

			<button type="submit" class="btn btn-primary mb-2" name="submitted" value="Edit Discount Code">Edit Discount Code</button>
		</form>
	</div>
</main>
EOD;

	return $html;
?>
