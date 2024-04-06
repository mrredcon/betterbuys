<?php
	session_start();

	$is_logged_in=false;
	$username='';
	if(array_key_exists('login', $_SESSION)) {
	    $is_logged_in=true;
	    $username=$_SESSION['login'];
	}

	$is_admin=false;
        if(array_key_exists('is_admin', $_SESSION) && $_SESSION['is_admin']) {
	    $is_admin=true;
        }

	$pdo = require_once 'connect.php';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
    <title>Welcome to Better Buys!</title>
    <style>
        .menu-item {
            margin-right: 20px;
        }
    </style>
</head>

<body>
	<nav class="navbar navbar-expand-lg bg-body-tertiary">
		<div class="container-fluid">
			<a class="navbar-brand" href="#">Better Buys</a>
			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>

			<div class="collapse navbar-collapse" id="navbarSupportedContent">
				<ul class="navbar-nav me-auto mb-2 mb-lg-0">
					<li class="nav-item">
						<a class="nav-link active" aria-current="page" href="#">Home</a>
					</li>

					<?php
						if ($is_logged_in) {
							echo '<li class="nav-item"><a class="nav-link" href="profile.php">View Profile</a></li>';
							echo '<li class="nav-item"><a class="nav-link" href="shopping_cart.php">Shopping Cart</a></li>';
							echo '<li class="nav-item"><a class="nav-link" href="logout.php">Log Out</a></li>';

							if ($is_admin) {
								echo '<li class="nav-item"><a class="nav-link text-danger" href="admin.php">Admin panel</a></li>';
							}

						} else {
							echo '<li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>';
							echo '<li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>';
						}
					?>
				</ul>

				<?php
					if ($is_logged_in) {
						echo '<p class="my-2 me-2">You' . "'" . 're logged in as: ' . $_SESSION['login'] . '</p>';
					}
				?>

				<form class="d-flex" role="search">
					<input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
					<button class="btn btn-outline-success" type="submit"><i class="icon-search"></i></button>
				</form>
			</div>
		</div>
	</nav>

	<div class="container text-center my-3">
		<div class="row mb-3">
			<button type="button" class="btn btn-primary w-auto me-3" id="btnSortPrice" value="asc">Sort by price</button>
			<button type="button" class="btn btn-primary w-auto me-3" id="btnSortName" value="asc">Sort by name</button>
		</div>

		<div id="productcontainer" class="row"></div>
	</div>
	<script src="assets/js/bootstrap.bundle.min.js"></script>
	<script src="assets/js/jquery-3.7.1.js"></script>
	<script src="assets/js/betterbuys.js"></script>
	</body>
</html>
