<!DOCTYPE html>
<html>
<head>
    <title>Welcome to Better Buys!</title>
    <style>
        .menu-item {
            margin-right: 20px;
        }
    </style>
</head>
<body>
    <h1>Welcome to Better Buys!</h1>
    <div class="menu">
        <!-- Admin link -->
        <a href="admin.php" class="menu-item">Admin page</a>

        <!-- Register link -->
        <a href="register.php" class="menu-item">Register</a>

        <!-- Login link -->
        <a href="login.php" class="menu-item">Login</a>

		<!-- Profile link -->
        <a href="profile.php?user_id=<?php echo $_SESSION['user_id']; ?>" class="menu-item">View Profile</a>
    </div>
    <hr>

		<?php

		$pdo = require 'connect.php';
		
		$sql = 'SELECT id, name, description FROM Product';
		
		$statement = $pdo->query($sql);
		
		// get all publishers
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
						echo "<td>" . $product['id'] . '</td>';
						echo "<td>" . $product['name'] . '</td>';
						echo "<td>" . $product['description'] . '</td>';
					echo "</tr>";
				}
			echo "</table>";
		} else {
			echo "No products found!";
		}
		?> 
	</body>
</html>
