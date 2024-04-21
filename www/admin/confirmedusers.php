<?php
	$pdo = require 'connect.php';
	$sql  = 'SELECT id, firstName, lastName, physicalAddress, emailAddress, money, isAdministrator, e164PhoneNumber FROM User';

	$statement = $pdo->query($sql);
	
	// get all users 
	$users = $statement->fetchAll(PDO::FETCH_ASSOC);
	
	$table = '';
	if ($users) {
		$table .= <<<EOD
			<div class="table-responsive small my-2">
				<table class="table table-striped table-sm">
					<thead>
						<tr>
							<th>Id</th>
							<th>First name</th>
							<th>Last name</th>
							<th>Physical address</th>
							<th>Email address</th>
							<th>Money</th>
							<th>Is administrator?</th>
							<th>Phone number</th>
							<th>Delete?</th>
						</tr>
					</thead>
		EOD;

		// show the users as a table
		foreach ($users as $user) {
			$is_admin = ((bool)$user['isAdministrator'] ? 'True' : 'False');

			$table .= <<<EOD
				<tr>
					<td>{$user['id']}</td>
					<td>{$user['firstName']}</td>
					<td>{$user['lastName']}</td>
					<td>{$user['physicalAddress']}</td>
					<td>{$user['emailAddress']}</td>
					<td>{$user['money']}</td>
					<td>{$is_admin}</td>
					<td>{$user['e164PhoneNumber']}</td>

					<td>
						<form method="post" action="delete.php">
							<input type="hidden" name="id" value="{$user['id']}">
							<input type="hidden" name="table_name" value="User">
							<input type="submit" value="Delete">
							<input type="hidden" name="redirect" value="/admin.php?page=confirmedusers">
						</form>
					</td>
				</tr>
			EOD;
		}
		$table .= '</table></div>';
	} else {
		$table = "No users found!";
	}

	$html = <<<EOD
		<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
			<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-0 border-bottom">
				<h1 class="h2">Confirmed Users</h1>
			</div>

			{$table}
		</main>
	EOD;
	return $html;
?>
