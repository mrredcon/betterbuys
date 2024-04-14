<?php
	session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Welcome to Better Buys!</title>
    <style>
        .menu-item {
            margin-right: 20px;
        }
    </style>
    
    <!-- temporary tables to prevent eye strain-->
    <link href="minimal-table.css" rel="stylesheet" type="text/css">
    
    <script 
        async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDF6QNhM2ewfjKKRJ7i2MxLourmtYaNT9g&callback=initMap">
    </script>

</head>
<form action="db_create.php" method="post">
			<input type="submit" value="Create database">
</form>
<form action="dlt_cookie.php" method="post">
			<input type="submit" value="Delete Your Cookies">
</form>
<body>
    <h1>Welcome to Better Buys!</h1>
    <?php
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
				
				# Shopping Cart link
				echo '<a href="shopping_cart.php" class="menu-item">Shopping Cart</a>';

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

        <!-- ********************************************** -->
		<!-- 			Product Cart-Elements				-->
		<!-- ********************************************** -->

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
            			
						
							echo '<td>';
								echo '<form method="post" action="shopping_cart.php">';
									echo '<input type="hidden" name="product_id" value="'. $product['id'].'">';
									echo '<input type="submit" value="Add to Cart">';
									echo '<input type="text" id="oamount" name="amount">';
								echo '</form>';
							echo '</td>';
						echo "</tr>";
            		}
            	echo "</table>";
            } else {
            	echo "No products found!";
            }
        ?> 


        <!-- ********************************************** -->
		<!-- 				Store Finder-For User		    -->
		<!-- ********************************************** -->
        <?php

         
        //---------------------Search Functionality---------------------//

        // Retrieve the search term from the POST array using 'storeSearch'
        $storeSearch = $_POST['storeSearch'] ?? '';

        // Prepare the SQL query to search for stores
        $sql = 'SELECT name, physicalAddress, latitude, longitude, onlineOnly, storeNumber FROM Store';
        if ($storeSearch) {
            $sql .= ' WHERE name LIKE :storeSearch OR storeNumber LIKE :storeSearch';
            $statement = $pdo->prepare($sql);
            $statement->execute(['storeSearch' => "%$storeSearch%"]);
        } else {
            $statement = $pdo->query($sql);
        }

        $stores = $statement->fetchAll(PDO::FETCH_ASSOC);

        if ($stores) {
            echo "<table>";
            echo "<tr><th>Store Number</th><th>Name</th><th>Physical Address</th><th>Latitude</th><th>Longitude</th><th>Online Only</th></tr>";
            foreach ($stores as $store) {
                // Check if the store is online only and set display text
                $onlineOnlyText = $store['onlineOnly'] ? 'Yes' : 'No';
        
                echo "<tr>";
                echo "<td>" . htmlspecialchars($store['storeNumber']) . "</td>";
                echo "<td>" . htmlspecialchars($store['name']) . "</td>";
                echo "<td>" . htmlspecialchars($store['physicalAddress']) . "</td>";
                echo "<td>" . htmlspecialchars($store['latitude']) . "</td>";
                echo "<td>" . htmlspecialchars($store['longitude']) . "</td>";
                echo "<td>" . $onlineOnlyText . "</td>";
                echo "</tr>";
            }
            echo "</table>";

            //---------------------Search Functionality Fin---------------------//
            

        } else {
            echo "No stores found!";
        }
        
        ?>
        
        <!---------------------Form Functionality--------------------->
        
        <form method="post" action="">
            <input type="text" name="storeSearch" placeholder="Search by store name or number...">
            <input type="submit" value="Search">
        </form>
        
        <!---------------------Form Functionality Fin--------------------->


        <!---------------------Map Functionality--------------------->

        <div id="map" style="height: 400px; width: 400px;"></div>

        <script>
        function initMap() {
            var bounds = new google.maps.LatLngBounds();
            var map = new google.maps.Map(document.getElementById('map'), {
                zoom: 10 // Initial zoom, but will be overridden by fitBounds if there are markers
            });

            <?php if (!empty($stores)): ?>
                <?php foreach ($stores as $store): ?>
                    var position = new google.maps.LatLng(parseFloat("<?php echo $store['latitude']; ?>"), parseFloat("<?php echo $store['longitude']; ?>"));
                    var marker = new google.maps.Marker({
                        position: position,
                        map: map,
                        title: "<?php echo htmlspecialchars($store['name']); ?>"
                    });
                    bounds.extend(position);
                <?php endforeach; ?>
                
                
                map.fitBounds(bounds, {top: 50, right: 50, bottom: 50, left: 50});
                google.maps.event.addListenerOnce(map, 'idle', function() {
                    if (map.getZoom() > 15) {
                        map.setZoom(15);
                    }
                });

                
            <?php else: ?>
                // Set a default center if no stores are available
                map.setCenter({lat: 29.61103, lng: -98.59598}); // San Antonio example coordinates
            <?php endif; ?>
        }
    </script>
        
        <!---------------------Map Functionality--------------------->

        </body>
    </html>
