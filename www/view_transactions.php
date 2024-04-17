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
			$sql = 'SELECT id, userId, subtotal, tax, shippingFee, fulfilled FROM Transaction WHERE userId=' . filter_input(INPUT_POST, 'customer_id');
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
			
	$transactions = $statement->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
	<head>
		<link href="minimal-table.css" rel="stylesheet" type="text/css">
	</head>
	<body>
		<a href="admin.php" class="menu-item">Return to Admin Page</a>
		<h1>Transactions</h1>
		<br>
		
		<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
			<label for="sortType">Sort By:</label><br>
			<select id="sortType" name="sort_type">
				<option value='date'  <?php if($sort === 'date') echo 'selected="selected"'?>>Order Date</option>
				<option value='priceG'  <?php if($sort === 'priceG') echo 'selected="selected"'?>>Price: Greatest</option>
				<option value='priceL'  <?php if($sort === 'priceL') echo 'selected="selected"'?>>Price: Least</option>
				<option value='customer'  <?php if($sort === 'customer') echo 'selected="selected"'?>>Customer</option>
				<option value='fulfilled'  <?php if($sort === 'fulfilled') echo 'selected="selected"'?>>Fulfilled</option>
				<option value='unfulfilled'  <?php if($sort === 'unfulfilled') echo 'selected="selected"'?>>Unfulfilled</option>
			</select>
			<input type="text" id="customerId" name="customer_id" placeholder="Enter Customer Id">
			<label for="customerId">*For customer sort only</label><br>
			<input type="submit" value="Sort Orders"><br><br>
		</form>
		
		<?php
			
			if($transactions){
				echo "<table>";
					echo "<tr>";
						echo "<th>Id</th>";
						echo "<th>userId</th>";
						echo "<th>subtotal</th>";
						echo "<th>tax</th>";
						echo "<th>shippingFee</th>";
						echo "<th>Fulfilled?</th>";
					echo "</tr>";
				
					foreach ($transactions as $transaction){
						echo "<tr>";
							echo '<td>' . $transaction['id'] . '</td>';
							echo '<td>' . $transaction['userId'] . '</td>';
							echo '<td>' . $transaction['subtotal'] . '</td>';
							echo '<td>' . $transaction['tax'] . '</td>';
							echo '<td>' . $transaction['shippingFee'] . '</td>';
							echo '<td>' . (($transaction['fulfilled']) ? 'Yes' : 'No') . '</td>';
							
							echo '<td>';
							echo '<form method="post" action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '">';
								echo '<input type="hidden" name="transaction_id" value="'. $transaction['id'] .'">';
								echo '<input type="hidden" name="sort_type" value="'. $sort .'">';
								echo '<input type="submit" value="' . (($transaction['fulfilled']) ? 'unfulfill' : 'fulfill') .'">';
							echo '</form>';
						echo '</td>';
						echo "</tr>";
					}
			}
			else{
				echo "No Transactions found";
			}
		?>

	</body>
</html>