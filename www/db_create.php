<?php

$SCHEMA_FILE = "/schema";

try {
	$config = require_once 'config.php';

	$host = $config['db_hostname'];
	$port = $config['db_port'];
	$dsn = "mysql:host=$host;port=$port;charset=UTF8";
	
	$user = $config['db_username'];
	$password = file_get_contents($_SERVER["DB_PASSWORD_FILE"]);
	
	$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];
	
	$pdo = new PDO($dsn, $user, $password, $options);

	$db   = $config['db_name'];
	$schemas = $pdo->query("SELECT count(*) FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$db';");
	if ($schemas->fetchColumn() > 0) {
		echo 'Database already exists!';
	} else {
		$pdo->exec("CREATE DATABASE $db;");
		$pdo->exec("USE $db;");

		$pdo->exec(file_get_contents($SCHEMA_FILE));

		$pdo->exec("INSERT INTO Product VALUES ( 1, 'The First Product', 'A product that may be purchased.', 0, 0, 0 );");
		echo "Database created successfully.";
	}
} catch(PDOException $e) {
	echo $e->getMessage();
}

$pdo = null;
?>
