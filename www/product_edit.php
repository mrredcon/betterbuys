<?php
	if($_SERVER['REQUEST_METHOD'] !== "POST") {
		return 'This page requires an HTTP POST in order to function.';
	}

	$pdo = require_once 'connect.php';
	$product_id = filter_input(INPUT_POST, 'product_id');

	$sql = 'SELECT name, description, price, discount, quantity FROM Product WHERE id=' . $product_id . ';';
	
	$statement = $pdo->query($sql);
	
	$product = $statement->fetch(PDO::FETCH_ASSOC);

	// load product data
	$name        = filter_input(INPUT_POST, 'name')        ?: $product['name'];
	$description = filter_input(INPUT_POST, 'description') ?: $product['description'];
	$price       = filter_input(INPUT_POST, 'price')       ?: $product['price'];
	$discount    = filter_input(INPUT_POST, 'discount')    ?: $product['discount'];

	$user_wants_to_save = filter_input(INPUT_POST, 'user_wants_to_save');

	function build_image_table($pdo, $product_id) {
		$sql = 'SELECT id,filePath, priority FROM ProductImage WHERE productId=' . $product_id;
		
		$statement = $pdo->query($sql);
		
		// get all product images
		$product_images = $statement->fetchAll(PDO::FETCH_ASSOC);
		$html = '';
		
		if ($product_images) {
			$html .= '
			<div class="table-responsive small">
				<table class="table table-striped table-sm">
				<thead>
					<tr>
						<th>Id</th>
						<th>Image</th>
						<th>File path</th>
						<th>Priority</th>
						<th>Delete?</th>
					</tr>
				</thead>';
		
			$delete_image = filter_input(INPUT_POST, 'delete_image');
			$image_id = filter_input(INPUT_POST, 'image_id');
			$image_priority = filter_input(INPUT_POST, 'image_priority');
		
			// show the images as a table
			foreach ($product_images as $product_image) {
				$priority = $product_image['priority'];
		
				if (isset($image_id) && (int)$image_id == (int)$product_image['id']) {
					if (isset($delete_image)) {
						continue;
					}
		
					if (isset($image_priority)) {
						$priority = $image_priority;
					}
				}
		
				$html .= <<<EOD
					<tr>
						<td>{$product_image['id']}</td>
						<td><img style="max-height: 300px; max-width: 300px; height: auto; width: auto;" src="{$product_image['filePath']}"></td>
						<td>{$product_image['filePath']}</td>
		
						<td>
							<form method="post">
								<input type="text" name="image_priority" value="$priority">
								<input type="hidden" name="image_id" value="{$product_image['id']}">
								<input type="hidden" name="product_id" value="$product_id">
								<br>
								<input type="submit" name="edit_image" value="Save new priority">
							</form>
						</td>
		
						<td>
							<form method="post">
								<input type="hidden" name="image_id" value="{$product_image['id']}">
								<input type="hidden" name="image_filepath" value="{$product_image['filePath']}">
								<input type="hidden" name="product_id" value="$product_id">
								<br>
								<input type="submit" name="delete_image" value="Delete">
							</form>
						</td>
					</tr>
				EOD;
			}

			$html .= "</table></div>";
			return $html;
		}

		return null;
	}

	function build_body($pdo, $name, $description, $price, $discount, $product_id) {
		$image_table = build_image_table($pdo, $product_id);
		$form = <<<EOD
		<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
			<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-0 border-bottom">
				<h1 class="h2">Edit Product</h1>
			</div>
		
			<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
				<form method="post">
					<div class="mb-3">
						<label for="textProductName" class="form-label">Name:</label><br>
						<input type="text" id="textProductName" class="form-control" name="name" value="$name">
					</div>
				
					<div class="mb-3">
						<label for="textProductDescription" class="form-label">Description:</label><br>
						<input type="text" id="textProductDescription" class="form-control" name="description" value="$description">
					</div>
		
					<div class="mb-3">
						<label for="textProductPrice" class="form-label">Price:</label><br>
						<input type="text" id="textProductPrice" class="form-control" name="price" value="$price">
					</div>
		
					<div class="mb-3">
						<label for="textProductDiscount" class="form-label">Discount:</label><br>
						<input type="text" id="textProductDiscount" class="form-control" name="discount" value="$discount"> (will be subtracted from the base price)
					</div>
		
					<input type="hidden" name="product_id" value="$product_id">
					<input type="submit" class="btn btn-primary mb-2" name="user_wants_to_save" value="Save Changes">
				</form>
			</div>
		
			<h1 class="h2">Product images</h1>
		
			<hr>
		
			<form action="product_edit.php" method="post" enctype="multipart/form-data">
				<label for="fileToUpload">Select image to upload:</label><br>
				<input type="file" name="fileToUpload" id="fileToUpload"><br>
				<br>
		
				<input type="submit" name="upload_image" value="Upload Image">
		
				<input type="hidden" name="product_id" value="$product_id">
			</form>

			{$image_table}
		</main>
		EOD;

		return $form;
	}
	
	function edit_product($pdo, $product_id, $name, $description, $price, $discount) {
		if ($discount == null || strlen($discount) == 0) {
			// Product is not on sale
			$discount = null;
		} else {
			// Product may or may not be on sale, let's double check
			
			// Check if user set the discount to 0
			if (bccomp($discount, 0) == 0) {
				// If the discount is 0, the product is not on sale.
				$discount = null;
			} else {
				// But if it is on sale, let's make sure the user put in something reasonable

				// Do base_price - discount
				// if result is 0 or negative, error out
				// Otherwise, we are good to go, go ahead and set discount in the DB to the difference
				$difference = bcsub($price, $discount);

				// Discount would make the product's new price either free or negative, error out
				if (bccomp($difference, "0") <= 0) {
					return 'Given discount was too high.  Discount cannot be greater than or equal to the base price.';
				}
			}
		}

		if (strlen($name) === 0) {
			return 'Product name cannot be blank.';
		} else {
			$sql = 'UPDATE Product SET name = :name, description = :desc, price = :price, discount = :discount, quantity = :quantity WHERE id=:id';

			$statement = $pdo->prepare($sql);

			$statement->execute([
				':id' => $product_id,
				':name' => $name,
				':desc' => $description,
				':price' => $price,
				':discount' => $discount,
				':quantity' => 0
			]);
		}

		return null;
	}

	function delete_image($pdo, $image_id) {
		// delete both from disk and database
		$image_filepath = filter_input(INPUT_POST, 'image_filepath');
		if (file_exists($image_filepath)) {
			unlink($image_filepath);
		}
		
		$sql = 'DELETE FROM ProductImage WHERE id=' . $image_id . ';';
		try {
			if ($pdo->exec($sql) == 0) {
				return 'Product image ' . $image_id . ' failed to be deleted.  (Does it exist?)';
			}

			return null;
		}
		catch(PDOException $e) {
			return $e->getMessage();
		}
	}

	function set_image_priority($pdo, $image_id, $priority) {
		if (is_numeric($priority) && (int)$priority >= 0) {
			$sql = 'UPDATE ProductImage SET priority = :priority WHERE id=:id';
			$statement = $pdo->prepare($sql);
			$statement->execute([
				':id' => (int)$image_id,
				':priority' => (int)$priority
			]);

			return null;
		}

		return 'Image priority must be zero or a positive integer.';
	}

	function upload_image($pdo, $product_id) {
		$temp_file = $_FILES["fileToUpload"]["tmp_name"];
		if (strlen($temp_file) == 0) {
			return;
		}

		$target_dir = "/images/" . $product_id . '/';
		$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);

		// Check if image file is a actual image or fake image
		$allowed_formats = array('image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/tiff');
		$mimetype = mime_content_type($temp_file);

		if(!in_array($mimetype, $allowed_formats)) {
			return "File was not an allowed format - " . $mimetype . ".";
		}

		if (!file_exists($target_dir)) {
			mkdir($target_dir, 0755);
		}
	
		if (move_uploaded_file($temp_file, $target_file)) {
			$sql = 'INSERT INTO ProductImage (filePath, productId, priority) VALUES (:filePath, :productId, :priority)';
				
			$statement = $pdo->prepare($sql);
				
			$statement->execute([
				':filePath' => $target_file,
				':productId' => $product_id,
				':priority' => 0
			]);

			return "The file ". htmlspecialchars( basename( $_FILES["fileToUpload"]["name"])). " has been uploaded.";
		} 

		return "Sorry, there was an error uploading your file.";
	}

	$image_id = filter_input(INPUT_POST, 'image_id');
	$delete_image = filter_input(INPUT_POST, 'delete_image');
	$edit_image = filter_input(INPUT_POST, 'edit_image');
	$upload_image = filter_input(INPUT_POST, 'upload_image');

	$save_product_changes = filter_input(INPUT_POST, 'save_product_changes');

	$draw_body = true;
	$error = null;

	if (isset($delete_image)) {
		$error = delete_image($pdo, $image_id);
	} else if (isset($edit_image)) {
		$priority = filter_input(INPUT_POST, 'image_priority');
		$error = set_image_priority($pdo, $image_id, $priority);
	} else if (isset($upload_image)) {
		$error = upload_image($pdo, $product_id);
	} else if (isset($user_wants_to_save)) {
		$error = edit_product($pdo, $product_id, $name, $description, $price, $discount);
		if ($err) {
			return $err;
		} else {
			header("Location: /admin.php?page=products");
			exit();
		}
	}
	
	if ($error) {
		$output = <<<EOD
		<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
			<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-0 border-bottom">
				<h1 class="h2">An error occurred while editing the product</h1>
			</div>

			<div class="alert alert-danger" role="alert">
				{$error}
			</div>
		EOD;

		return $output;
	}

	return build_body($pdo, $name, $description, $price, $discount, $product_id);
?>
