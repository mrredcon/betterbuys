<!DOCTYPE html>

<html>
<head>
</head>
<body>
	<a href="admin.php">Admin page</a>
	<br>

	<?php
		$pdo = require_once 'connect.php';
		$category_id = $_POST['category_id'];

		// delete a single category
		$sql = 'DELETE FROM Category WHERE id=' . $category_id . ';';
		
		try
		{
			if ($pdo->exec($sql) == 1)
			{
				echo '<p>Category id ' . $category_id . ' deleted successfully.</p>';
			}
			else
			{
				echo '<p>Category id ' . $category_id . ' failed to be deleted.  (Does it exist?)</p>';
			}
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
		}
	?>
</body>
</html>

