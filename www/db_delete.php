<?php

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
