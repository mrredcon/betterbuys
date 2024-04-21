<?php
	$pdo = require 'connect.php';
	$sql  = 'SELECT id, emailAddress, confirmationCode, dateCreated FROM PendingUser';

	$statement = $pdo->query($sql);
	
	// get all users 
	$pending_users = $statement->fetchAll(PDO::FETCH_ASSOC);
	
	$table = '';
	if ($pending_users) {
		$table .= <<<EOD
			<div class="table-responsive small my-2">
				<table class="table table-striped table-sm">
					<thead>
						<tr>
							<th>Id</th>
							<th>Email address</th>
							<th>Confirmation code</th>
							<th>Date created</th>
							<th>Delete?</th>
						</tr>
					</thead>
		EOD;

		// show the users as a table
		foreach ($pending_users as $pending_user) {
			$table .= <<<EOD
				<tr>
					<td>{$pending_user['id']}</td>
					<td>{$pending_user['emailAddress']}</td>
					<td>{$pending_user['confirmationCode']}</td>
					<td>{$pending_user['dateCreated']}</td>
	
					<td>
						<form method="post" action="delete.php">
							<input type="hidden" name="id" value="{$pending_user['id']}">
							<input type="hidden" name="table_name" value="PendingUser">
							<input type="submit" value="Delete">
							<input type="hidden" name="redirect" value="/admin.php?page=pendingusers">
						</form>
					</td>
				</tr>
			EOD;
		}
		$table .= '</table></div>';
	} else {
		$table = "No pending users found!";
	}

	$html = <<<EOD
		<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
			<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-0 border-bottom">
				<h1 class="h2">Pending Users</h1>
			</div>

			{$table}
		</main>
	EOD;
	return $html;
?>
