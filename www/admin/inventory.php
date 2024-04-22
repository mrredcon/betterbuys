<?php
	$pdo = require 'connect.php';

	$sql = 'SELECT * FROM Store';
	$statement = $pdo->query($sql);
	$stores = $statement->fetchAll(PDO::FETCH_ASSOC);

	if (count($stores) == 0) {
		return '
			<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
				<p class="my-3">There are no stores to add inventory to. Try <a href="admin.php?page=stores">creating a store first.</a></p>
			</main>';
	}

	$sql = 'SELECT id, name, description, price, discount FROM Product';
	$statement = $pdo->query($sql);
	$products = $statement->fetchAll(PDO::FETCH_ASSOC);

	$store_options = '';
	foreach ($stores as $store) {
		$clean_store_id = htmlspecialchars($store['id']);
		$clean_store_name = htmlspecialchars($store['name']);

		$store_options .= '<option value="' . $clean_store_id . '">' . $clean_store_name . '</option>';
	}

	$product_options = '';
	foreach ($products as $product) {
		$clean_product_id = htmlspecialchars($product['id']);
		$clean_product_name = htmlspecialchars($product['name']);

		$product_options .= '<option value="' . $clean_product_id . '">' . $clean_product_name . '</option>';
	}

	$html = <<<EOD
	<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
		<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-0 border-bottom">
			<h1 class="h2">Inventory</h1>
		</div>

		<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
			<form action="addStoreInv.php" method="post">
				<div class="mb-3">
					<label for="selectStoreId" class="form-label">Store</label>
					<select class="form-select" id="selectStoreId" name="storeId">
						{$store_options}
					</select>
				</div>
	
				<div class="mb-3">
					<label for="selectProductId" class="form-label">Product</label>
					<select class="form-select" id="selectProductId" name="productId">
						{$product_options}
					</select>
				</div>
	
				<div class="mb-3">
					<label for="inputProductQuantity" class="form-label">Quantity</label>
					<input type="number" class="form-control" id="inputProductQuantity" name="quantity" min="1" required>
				</div>
	
				<button type="submit" class="btn btn-primary mb-2" value="Add to Inventory">Add to Inventory</button>
				<button type="button" class="btn btn-secondary mb-2" onclick="window.location='editStoreInv.php'">Edit Inventory</button>
			</form>
		</div>
	</main>
EOD;
	return $html;
?>
