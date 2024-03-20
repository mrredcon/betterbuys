<!DOCTYPE html>

<hmtl>

<head>
</head>
<body>
<a href="admin.php">Admin page</a>

<form>

<?php
	$pdo = require_once 'connect.php';
	
	// insert a single product
	$sql = 'SELECT name, description FROM Product WHERE id='.$_POST["product_id"].';';
    
	
	$statement = $pdo->query($sql);
    
    //get a product
    $product = $statement->fetch(PDO::FETCH_ASSOC);
	?>

    <label for="textProductName">Name:</label><br>
    
    <input type="text" id="textProductName" name="product_name" <?php echo 'value="' . $product['name'] . '"'; ?> >
    
 

</form>

</body>
</html>


