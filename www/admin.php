<!DOCTYPE html>
<html>
	<head>
	</head>
	<body>
		<a href="/index.html">Back to home page</a>
		<hr>

		<form action="db_create.php" method="post">
			<input type="submit" value="Create database">
		</form>

		<hr>

		<form action="db_delete.php" method="post">
			<input type="submit" value="Delete database">
		</form>

		<hr>

		<form action="product_insert.php" method="post">
			<label for="pname">Name:</label><br>
			<input type="text" id="pname" name="name"><br>

			<label for="pdesc">Description:</label><br>
			<input type="text" id="pdesc" name="description">

			<input type="submit" value="Add product">
		</form>

		<hr>

		<form action="upload.php" method="post" enctype="multipart/form-data">
			Select image to upload:<br>
			<input type="file" name="fileToUpload" id="fileToUpload"><br>
			<br>
			<input type="submit" value="Upload Image" name="submit"><br>
		</form>
	</body>
</html>
