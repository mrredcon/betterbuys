<?php

function connect()
{
	try {
		$host = $_ENV["DB_HOSTNAME"];
		$port = $_ENV["DB_PORT"];
		$db   = $_ENV["DB_NAME"];
		$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=UTF8";

		$user = $_ENV["DB_USERNAME"];
		$password = file_get_contents($_ENV["DB_PASSWORD_FILE"]);

		$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];

		return new PDO($dsn, $user, $password, $options);
	} catch (PDOException $e) {
		die($e->getMessage());
	}
}

return connect();
