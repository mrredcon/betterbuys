<?php

function connect()
{
	try {
		$host = $_SERVER["DB_HOSTNAME"];
		$port = $_SERVER["DB_PORT"];
		$db   = $_SERVER["DB_NAME"];
		$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=UTF8";

		$user = $_SERVER["DB_USERNAME"];
		$password = file_get_contents($_SERVER["DB_PASSWORD_FILE"]);

		$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];

		return new PDO($dsn, $user, $password, $options);
	} catch (PDOException $e) {
		die($e->getMessage());
	}
}

return connect();
