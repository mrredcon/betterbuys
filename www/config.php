<?php

function get_config()
{
	$config = parse_ini_file($_SERVER['BB_CONFIG_FILE']);
	if (!$config) {
		die('Better Buys configuration file missing or invalid. Try copying bb_config.ini.example to bb_config.ini and filling out its options.');
	}
	return $config;
}

return get_config();
