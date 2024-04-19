<?php
function connect()
{
	$SCHEMA_FILE = "/schema";

	$config = require_once 'config.php';

	$host = $config['db_hostname'];
	$port = $config['db_port'];
	$db   = $config['db_name'];
	$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=UTF8";

	$user = $config['db_username'];
	$password = file_get_contents($_SERVER["DB_PASSWORD_FILE"]);

	$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];

	try {
		return new PDO($dsn, $user, $password, $options);
	} catch (PDOException $e) {
		//echo var_dump($e);
		//echo '<pre>' . var_export($e, true) . '</pre>';
		if ($e->getCode() === 1049) {
			// Database does not exist, lets try to create it
			$dsn = "mysql:host=$host;port=$port;charset=UTF8";
			$pdo = new PDO($dsn, $user, $password, $options);
			$pdo->exec("CREATE DATABASE $db;");
			$pdo->exec("USE $db;");

			$pdo->exec(file_get_contents($SCHEMA_FILE));

			$pdo->exec("INSERT INTO Product VALUES ( 1, 'The First Product', 'A product that may be purchased.', 0, 0, 0 );");

			$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=UTF8";
			return new PDO($dsn, $user, $password, $options);
		} else {
			die($e->getMessage());
		}
	}
}

return connect();

