<?php

function connect()
{
	try {
		$config = require_once 'config.php';

		$host = $config['db_hostname'];
		$port = $config['db_port'];
		$db   = $config['db_name'];
		$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=UTF8";

		$user = $config['db_username'];
		$password = file_get_contents($_SERVER["DB_PASSWORD_FILE"]);

		$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];

		return new PDO($dsn, $user, $password, $options);
	} catch (PDOException $e) {
		die($e->getMessage());
	}
}

return connect();
