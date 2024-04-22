<?php
	session_start();
	
	// Check if user is logged in
	if(!isset($_SESSION['user_id'])){
	    header("Location: login.php");
	    exit();
	}
	
	// Check if user is an administrator
	$is_admin = $_SESSION['is_admin'];
	
	// System check for if user trying to access their account
	$user_to_show = filter_input(INPUT_GET, 'user_id');
	if ($user_to_show && !$is_admin && $user_to_show != $_SESSION['user_id']) {
	    // If not, deny access
	    echo "Access Denied";
	    exit();
	}
	
	// If we are missing the GET parameter, just assume user is viewing their own profile
	if (!$user_to_show) {
	        $user_to_show = $_SESSION['user_id'];
	}
	
	$pdo = require_once 'connect.php';
	
	// Fetch user's profile info from the database
	$sql = 'SELECT firstName, lastName, physicalAddress, emailAddress, money, e164PhoneNumber FROM User WHERE id=:user_id';
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':user_id', $user_to_show, PDO::PARAM_INT);
	$stmt->execute();
	$user = $stmt->fetch(PDO::FETCH_ASSOC);
	
	if(!$user){
	    // If user does not exist. prompt an error message
	    echo "Root admin cannot view the profile page or user not found.";
	    exit();
	}

	if ($_SERVER["REQUEST_METHOD"] == "POST" && ($is_admin || $_SESSION['user_id'] == $user_to_show)) {
	    // Retrieve form data
	    $first_name = $_POST['first_name'];
	    $last_name = $_POST['last_name'];
	    $address = $_POST['address'];
	    $phone_number = $_POST['phone_number'] ?: null;
	    $money = $_POST['money'];
	
	    // Validate money amount
	    if($money < 0) {
	        echo "Money amount must be positive.";
	        exit();
	    }
	
	    // Update user's profile info in the database
	    $sql = 'UPDATE User SET firstName=:first_name, lastName=:last_name, physicalAddress=:address, e164PhoneNumber=:phone_number, money=:money WHERE id=:user_id';
	    $stmt = $pdo->prepare($sql);
	    $stmt->bindParam(':first_name', $first_name, PDO::PARAM_STR);
	    $stmt->bindParam(':last_name', $last_name, PDO::PARAM_STR);
	    $stmt->bindParam(':address', $address, PDO::PARAM_STR);
	    $stmt->bindParam(':phone_number', $phone_number);
	    $stmt->bindParam(':money', $money, PDO::PARAM_STR);
	    $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
	    $stmt->execute();
	
	    // Redirect back to profile page after updating
	    header("Location: profile.php");
	    exit();
	}
?>

<!DOCTYPE html>
<html>
<head>
	<link href="assets/css/bootstrap.min.css" rel="stylesheet">
	<link href="assets/css/dataTables.min.css" rel="stylesheet">
	<link rel="stylesheet" href="assets/css/font-awesome.min.css">
	<title>Better Buys: User Profile</title>
