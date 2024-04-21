<!-- CONTENT SECTION -->	
<?php
	$page = 'products';
	if (isset($_GET['page'])) {
	        $page = $_GET['page'];
	}

	$main_body = '';

	switch($page) {
		case 'product_edit':
			$main_body = include 'product_edit.php';
			break;
	        case 'stores':
	                $main_body = include 'admin/stores.php';
	                break;
		case 'store_edit':
			$main_body = include 'store_edit.php';
			break;
	        case 'inventory':
	                $main_body = include 'admin/inventory.php';
	                break;
	        case 'transactions':
	                $main_body = include 'admin/transactions.php';
	                break;
	        case 'discountcodes':
	                $main_body = include 'admin/discountcodes.php';
	                break;
	        case 'edit_discount':
	                $main_body = include 'edit_discount.php';
	                break;
	        case 'confirmedusers':
	                $main_body = include 'admin/confirmedusers.php';
	                break;
	        case 'pendingusers':
	                $main_body = include 'admin/pendingusers.php';
	                break;
	        case 'database':
	                $main_body = include 'admin/database.php';
	                break;
	        default:
	                $main_body = include 'admin/products.php';
	                break;
	}
?>
<!doctype html>
<html lang="en" data-bs-theme="auto">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content="">
		<meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
		<meta name="generator" content="Hugo 0.122.0">
		<title>Dashboard Template · Bootstrap v5.3</title>

		<link href="assets/css/bootstrap.min.css" rel="stylesheet">
		<link rel="stylesheet" href="assets/css/font-awesome.min.css">

		<!-- Custom styles for this template -->
		<link rel="stylesheet" href="assets/css/admin.css">
		<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
	</head>

	<body>
		<header class="navbar sticky-top bg-dark flex-md-nowrap p-0 shadow" data-bs-theme="dark">
			<a class="navbar-brand col-md-3 col-lg-2 me-0 px-3 fs-6 text-white" href="/admin.php">Better Buys</a>
		
			<ul class="navbar-nav flex-row d-md-none">
				<li class="nav-item text-nowrap">
					<button class="nav-link px-3 text-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSearch" aria-controls="navbarSearch" aria-expanded="false" aria-label="Toggle search">
						<svg class="bi"><use xlink:href="#search"/></svg>
					</button>
				</li>
				<li class="nav-item text-nowrap">
					<button class="nav-link px-3 text-white" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
						<svg class="bi"><use xlink:href="#list"/></svg>
					</button>
				</li>
			</ul>
		
			<div id="navbarSearch" class="navbar-search w-100 collapse">
				<input class="form-control w-100 rounded-0 border-0" type="text" placeholder="Search" aria-label="Search">
			</div>
		</header>

		<div class="container-fluid">
			<div class="row">
				<div class="sidebar border border-right col-md-3 col-lg-2 p-0 bg-body-tertiary">
					<div class="offcanvas-md offcanvas-end bg-body-tertiary" tabindex="-1" id="sidebarMenu" aria-labelledby="sidebarMenuLabel">
						<div class="offcanvas-header">
							<h5 class="offcanvas-title" id="sidebarMenuLabel">Better Buys</h5>
							<button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#sidebarMenu" aria-label="Close"></button>
						</div>
						<div class="offcanvas-body d-md-flex flex-column p-0 pt-lg-3 overflow-y-auto">
							<ul class="nav flex-column">
								<li class="nav-item">
									<a class="nav-link d-flex align-items-center gap-2" href="admin.php?page=products">
										<i class="icon-money"></i>
										Products
									</a>
								</li>
								<li class="nav-item">
									<a class="nav-link d-flex align-items-center gap-2" href="admin.php?page=stores">
										<i class="icon-building"></i>
										Stores
									</a>
								</li>
								<li class="nav-item">
									<a class="nav-link d-flex align-items-center gap-2" href="admin.php?page=inventory">
										<i class="icon-truck"></i>
										Inventory
									</a>
								</li>
								<li class="nav-item">
									<a class="nav-link d-flex align-items-center gap-2" href="admin.php?page=transactions">
										<i class="icon-credit-card"></i>
										Transactions
									</a>
								</li>
								<li class="nav-item">
									<a class="nav-link d-flex align-items-center gap-2" href="admin.php?page=discountcodes">
										<i class="icon-ticket"></i>
										Discount Codes
									</a>
								</li>
							</ul>
		
							<h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-body-secondary text-uppercase">
								<span>User Management</span>
							</h6>

							<ul class="nav flex-column mb-auto">
								<li class="nav-item">
									<a class="nav-link d-flex align-items-center gap-2" href="admin.php?page=confirmedusers">
										<i class="icon-user"></i>
										Confirmed Users
									</a>
								</li>
								<li class="nav-item">
									<a class="nav-link d-flex align-items-center gap-2" href="admin.php?page=pendingusers">
										<i class="icon-question"></i>
										Pending Users
									</a>
								</li>
							</ul>
		
							<hr class="my-3">
		
							<ul class="nav flex-column mb-auto">
								<li class="nav-item">
									<a class="nav-link d-flex align-items-center gap-2" href="admin.php?page=database">
										<i class="icon-hdd"></i>
										Database Maintenance
									</a>
								</li>
								<li class="nav-item">
									<a class="nav-link d-flex align-items-center gap-2" href="/">
										<i class="icon-home"></i>
										Return to Home Page
									</a>
								</li>
							</ul>
						</div>
					</div>
				</div>
		
				<!-- CONTENT SECTION -->	
				<?=$main_body?>
			</div>
		</div>
		<script src="assets/js/bootstrap.bundle.min.js"></script>
	</body>
</html>
