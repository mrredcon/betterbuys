<!DOCTYPE html>

<html>
<head>
</head>
<body>
	<a href="admin.php">Admin page</a>
	<br>

	<?php
		$pdo = require_once 'connect.php';
		$product_id = $_POST['product_id'];

		// retrieve a single product
		$sql = 'DELETE FROM Product WHERE id=' . $product_id . ';';
		
		try
		{
			if ($pdo->exec($sql) == 1)
			{
				echo '<p>Product id ' . $product_id . ' deleted successfully.</p>';
			}
			else
			{
				echo '<p>Product id ' . $product_id . ' failed to be deleted.  (Does it exist?)</p>';
			}
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
		}
	?>
</body>
</html>

