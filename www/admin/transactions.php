<?php
	$pdo = require 'connect.php';
	
	if(filter_input(INPUT_POST, 'transaction_id')){
		$sql = 'SELECT id, fulfilled FROM Transaction WHERE id=' . filter_input(INPUT_POST, 'transaction_id');
		
		$statement = $pdo->query($sql);
		
		$fulfillTransaction = $statement->fetch(PDO::FETCH_ASSOC);
		
		$status = ($fulfillTransaction['fulfilled'] == 0 ? 1 : 0);
		
		$sql = 'UPDATE Transaction SET fulfilled = :fulfilled WHERE id=:id';
		
		$statement = $pdo->prepare($sql);
		
		$statement->execute([
			':fulfilled' => $status,
			':id' => $fulfillTransaction['id']
		]);
	}
	
	$sort = filter_input(INPUT_POST, 'sort_type');
	switch($sort){
		case 'priceG':
			$sql = 'SELECT id, userId, subtotal, tax, shippingFee, fulfilled FROM Transaction ORDER BY subtotal DESC';
			break;
		case 'priceL':
			$sql = 'SELECT id, userId, subtotal, tax, shippingFee, fulfilled FROM Transaction ORDER BY subtotal';
			break;
		case 'customer':
			$customer_id = filter_input(INPUT_POST, 'customer_id');
			if ($customer_id) {
				$sql = 'SELECT id, userId, subtotal, tax, shippingFee, fulfilled FROM Transaction WHERE userId=' . $customer_id;
			} else {
				// User forgot to set customer ID before clicking sort
				$sql = 'SELECT id, userId, subtotal, tax, shippingFee, fulfilled FROM Transaction ORDER BY id DESC';
			}
			break;
		case 'fulfilled':
			$sql = 'SELECT id, userId, subtotal, tax, shippingFee, fulfilled FROM Transaction WHERE fulfilled=1';
			break;
		case 'unfulfilled':
			$sql = 'SELECT id, userId, subtotal, tax, shippingFee, fulfilled FROM Transaction WHERE fulfilled=0';
			break;
		default:
			$sort = 'date';
		case 'date':
			$sql = 'SELECT id, userId, subtotal, tax, shippingFee, fulfilled FROM Transaction ORDER BY id DESC';
	}
			
	$statement = $pdo->query($sql);
			
	$table = '';
	$transactions = $statement->fetchAll(PDO::FETCH_ASSOC);
	if($transactions){
		$table .= <<<EOD
		<div class="table-responsive small">
			<table class="table table-striped table-sm">
				<thead>
					<tr>
						<th>Id</th>
						<th>userId</th>
						<th>subtotal</th>
						<th>tax</th>
						<th>shippingFee</th>
						<th>Fulfilled?</th>
						<th>Action</th>
					</tr>
				</thead>
		EOD;
		
				foreach ($transactions as $transaction){
					$fulfilled_status = (($transaction['fulfilled']) ? 'Yes' : 'No');
					$fulfilled_option = (($transaction['fulfilled']) ? 'unfulfill' : 'fulfill');

					$table .= <<<EOD
					<tr>
						<td>{$transaction['id']}</td>
						<td>{$transaction['userId']}</td>
						<td>{$transaction['subtotal']}</td>
						<td>{$transaction['tax']}</td>
						<td>{$transaction['shippingFee']}</td>
						<td>{$fulfilled_status}</td>
						
						<td>
							<form method="post">
								<input type="hidden" name="transaction_id" value="{$transaction['id']}">
								<input type="hidden" name="sort_type" value="$sort">
								<input type="submit" value="$fulfilled_option">
							</form>
						</td>
					</tr>
					EOD;
				}

			$table .= '</table></div>';
	} else {
		$table = "No Transactions found";
	}

	$sort_date_selected        = $sort === 'date'        ? 'selected' : '';
	$sort_priceG_selected      = $sort === 'priceG'      ? 'selected' : '';
	$sort_priceL_selected      = $sort === 'priceL'      ? 'selected' : '';
	$sort_customer_selected    = $sort === 'customer'    ? 'selected' : '';
	$sort_fulfilled_selected   = $sort === 'fulfilled'   ? 'selected' : '';
	$sort_unfulfilled_selected = $sort === 'unfulfilled' ? 'selected' : '';

	$html = <<<EOD
	<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
		<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-0 border-bottom">
			<h1 class="h2">Transactions</h1>
		</div>
	
		<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
			<form method="post">
				<div class="mb-3">
					<label for="selectSortType" class="form-label">Filter/Sort By</label>
					<select class="form-select" id="selectSortType" name="sort_type">
						<option value='date' $sort_date_selected >Order Date</option>
						<option value='priceG' $sort_priceG_selected >Price: Greatest</option>
						<option value='priceL' $sort_priceL_selected >Price: Least</option>
						<option value='customer' $sort_customer_selected >Customer</option>
						<option value='fulfilled' $sort_fulfilled_selected >Fulfilled</option>
						<option value='unfulfilled' $sort_unfulfilled_selected >Unfulfilled</option>
					</select>
				</div>
	
				<div class="mb-3">
					<label for="inputCustomerId" class="form-label">Customer Id (for customer sort only)</label>
					<input type="text" class="form-control" id="inputCustomerId" name="customerId">
				</div>
	
				<button type="submit" class="btn btn-primary mb-2" value="Sort Orders">Sort Orders</button>
			</form>
		</div>
	
		{$table}
	</main>
	EOD;
	return $html;
?>
