<!DOCTYPE html>

<html>
<head>
</head>
<body>
<a href="admin.php">Admin page</a>
	<?php
		$pdo = require_once 'connect.php';
		$product_id = filter_input(INPUT_POST, 'product_id');

		// retrieve a single product
		$sql = 'SELECT name, description, price, discount, quantity FROM Product WHERE id=' . $product_id . ';';
		
		$statement = $pdo->query($sql);
		
		//get a product
		$product = $statement->fetch(PDO::FETCH_ASSOC);

		$user_wants_to_save = filter_input(INPUT_POST, 'user_wants_to_save');
		
		$new_product_name = filter_input(INPUT_POST, 'new_product_name');
		$new_product_desc = filter_input(INPUT_POST, 'new_product_desc');
		$new_product_price = filter_input(INPUT_POST, 'new_product_price');
		$new_product_discount = filter_input(INPUT_POST, 'new_product_discount');
		$new_product_quantity = filter_input(INPUT_POST, 'new_product_quantity');

		$product_display_name = isset($new_product_name) ? $new_product_name : $product['name'];
		$product_display_desc = isset($new_product_desc) ? $new_product_desc : $product['description'];
		$product_display_price = isset($new_product_price) ? $new_product_price : $product['price'];
		$product_display_discount = isset($new_product_discount) ? $new_product_discount : $product['discount'];
		$product_display_quantity = isset($new_product_quantity) ? $new_product_quantity : $product['quantity'];
	?>
	
	<form action="product_edit.php" method="post">
		<label for="textProductName">Name:</label><br>
		<input type="text" id="textProductName" name="new_product_name" <?php echo 'value="' . $product_display_name . '"'; ?> >
		<br>
	
		<label for="textProductDescription">Description:</label><br>
		<input type="text" id="textProductDescription" name="new_product_desc" <?php echo 'value="' . $product_display_desc . '"'; ?> >
		<br>

		<label for="textProductPrice">Price:</label><br>
		<input type="text" id="textProductPrice" name="new_product_price" <?php echo 'value="' . $product_display_price . '"'; ?> >
		<br>

		<label for="textProductDiscount">Discount:</label><br>
		-&nbsp;<input type="text" id="textProductDiscount" name="new_product_discount" <?php echo 'value="' . $product_display_discount . '"'; ?> > (will be subtracted from the base price)
		<br>

		<label for="textProductQuantity">Quantity in stock:</label><br>
		<input type="text" id="textProductQuantity" name="new_product_quantity" <?php echo 'value="' . $product_display_quantity . '"'; ?> >
		<br>
		<br>

		<input type="hidden" name="product_id" <?php echo 'value="' . $product_id . '"'; ?> >
		<input type="submit" name="user_wants_to_save" value="Commit changes to database">
	</form>

	<hr>

	<h1>Product images</h1>

	<hr>


	<?php
		$sql = 'SELECT id,filePath, priority FROM ProductImage WHERE productId=' . $product_id;
		
		$statement = $pdo->query($sql);
		
		// get all product images
		$product_images = $statement->fetchAll(PDO::FETCH_ASSOC);
		
		if ($product_images) {
			echo "<table>";
				echo "<tr>";
					echo "<th>Id</th>";
					echo "<th>Image</th>";
					echo "<th>File path</th>";
					echo "<th>Priority</th>";
					echo "<th>Delete?</th>";
				echo "</tr>";

				$delete_image = filter_input(INPUT_POST, 'delete_image');
				$image_id = filter_input(INPUT_POST, 'image_id');
				$image_priority = filter_input(INPUT_POST, 'image_priority');

				// show the products as a table
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

					echo "<tr>";
						echo '<td>' . $product_image['id'] . '</td>';
						echo '<td><img style="max-height: 300px; max-width: 300px; height: auto; width: auto;" src="' . $product_image['filePath'] . '"></td>';
						echo '<td>' . $product_image['filePath'] . '</td>';

						echo '<td>' .
							'<form action="product_edit.php" method="post">' .
								'<input type="text" name="image_priority" value="' . $priority . '">' .
								'<input type="hidden" name="image_id" value="' . $product_image['id'] . '">' .
								'<input type="hidden" name="product_id" value="' . $product_id . '">' .
								'<br>' .
								'<input type="submit" name="edit_image" value="Save new priority">' .
							'</form>' .
						'</td>';	

						echo '<td>' .
							'<form action="product_edit.php" method="post">' .
								'<input type="hidden" name="image_id" value="' . $product_image['id'] . '">' .
								'<input type="hidden" name="image_filepath" value="' . $product_image['filePath'] . '">' .
								'<input type="hidden" name="product_id" value="' . $product_id . '">' .
								'<br>' .
								'<input type="submit" name="delete_image" value="Delete">' .
							'</form>' .
						'</td>';	
					echo "</tr>";
				}
			echo "</table>";
		} else {
			echo "No products images found!";
		}
	?>

	<hr>

	<form action="product_edit.php" method="post" enctype="multipart/form-data">
		<label for="fileToUpload">Select image to upload:</label><br>
		<input type="file" name="fileToUpload" id="fileToUpload"><br>
		<br>

		<input type="submit" name="upload_image" value="Upload Image">

		<input type="hidden" name="product_id" <?php echo 'value="' . $product_id . '"'; ?> >
	</form>