</head>
<body>
	<nav class="navbar navbar-expand-lg bg-body-tertiary">
		<div class="container-fluid">
			<a class="navbar-brand" href="/" id="btnLogo">Better Buys</a>
			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>

			<div class="collapse navbar-collapse" id="navbarSupportedContent">
				<ul class="navbar-nav me-auto mb-2 mb-lg-0">
					<li class="nav-item">
						<a class="nav-link" aria-current="page" href="/" id="btnHome">Home</a>
					</li>

					<li class="nav-item"><a class="nav-link" href="shopping_cart.php">Shopping Cart</a></li>
					<li class="nav-item"><a class="nav-link active" href="profile.php">Profile</a></li>
					<li class="nav-item"><a class="nav-link" href="logout.php">Log Out</a></li>

					<?php
						if ($is_admin) {
							echo '<li class="nav-item"><a class="nav-link text-danger" href="admin.php">Admin panel</a></li>';
						}
					?>
				</ul>

				<p class="my-2 me-2">You're logged in as: <?= $_SESSION['login'] ?></p>
			</div>
		</div>
	</nav>

	<div class="container">
		<form class="row g-3 mt-2 mb-3" method="post">
			<div class="col-12">
				<label class="form-label" for="inputFirstName">First Name</label>
				<input id="inputFirstName" class="form-control" type="text" value="<?=$user['firstName']?>" name="first_name">
			</div>

			<div class="col-12">
				<label class="form-label" for="inputLastName">Last Name</label>
				<input id="inputLastName" class="form-control" type="text" value="<?=$user['lastName']?>" name="last_name">
			</div>

			<div class="col-12">
				<label class="form-label" for="inputEmail">Email</label>
				<input id="inputEmail" class="form-control" type="email" value="<?=$user['emailAddress']?>">
			</div>

			<div class="col-12">
				<label class="form-label" for="inputPhysicalAddress">Physical Address</label>
				<input id="inputPhysicalAddress" class="form-control <?= ($user['physicalAddress'] ? '' : 'is-invalid') ?>" type="text" value="<?=$user['physicalAddress']?>" name="address">
				<div class="invalid-feedback">
					A valid address is required to place an order.
				</div>
			</div>

			<div class="col-12">
				<label class="form-label" for="inputPhone">Phone</label>
				<input id="inputPhone" class="form-control" type="text" value="<?=$user['e164PhoneNumber']?>" name="phone_number">
			</div>

			<div class="col-12">
				<label class="form-label" for="inputMoney">Money</label>
				<input id="inputMoney" class="form-control <?= ((float)$user['money'] <= 0 ? 'is-invalid' : '') ?>" type="text" value="<?=$user['money']?>" name="money">
				<div class="invalid-feedback">
					Additional funds are required to place an order.
				</div>
			</div>

			<div class="col-12">
				<button type="submit" class="btn btn-primary">Edit Profile</button>
			</div>
		</form>

		<hr>

		<div class="row">
			<div class="col">
				<?php
					// Display previous purchases
					echo "<h3>Previous Purchases</h3>";
					// Fetch user's previous purchases from the TransactionItem table
					$sql = 'SELECT ti.productId, tr.purchaseDate, ti.quantity, tr.subtotal, tr.shippingFee 
					        FROM TransactionItem ti
					        INNER JOIN Transaction tr ON ti.transactionId = tr.id
					        WHERE tr.userId = :user_id';
					
					$stmt = $pdo->prepare($sql);
					$stmt->bindParam(':user_id', $user_to_show, PDO::PARAM_INT);
					$stmt->execute();
					$purchases = $stmt->fetchAll(PDO::FETCH_ASSOC);
					
					if(count($purchases) > 0) {
						echo '<table id="tablePreviousPurchases">';
							echo '<thead>';
								echo '<tr>';
									echo '<th>Product</th>';
									echo '<th>Date</th>';
									echo '<th>Quantity</th>';
									echo '<th>Subtotal</th>';
									echo '<th>Shipping Fee</th>';
								echo '</tr>';
							echo '</thead>';

							echo '<tbody>';
					    		foreach($purchases as $purchase) {
					    		    // Fetch product information for each purchase
					    		    $product_id = $purchase['productId'];
					    		    $product_sql = 'SELECT name FROM Product WHERE id=:product_id';
					    		    $product_stmt = $pdo->prepare($product_sql);
					    		    $product_stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
					    		    $product_stmt->execute();
					    		    $product = $product_stmt->fetch(PDO::FETCH_ASSOC);
					
					    		    echo "<tr>";
					    		    	echo "<td>" . $product['name'] . "</td>";
					    		    	echo "<td>" . $purchase['purchaseDate'] . "</td>";
					    		    	echo "<td>" . $purchase['quantity'] . "</td>";
					    		    	echo "<td>$" . $purchase['subtotal'] . "</td>";
					    		    	echo "<td>$" . $purchase['shippingFee'] . "</td>";
					    		    echo "</tr>";
					    		}
					    echo "</tbody></table>";
					} else {
					    echo "No previous purchases found.";
					}

					// Allow user to edit their profile
					// if ($is_admin || $_SESSION['user_id'] == $user_to_show) {
					// 	echo '<a class="btn btn-primary" href="edit_profile.php?user_id=' . $user_to_show . '">Edit Profile</a>';
					// }
					
					// Delete account link (only visible to logged-in user)
				?>
			</div>
		</div>

		<?php
			if ($_SESSION['user_id'] == $user_to_show) {
				echo '
				<div class="row mt-3">
					<div class="col">
						<div class="alert alert-danger" role="alert">
							<h4 class="alert-heading">Danger Zone</h4>
							<p>Account deletion is irreversible, make sure you are certain before clicking the following button!</p>
							<hr>
							<a class="btn btn-danger" href="account_delete.php?user_id=' . $user_to_show . '">Delete Account</a>
						</div>
					</div>
				</div>';
			}
		?>
	</div>

	<script src="assets/js/bootstrap.bundle.min.js"></script>
	<script src="assets/js/jquery-3.7.1.js"></script>
	<script src="assets/js/dataTables.min.js"></script>

	<script>
		let table = new DataTable('#tablePreviousPurchases');
	</script>
</body>
</html>
