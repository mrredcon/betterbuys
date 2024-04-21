<?php
	$pdo = require 'connect.php';

	$sql = 'SELECT id, name, description, price, discount FROM Product';
	
	$statement = $pdo->query($sql);
	
	// get all products
	$products = $statement->fetchAll(PDO::FETCH_ASSOC);
	$table = '';
	
	if ($products) {
		$table = '
		<div class="table-responsive small">
			<table class="table table-striped table-sm">
				<thead>
					<tr>
						<th scope="col">Id</th>
						<th scope="col">Name</th>
						<th scope="col">Price</th>
						<th scope="col">Discount</th>
						<th scope="col">Description</th>
						<th scope="col">Edit</th>
						<th scope="col">Delete</th>
					</tr>
				</thead>';

		// show the products as a table
		foreach ($products as $product) {
			$table .= <<<EOD
			<tr>
				<td>{$product['id']}</td>
				<td>{$product['name']}</td>
				<td>{$product['price']}</td>
				<td>{$product['discount']}</td>
				<td>{$product['description']}</td>

				<td>
					<form method="post" action="admin.php?page=product_edit">
						<input type="hidden" name="product_id" value="{$product['id']}">
						<button type="submit" value="Edit" class="btn py-0"><i class="icon-edit"></i></button>
					</form>
				</td>

				<td>
					<form method="post" action="product_delete.php">
						<input type="hidden" name="product_id" value="{$product['id']}">
						<button type="submit" value="Delete" class="btn py-0"><i class="icon-remove"></i></button>
					</form>
				</td>
			</tr>
			EOD;
		}
		$table .= "</table></div>";
	} else {
		return "No products found!";
	}

	$output = <<<EOD
	<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
		<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-0 border-bottom">
			<h1 class="h2">Products</h1>
		</div>
	
		<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
			<form action="product_insert.php" method="post">
				<div class="mb-3">
					<label for="inputProductName" class="form-label">Product name</label>
					<input type="text" class="form-control" id="inputProductName" aria-describedby="helpProductName" name="name">
					<div id="helpProductName" class="form-text">This is what the customer will see when purchasing the product.</div>
				</div>
	
				<div class="mb-3">
					<label for="inputProductDescription" class="form-label">Product description</label>
					<input type="text" class="form-control" id="inputProductDescription" name="description">
				</div>
	
				<div class="mb-3">
					<label for="inputProductPrice" class="form-label">Price</label>
					<input type="text" class="form-control" id="inputProductPrice" name="price">
				</div>
	
				<button type="submit" class="btn btn-primary mb-2" value="Add product">Add Product</button>
			</form>
		</div>

		{$table}	
	</main>
	EOD;

	return $output;
?>
