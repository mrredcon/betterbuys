<?php
	$pdo = require_once 'connect.php';

	$table_name = filter_input(INPUT_POST, 'table_name');
	$id = filter_input(INPUT_POST, 'id');

	if ($table_name && $id) {
		$deleted = $pdo->query('DELETE FROM ' . $table_name . ' WHERE id=' . $id);
		echo 'Deleted ' . $deleted->rowCount() . ' rows from the database.<br>';
	} else {
		echo 'Missing table name and/or id to delete.';
	}
?>
