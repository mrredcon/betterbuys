<?php

try {
	$host = $_SERVER["DB_HOSTNAME"];
	$port = $_SERVER["DB_PORT"];
	$dsn = "mysql:host=$host;port=$port;charset=UTF8";
	
	$user = $_SERVER["DB_USERNAME"];
	$password = file_get_contents($_SERVER["DB_PASSWORD_FILE"]);
	
	$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];
	
	$pdo = new PDO($dsn, $user, $password, $options);

	$db   = $_SERVER["DB_NAME"];
	$schemas = $pdo->query("SELECT count(*) FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$db';");
	if ($schemas->fetchColumn() > 0) {
		$pdo->exec("DROP DATABASE $db;");
		echo 'Database deleted successfully.';
	} else {
		echo 'Database does not exist yet!';
	}
} catch(PDOException $e) {
	echo $e->getMessage();
}

$pdo = null;
?>
