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

// Delete database
$sql = "DROP DATABASE betterbuys";
if ($conn->query($sql) === TRUE) {
  echo "Database deleted successfully";
} else {
  echo "Error deleting database: " . $conn->error;
}

$conn->close();
?> 
