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
    <?php
        session_start();
	if(array_key_exists('login', $_SESSION)) {
            echo '<p>You\'re logged in as ' . $_SESSION['login'] . '.</p>';
	}
    ?>
    <div class="menu">
	<?php
            # Admin Link
	    if(array_key_exists('is_admin', $_SESSION) && $_SESSION['is_admin']) {
                echo '<a href="admin.php" class="menu-item">Admin page</a>';
	    }

	    if(array_key_exists('user_id', $_SESSION)) {
		# Profile link
                echo '<a href="profile.php" class="menu-item">View Profile</a>';

                # Log out link
                echo '<a href="logout.php" class="menu-item">Log Out</a>';
	    } else {
                # Login link
                echo '<a href="login.php" class="menu-item">Login</a>';

                # Register link
                echo '<a href="register.php" class="menu-item">Register</a>';
	    }
        ?>
    </div>
    <hr>

        <?php
            $pdo = require_once 'connect.php';
            
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
