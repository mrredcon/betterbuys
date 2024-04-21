<?php
	$pdo = require_once 'connect.php';

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

	$sql  = 'SELECT id, code, reductionType, flatReduction, multiplierReduction, startDate, expireDate, productId, usesRemaining, userId, enabled FROM DiscountCode';

	$statement = $pdo->query($sql);
	
	// get all discount codes
	$discountCodes = $statement->fetchAll(PDO::FETCH_ASSOC);
	$table = '';
	
	if ($discountCodes) {
		$table = <<<EOD
		<div class="table-responsive small my-2">
			<table class="table table-striped table-sm">
			<thead>
				<tr>
					<th>Id</th>
					<th>Code</th>
					<th>Type</th>
					<th>Amount</th>
					<th>Start</th>
					<th>End</th>
					<th>Product</th>
					<th>User</th>
					<th>Uses Left</th>
					<th>Enabled</th>
					<th>Edit</th>
					<th>Delete</th>
				</tr>
			</thead>
EOD;

			// show the discounts as a table
			foreach ($discountCodes as $discountCode) {
				$reduction = $discountCode['reductionType'] === 'Flat' ? $discountCode['flatReduction'] : $discountCode['multiplierReduction'];
				$isEnabled = ($discountCode['enabled'] == 1 ? 'true' : 'false');
				$table .= <<<EOD
				<tr>
					<td>{$discountCode['id']}</td>
					<td>{$discountCode['code']}</td>
					<td>{$discountCode['reductionType']}</td>
					<td>{$reduction}</td>
					<td>{$discountCode['startDate']}</td>
					<td>{$discountCode['expireDate']}</td>
					<td>{$discountCode['productId']}</td>
					<td>{$discountCode['userId']}</td>
					<td>{$discountCode['usesRemaining']}</td>
					<td>{$isEnabled}</td>
					
					<td>
						<form method="post" action="/admin.php?page=edit_discount">
							<input type="hidden" name="discountCodeId" value="{$discountCode['id']}">
							<button type="submit" value="Edit" class="btn py-0"><i class="icon-edit"></i></button>
						</form>
					</td>

					<td>
						<form method="post" action="delete.php">
							<input type="hidden" name="id" value="{$discountCode['id']}">
							<input type="hidden" name="table_name" value="DiscountCode">
							<input type="hidden" name="redirect" value="/admin.php?page=discountcodes">
							<button type="submit" value="Delete" class="btn py-0"><i class="icon-remove"></i></button>
						</form>
					</td>
				</tr>
EOD;
			}
		$table .= '</table></div>';
	} else {
		$table = "No discount codes found!";
	}

$html = <<<EOD
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
	<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
		<h1 class="h2">Discount Codes</h1>
	</div>

	<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
		<form method="post">
			<div class="mb-3">
				<label for="inputCode" class="form-label">Code</label>
				<input type="text" class="form-control" id="inputCode" aria-describedby="helpCode" name="code">
				<div id="helpCode" class="form-text">This is the code that the customer needs to enter to access the discount.</div>
			</div>

			<div class="mb-3">
				<label for="inputDiscountAmount" class="form-label">Discount Amount</label>
				<input type="text" class="form-control" id="inputDiscountAmount" name="amount">
			</div>

			<div class="mb-3">
				<label for="selectReductionType" class="form-label">Reduction Type</label>
				<select class="form-select" id="selectReductionType" name="redtype">
					<option value='Flat'>Flat Reduction</option>  
					<option value='Multiplier'>Multiplier Reduction</option>  
				</select>
			</div>

			<div class="mb-3">
				<label for="inputExpirationDate" class="form-label">Expiration Date</label>
				<input type="date" class="form-control" id="inputExpirationDate" name="expiration">
			</div>

			<h4>Optional settings</h4>

			<div class="mb-3">
				<label for="inputProductId" class="form-label">Product ID</label>
				<input type="text" class="form-control" id="inputProductId" name="productId">
			</div>


			<div class="mb-3">
				<label for="inputUserId" class="form-label">User ID</label>
				<input type="text" class="form-control" id="inputUserId" name="userId">
			</div>

			<div class="mb-3">
				<label for="inputUses" class="form-label">Total Uses</label>
				<input type="text" class="form-control" id="inputUses" name="totalUses">
			</div>

			<button type="submit" class="btn btn-primary mb-2" name="submitted" value="Create Discount Code">Create Discount Code</button>
		</form>
	</div>

	<br>

	{$table}
</main>
EOD;
return $html;
?>
