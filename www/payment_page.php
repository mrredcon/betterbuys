<?php
	session_start();
?>
<!DOCTYPE html>
<html>
	<head>
	</head>
	<body>
		<a href="shopping_cart.php" class="menu-item">Return to Shopping Cart</a>
		<hr>
		<h1>Enter Payment Information</h1>
		
		<form action="place_order.php" method="post">
			<label for="cardNumber">Card Number:</label><br>
			<input type="text" id="cardNumber">
			<br>
			
			<label for="expirationDate">Expiration Date:</label><br>
			<select id="expirationDate">
				<option>--</option> 
				<option>01</option>  
				<option>02</option>  
				<option>03</option>  
				<option>04</option>
				<option>05</option>  
				<option>06</option>
				<option>07</option>  
				<option>08</option>
				<option>09</option>  
				<option>10</option>
				<option>11</option>  
				<option>12</option>
			</select>
			/
			<select>
				<option>----</option> 
				<option>2028</option> 			
				<option>2028</option>  
				<option>2027</option>  
				<option>2026</option>  
				<option>2025</option>
				<option>2024</option>
			</select>
			<br>
			
			<label for="securityCode">Security Code:</label><br>
			<input type="text" id="securityCode">
			<br>
			
			<label for="paymentAddress">Payment Address:</label><br>
			<input type="text" id="paymentAddress">
			<br>
			
			<label for="zipCode">Zip Code:</label><br>
			<input type="text" id="zipCode">
			<br><br>
		
			<input type="hidden" name="subtotal" value=<?php echo filter_input(INPUT_POST, 'subtotal')?>>
			<input type="hidden" name="dCode" value=<?php echo filter_input(INPUT_POST, 'dCode')?>>
			<input type="submit" value="Finalize Order">
		</form>
	</body>
</html>