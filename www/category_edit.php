<!DOCTYPE html>

<html>
<head>
</head>
<body>
<a href="admin.php">Admin page</a>


	<?php
		$pdo = require_once 'connect.php';
		$category_id = $_POST['category_id'];

		// retrieve a single category
		$sql = 'SELECT name FROM Category WHERE id=' . $category_id . ';';
		
		$statement = $pdo->query($sql);
		
		//get a category
		$category = $statement->fetch(PDO::FETCH_ASSOC);

		$user_wants_to_save = filter_input(INPUT_POST, 'user_wants_to_save');

		$new_category_name = filter_input(INPUT_POST, 'new_category_name');

		$category_display_name = '';
		if (isset($new_category_name))
		{
			$category_display_name = $new_category_name;
		}
		else
		{
			$category_display_name = $category["name"];
		}
	?>
	
	<form action="category_edit.php" method="post">
		<label for="textCategoryName">Name:</label><br>
		<input type="text" id="textCategoryName" name="new_category_name" <?php echo 'value="' . $category_display_name . '"'; ?> >
	
		<br>
	
		<input type="hidden" name="category_id" <?php echo 'value="' . $category_id . '"'; ?> >
		<input type="hidden" name="user_wants_to_save" value="true">

		<input type="submit" value="Commit changes to database">
	</form>

	<?php
		if($_SERVER['REQUEST_METHOD'] == "POST")
		{

			if (isset($user_wants_to_save) and strlen($new_category_name) == 0)
			{
				echo '<h1>Category name cannot be blank.</h1>';
			}
			else if (isset($user_wants_to_save))
			{
				// update the category's name
				$sql = 'UPDATE Category SET name = :name WHERE id=:id';
				
				$statement = $pdo->prepare($sql);
				
				$statement->execute([
					':name' => $new_category_name,
					':id' => $category_id
				]);
				
				echo 'The category id ' . $category_id . ' was edited!';
			}
		}
	?>
</body>
</html>

