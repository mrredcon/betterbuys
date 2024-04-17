<?php
	session_start();
?>
<!DOCTYPE html>
<html>
	<head>
		<link href="minimal-table.css" rel="stylesheet" type="text/css">
	</head>
	<body>
		<?php

    			// Check if user is logged in
    			if(!isset($_SESSION['user_id'])){
    			    header("Location: login.php");
    			    exit();
    			}

    			// Check if user is an administrator
    			$is_admin = $_SESSION['is_admin'];
			if (!$is_admin) {
				echo 'Only administrators can view this page.';
				exit();
			}
		?>

		<a href="/index.html">Back to home page</a>
		<hr>

		<form action="db_create.php" method="post">
			<input type="submit" value="Create database">
		</form>

		<hr>

		<form action="db_delete.php" method="post">
			<input type="submit" value="Delete database">
		</form>

		<hr>
		
		<h1>Products</h1>

		<form action="product_insert.php" method="post">
			<label for="pname">Name:</label><br>
			<input type="text" id="pname" name="name"><br>

			<label for="pdesc">Description:</label><br>
			<input type="text" id="pdesc" name="description"><br>

			<label for="pprice">Price:</label><br>
			<input type="text" id="pprice" name="price"><br>

			<label for="pquantity">Quantity:</label><br>
			<input type="text" id="pquantity" name="quantity"><br>

			<br>

			<input type="submit" value="Add product">

			<br>
			<br>
		</form>

	<?php
		$pdo = require 'connect.php';

		$sql = 'SELECT id, name, description FROM Product';
		
		$statement = $pdo->query($sql);
		
		// get all products
		$products = $statement->fetchAll(PDO::FETCH_ASSOC);
		
		if ($products) {
			echo "<table>";
				echo "<tr>";
					echo "<th>Id</th>";
					echo "<th>Name</th>";
					echo "<th>Description</th>";
				echo "</tr>";

				// show the products as a table
				foreach ($products as $product) {
					echo "<tr>";
						echo '<td>' . $product['id'] . '</td>';
						echo '<td>' . $product['name'] . '</td>';
						echo '<td>' . $product['description'] . '</td>';

						echo '<td>';
							echo '<form method="post" action="product_edit.php">';
								echo '<input type="hidden" name="product_id" value="'. $product['id'].'">';
								echo '<input type="submit" value="Edit">';
							echo '</form>';
						echo '</td>';

						echo '<td>';
							echo '<form method="post" action="product_delete.php">';
								echo '<input type="hidden" name="product_id" value="'. $product['id'].'">';
								echo '<input type="submit" value="Delete">';
							echo '</form>';
						echo '</td>';
					echo "</tr>";
				}
			echo "</table>";
		} else {
			echo "No products found!";
		}
	?>
	</form>

		<hr>
		<h1>Categories</h1>

		<form action="category_insert.php" method="post">
			<label for="cname">Category name:</label><br>
			<input type="text" id="cname" name="category_name"><br>
			<input type="submit" value="Add category">
		</form>

		<br>

		<?php
			$sql  = 'SELECT a.name, a.id, b.name AS parentCategoryName ';
			$sql .= 'FROM Category a LEFT JOIN Category b ON a.parentCategory = b.id ';
			$sql .= 'ORDER BY a.id';

			$statement = $pdo->query($sql);
			
			// get all categories
			$categories = $statement->fetchAll(PDO::FETCH_ASSOC);
			
			if ($categories) {
				echo "<table>";
					echo "<tr>";
						echo "<th>Id</th>";
						echo "<th>Name</th>";
						echo "<th>Parent Category</th>";
					echo "</tr>";

					// show the categories as a table
					foreach ($categories as $category) {
						$parentCategoryName = $category['parentCategoryName'];

						if ($parentCategoryName == null) {
							$parentCategoryName = '[None]';
						}
							

						echo "<tr>";
							echo "<td>" . $category['id'] . '</td>';
							echo "<td>" . $category['name'] . '</td>';
							echo "<td>" . $parentCategoryName . '</td>';

							echo '<td>';
								echo '<form method="post" action="category_edit.php">';
									echo '<input type="hidden" name="category_id" value="'. $category['id'].'">';
									echo '<input type="submit" value="Edit">';
								echo '</form>';
							echo '</td>';

							echo '<td>';
								echo '<form method="post" action="category_delete.php">';
									echo '<input type="hidden" name="category_id" value="'. $category['id'].'">';
									echo '<input type="submit" value="Delete">';
								echo '</form>';
							echo '</td>';
						echo "</tr>";
					}
				echo "</table>";
			} else {
				echo "No categories found!";
			}
		?> 

		<hr>
		<h1>Pending Users</h1>

		<br>

		<?php
			$sql  = 'SELECT id, emailAddress, confirmationCode, dateCreated FROM PendingUser';

			$statement = $pdo->query($sql);
			
			// get all pending users 
			$pending_users = $statement->fetchAll(PDO::FETCH_ASSOC);
			
			if ($pending_users) {
				echo "<table>";
					echo "<tr>";
						echo "<th>Id</th>";
						echo "<th>Email address</th>";
						echo "<th>Confirmation code</th>";
						echo "<th>Date created</th>";
						echo "<th>Delete?</th>";
					echo "</tr>";

					// show the pending users as a table
					foreach ($pending_users as $pending_user) {
						echo "<tr>";
							echo "<td>" . $pending_user['id'] . '</td>';
							echo "<td>" . $pending_user['emailAddress'] . '</td>';
							echo "<td>" . $pending_user['confirmationCode'] . '</td>';
							echo "<td>" . $pending_user['dateCreated'] . '</td>';

							echo '<td>';
								echo '<form method="post" action="delete.php">';
									echo '<input type="hidden" name="id" value="'. $pending_user['id'].'">';
									echo '<input type="hidden" name="table_name" value="PendingUser">';
									echo '<input type="submit" value="Delete">';
								echo '</form>';
							echo '</td>';
						echo "</tr>";
					}
				echo "</table>";
			} else {
				echo "No pending users found!";
			}
		?> 

		<hr>
		<h1>Confirmed Users</h1>

		<br>

		<?php
			$sql  = 'SELECT id, firstName, lastName, physicalAddress, emailAddress, money, isAdministrator, e164PhoneNumber FROM User';

			$statement = $pdo->query($sql);
			
			// get all users 
			$users = $statement->fetchAll(PDO::FETCH_ASSOC);
			
			if ($users) {
				echo "<table>";
					echo "<tr>";
						echo "<th>Id</th>";
						echo "<th>First name</th>";
						echo "<th>Last name</th>";
						echo "<th>Physical address</th>";
						echo "<th>Email address</th>";
						echo "<th>Money</th>";
						echo "<th>Is administrator?</th>";
						echo "<th>Phone number</th>";
						echo "<th>Delete?</th>";
					echo "</tr>";

					// show the users as a table
					foreach ($users as $user) {
						echo "<tr>";
							echo "<td>" . $user['id'] . '</td>';
							echo "<td>" . $user['firstName'] . '</td>';
							echo "<td>" . $user['lastName'] . '</td>';
							echo "<td>" . $user['physicalAddress'] . '</td>';
							echo "<td>" . $user['emailAddress'] . '</td>';
							echo "<td>" . $user['money'] . '</td>';
							echo "<td>" . ((bool)$user['isAdministrator'] ? 'True' : 'False') . '</td>';
							echo "<td>" . $user['e164PhoneNumber'] . '</td>';

							echo '<td>';
								echo '<form method="post" action="delete.php">';
									echo '<input type="hidden" name="id" value="'. $user['id'].'">';
									echo '<input type="hidden" name="table_name" value="User">';
									echo '<input type="submit" value="Delete">';
								echo '</form>';
							echo '</td>';
						echo "</tr>";
					}
				echo "</table>";
			} else {
				echo "No users found!";
			}
		?>
		
		<hr>
		<form action="view_transactions.php" method="post">
			<input type="submit" value="View All Transactions">
		</form>
		
		<hr>
		<form action="create_discount.php" method="post">
			<input type="submit" value="Create Discount Codes">
		</form>
		<br>
		
		<?php
			$sql  = 'SELECT id, code, reductionType, flatReduction, multiplierReduction, startDate, expireDate, productId, usesRemaining, userId, enabled FROM DiscountCode';

			$statement = $pdo->query($sql);
			
			// get all pending users 
			$discountCodes = $statement->fetchAll(PDO::FETCH_ASSOC);
			
			if ($discountCodes) {
				echo "<table>";
					echo "<tr>";
						echo "<th>Id</th>";
						echo "<th>Code</th>";
						echo "<th>Type</th>";
						echo "<th>Amount</th>";
						echo "<th>Start</th>";
						echo "<th>End</th>";
						echo "<th>Product</th>";
						echo "<th>User</th>";
						echo "<th>Uses Left</th>";
						echo "<th>Enabled</th>";
					echo "</tr>";

					// show the pending users as a table
					foreach ($discountCodes as $discountCode) {
						echo "<tr>";
							echo "<td>" . $discountCode['id'] . '</td>';
							echo "<td>" . $discountCode['code'] . '</td>';
							echo "<td>" . $discountCode['reductionType'] . '</td>';
							if($discountCode['reductionType'] === 'Flat'){
								echo "<td>" . $discountCode['flatReduction'] . '</td>';
							}
							else{
								echo "<td>" . $discountCode['multiplierReduction'] . '</td>';
							}
							echo "<td>" . $discountCode['startDate'] . '</td>';
							echo "<td>" . $discountCode['expireDate'] . '</td>';
							echo "<td>" . $discountCode['productId'] . '</td>';
							echo "<td>" . $discountCode['userId'] . '</td>';
							echo "<td>" . $discountCode['usesRemaining'] . '</td>';
							$isEnabled = ($discountCode['enabled'] == 1 ? 'true' : 'false');
							echo "<td>" . $isEnabled . '</td>';
							
							echo '<td>';
								echo '<form method="post" action="edit_discount.php">';
									echo '<input type="hidden" name="discountCodeId" value="'. $discountCode['id'].'">';
									echo '<input type="submit" value="Edit">';
								echo '</form>';
							echo '</td>';

							echo '<td>';
								echo '<form method="post" action="delete.php">';
									echo '<input type="hidden" name="id" value="'. $discountCode['id'].'">';
									echo '<input type="hidden" name="table_name" value="DiscountCode">';
									echo '<input type="submit" value="Delete">';
								echo '</form>';
							echo '</td>';
						echo "</tr>";
					}
				echo "</table>";
			} else {
				echo "No discount codes found!";
			}
		?>
		
	</body>
</html>