<?php
		if($_SERVER['REQUEST_METHOD'] == "POST")
		{
			$delete_image = filter_input(INPUT_POST, 'delete_image');
			$edit_image = filter_input(INPUT_POST, 'edit_image');
			$save_product_changes = filter_input(INPUT_POST, 'save_product_changes');
			$upload_image = filter_input(INPUT_POST, 'upload_image');

			if (isset($delete_image)) {
				// delete both from disk and database
				$image_filepath = filter_input(INPUT_POST, 'image_filepath');
				if (file_exists($image_filepath)) {
					unlink($image_filepath);
				}
				
				$sql = 'DELETE FROM ProductImage WHERE id=' . $image_id . ';';
				try {
					if ($pdo->exec($sql) == 1) {
						echo '<p>Product image ' . $image_id . ' deleted successfully.</p>';
					} else {
						echo '<p>Product image ' . $image_id . ' failed to be deleted.  (Does it exist?)</p>';
					}
				}
				catch(PDOException $e) {
					echo $e->getMessage();
				}
			} else if (isset($edit_image)) {
				echo 'Setting product image with id=' . $image_id . ' to priority=' . $image_priority;

				if (is_numeric($image_priority) && (int)$image_priority > 0) {
					$sql = 'UPDATE ProductImage SET priority = :priority WHERE id=:id';
					$statement = $pdo->prepare($sql);
					$statement->execute([
						':priority' => (int)$image_priority,
						':id' => (int)$image_id
					]);
					

				} else {
					echo '<h1>Image priority must be a positive integer.</h1>';
				}
			} else if (isset($upload_image)) {
				$temp_file = $_FILES["fileToUpload"]["tmp_name"];
				if (strlen($temp_file) == 0) {
					return;
				}

				$target_dir = "/images/" . $product_id . '/';
				$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);

				// Check if image file is a actual image or fake image
				$allowed_formats = array('image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/tiff');
				$mimetype = mime_content_type($temp_file);

				if(in_array($mimetype, $allowed_formats)) {
					echo "File is an image - " . $mimetype . ".";
				} else {
					echo "File was not an allowed format - " . $mimetype . ".";
					return;
				}

				// Check if file already exists
				// TODO: Just overwrite the file if it already exists, nbd
				// if (file_exists($target_file)) {
				// 	echo "Sorry, file already exists.";
				// 	return;
				// }
				
				if (!file_exists($target_dir)) {
					mkdir($target_dir, 0755);
				}
			
				if (move_uploaded_file($temp_file, $target_file)) {
					// TODO: Add a way to set priority (thumbnails with drag-n-drop would be awesome).
					$sql = 'INSERT INTO ProductImage (filePath, productId, priority) VALUES (:filePath, :productId, :priority)';
						
					$statement = $pdo->prepare($sql);
						
					$statement->execute([
						':filePath' => $target_file,
						':productId' => $product_id,
						':priority' => 0
					]);

					echo "The file ". htmlspecialchars( basename( $_FILES["fileToUpload"]["name"])). " has been uploaded.";
				} else {
					echo "Sorry, there was an error uploading your file.";
				}
			}

			if (isset($user_wants_to_save)) {
				$discount_error = false;

				if ($new_product_discount == null || strlen($new_product_discount) == 0) {
					// Product is not on sale
					$new_product_discount = null;
				 	echo '<p>Discount removed.</p>';
				} else {
					// Product may or may not be on sale, let's double check
					
					// Check if user set the discount to 0
					if (bccomp($new_product_discount, 0) == 0) {
						// If the discount is 0, the product is not on sale.
						$new_product_discount = null;
				 		echo '<p>Discount removed.</p>';
					} else {
						// But if it is on sale, let's make sure the user put in something reasonable

						// Do base_price - discount
						// if result is 0 or negative, error out
						// Otherwise, we are good to go, go ahead and set discount in the DB to the difference
						$difference = bcsub($new_product_price, $new_product_discount);

						// Discount would make the product's new price either free or negative, error out
						if (bccomp($difference, "0") <= 0) {
							$discount_error = true;
						} else {
							// Base price - discount was greater than $0.00, we are good
							echo '<h1>Effective price: ' . $difference . '</h1>';
						}
					}
				}

				if ($discount_error) {
					echo '<h1>Given discount was too high.  Discount cannot be greater than or equal to the base price.</h1>';
				} else if (strlen($new_product_name) == 0) {
					echo '<h1>Product name cannot be blank.</h1>';
				} else {
					$sql = 'UPDATE Product SET name = :name, description = :desc, price = :price, discount = :discount, quantity = :quantity WHERE id=:id';

					$statement = $pdo->prepare($sql);


					$statement->execute([
						':name' => $new_product_name,
						':desc' => $new_product_desc,
						':price' => $new_product_price,
						':discount' => $new_product_discount,
						':quantity' => $new_product_quantity,
						':id' => $product_id
					]);

					echo 'The product id ' . $product_id . ' was edited!';
				}
			}
		}
?>
</body>
</html>

