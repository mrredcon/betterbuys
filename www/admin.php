<?php
	session_start();
	ob_start();

	
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


		<!-- ********************************************** -->
		<!-- 					Stores						-->
		<!-- ********************************************** -->
		
		
		<hr>
		<h1>Stores</h1>

		<br>

		<h2>Add Store</h2>
		<form action="store_insert.php" method="post">
			<label for="sname">Name:</label><br>
			<input type="text" id="sname" name="name"><br>

			<label for="saddress">Physical Address:</label><br>
			<input type="text" id="saddress" name="physicalAddress"><br>

			<label for="slatitude">Latitude:</label><br>
			<input type="text" id="slatitude" name="latitude"><br>

			<label for="slongitude">Longitude:</label><br>
			<input type="text" id="slongitude" name="longitude"><br>

			<label for="sonlineOnly">Online Only:</label><br>
			<select id="sonlineOnly" name="onlineOnly">
				<option value="0">No</option>
				<option value="1">Yes</option>
			</select><br>

			<label for="snumber">Store Number:</label><br>
			<input type="text" id="snumber" name="storeNumber"><br>

			<input type="submit" value="Add Store">
		</form>
		
		<?php
		//fetch all stores
		
			try {
				$sql = 'SELECT * FROM Store';
				$statement = $pdo->query($sql);
				$stores = $statement->fetchAll(PDO::FETCH_ASSOC);

				if ($stores) {
					echo "<table>";
						echo "<tr>";
							echo "<th>Store ID</th>";
							echo "<th>Store Number</th>";
							echo "<th>Store Name</th>";
							echo "<th>Store Location Address</th>";
							echo "<th>Store Latitude</th>";
							echo "<th>Store Longitude</th>";
							echo "<th>Online Only</th>"; 
							
						echo "</tr>";

					foreach ($stores as $store) {
						echo "<tr>";
							echo "<td>{$store['id']}</td>";
							echo "<td>{$store['storeNumber']}</td>"; 
							echo "<td>{$store['name']}</td>"; 
							echo "<td>{$store['physicalAddress']}</td>";
							echo "<td>{$store['latitude']}</td>";
							echo "<td>{$store['longitude']}</td>";
							echo "<td>" . ($store['onlineOnly'] ? "Yes" : "No") . "</td>";

							echo "<td>";
							echo "<a href='store_edit.php?id={$store['id']}'>Edit</a> ";
							echo "<a href='store_delete.php?id={$store['id']}' onclick=\"return confirm('Are you sure?')\">Delete</a>";
							echo "</td>";
							echo "</tr>";
							
							if (isset($store['id'])) {
								// It's safe to use $store['id'], so we proceed with echoing the form
								echo '<td>';
								echo '<form method="post" action="store_edit.php">';
									echo '<input type="hidden" name="id" value="'. $store['id'].'">';
									echo '<input type="submit" value="Edit">';
								echo '</form>';
								echo '</td>';
							} else {
								// $store['id'] is not set, so we do something else, like logging or displaying a message
								echo '<td>Store ID is not set.</td>';

							echo '<td>';
							echo '<form method="post" action="store_delete.php">';
								echo '<input type="hidden" name="id" value="'. $store['id'].'">';
								echo '<input type="submit" value="Delete">';
							echo '</form>';
						echo "</tr>";
					}}
					echo "</table>";
				} else {
					echo "No stores found.";
				}
			} catch (PDOException $e) {
				echo "Database error: " . $e->getMessage();
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
	</body>
</html>
<?php ob_end_flush(); ?> 
