<?php
	$html = <<<EOD
	<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
		<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
			<h1 class="h2">Database Maintenance</h1>
		</div>


		<div class="alert alert-danger" role="alert">
			WARNING: All data will be lost permanently.

			<br><br>

			<form action="db_delete.php" method="post">
				<input type="submit" value="Delete database">
			</form>
		</div>
	</main>
EOD;

	return $html;
?>
