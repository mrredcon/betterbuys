<?php
	$pdo = require_once 'connect.php';

	$table_name = filter_input(INPUT_POST, 'table_name');
	$id = filter_input(INPUT_POST, 'id');
	$redirect = filter_input(INPUT_POST, 'redirect');

	if ($table_name && $id) {
		$deleted = $pdo->query('DELETE FROM ' . $table_name . ' WHERE id=' . $id);
		if ($redirect) {
			header('Location: ' . $redirect);
		} else {
			echo 'Deleted ' . $deleted->rowCount() . ' rows from the database.<br>';
		}
	} else {
		echo 'Missing table name and/or id to delete.';
	}
?>
