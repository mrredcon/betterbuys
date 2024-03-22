<!DOCTYPE html>

<html>
<head>
</head>
<body>
	<a href="admin.php">Admin page</a>

	<?php
	$pdo = require_once 'connect.php';

	// Magic number to set parentCategory to NULL
	$REMOVE_PARENT_FLAG = -1;

	$REMOVE_PARENT_LABEL = '[None]';

	// This helps prevent paradoxes
	// A parent can't be the child of one of their own children, right?
	function bannedIds($categoryId, $bannedSoFar) {
		global $pdo;
		$categoryId = (int)$categoryId;

		$sql = 'SELECT id FROM Category WHERE parentCategory=' . $categoryId;
		$statement = $pdo->query($sql);
		$children = $statement->fetchAll(PDO::FETCH_COLUMN, 0);

		if (count($children) == 0) {
			return array($categoryId);
		}

		foreach($children as $child) {
			$bannedSoFar = array_merge($bannedSoFar, bannedIds($child, $bannedSoFar));
		}

		array_push($bannedSoFar, $categoryId);
		return $bannedSoFar;
	}

	?>

	<?php
		$category_id = $_POST['category_id'];

		// retrieve a single category
		$sql = 'SELECT name, parentCategory FROM Category WHERE id=' . $category_id . ';';
		
		$statement = $pdo->query($sql);
		
		//get a category
		$category = $statement->fetch(PDO::FETCH_ASSOC);

		$user_wants_to_save = filter_input(INPUT_POST, 'user_wants_to_save');

		$new_category_name = filter_input(INPUT_POST, 'new_category_name');
		$new_category_parent = filter_input(INPUT_POST, 'new_category_parent');

		$category_display_name = isset($new_category_name) ? $new_category_name : $category["name"];
		$category_display_parent = isset($new_category_parent) ? (int)$new_category_parent : (int)$category["parentCategory"];
	?>
	
	<form action="category_edit.php" method="post">
		<label for="textCategoryName">Name:</label><br>
		<input type="text" id="textCategoryName" name="new_category_name" <?php echo 'value="' . $category_display_name . '"'; ?> >
	
		<br>
	
		<input type="hidden" name="category_id" <?php echo 'value="' . $category_id . '"'; ?> >
		<input type="hidden" name="user_wants_to_save" value="true">

		<label for="parentCategorySelector">Choose a parent category:</label>
		<select name="new_category_parent" id="parentCategorySelector">
			<?php
				$bannedIds = bannedIds($category_id, []);
				$sql = 'SELECT id, name FROM Category';
				$statement = $pdo->query($sql);
				$all_categories = $statement->fetchAll(PDO::FETCH_ASSOC);

				$is_selected = '';

				// If a category does not have a parent, then make sure the '[None]' menu item is pre-selected
				if ($category_display_parent == null) {
					// Trailing space intentionally kept
					$is_selected = 'selected ';
				}

				echo '<option ' . $is_selected . 'value="' . $REMOVE_PARENT_FLAG . '">' . $REMOVE_PARENT_LABEL . '</option>';

				foreach($all_categories as $cat)
				{
					if (!in_array((int)$cat['id'], $bannedIds))
					{
						$is_selected = '';

						if ((int)$cat['id'] == $category_display_parent)
						{
							// Trailing space intentionally kept
							$is_selected = 'selected ';
						}

						echo '<option ' . $is_selected . 'value="' . $cat['id'] . '">' . $cat['name'] . '</option>';
					}
				}
			?>
		</select>

		<input type="submit" value="Commit changes to database">
	</form>

	<?php
		if($_SERVER['REQUEST_METHOD'] == "POST")
		{
			$new_category_parent = (int)filter_input(INPUT_POST, 'new_category_parent');

			if ($new_category_parent == $REMOVE_PARENT_FLAG)
			{
				$new_category_parent = null;
			}

			if (isset($user_wants_to_save) and strlen($new_category_name) == 0)
			{
				echo '<h1>Category name cannot be blank.</h1>';
			}
			else if (isset($user_wants_to_save))
			{
				// update the category's name
				$sql = 'UPDATE Category SET name = :name, parentCategory = :parentCategory WHERE id=:id';
				
				$statement = $pdo->prepare($sql);
				
				$statement->execute([
					':name' => $new_category_name,
					':parentCategory' => $new_category_parent,
					':id' => $category_id
				]);
				
				echo 'The category id ' . $category_id . ' was edited!';
			}
		}
	?>
</body>
</html>

