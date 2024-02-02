 <?php
$servername = "mysql";
$username = "root";
$password = "helloworld";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

function runQuery($sql) {
	global $conn;

	// Run and check query
	if ($conn->query($sql) === TRUE) {
	  echo "Query \"" . $sql . "\" ran successfully.<br>";
	} else {
	  echo "Error running query \"" . $sql . "\" : " . $conn->error . "<br>";
	}
}

$result = $conn->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = 'betterbuys';");
if (mysqli_num_rows($result) > 0) {
	echo 'betterbuys database already exists!';
} else {
	runQuery("CREATE DATABASE betterbuys;");
	runQuery("USE betterbuys;");
	runQuery("CREATE TABLE Product ( `Id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, `Name` varchar(255), `Description` varchar(255) );");
	runQuery("INSERT INTO Product VALUES ( 1, 'The First Product', 'A product that may be purchased.' );");
}

$conn->close();
?> 
