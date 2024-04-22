<?php
	try {
		$pdo = require 'connect.php';
		$sql = 'SELECT * FROM Store';
		$statement = $pdo->query($sql);
		$stores = $statement->fetchAll(PDO::FETCH_ASSOC);
		$table = '';
		
		if ($stores) {
			$table .= <<<EOD
			<div class="table-responsive small">
			    <table class="table table-striped table-sm">
				<thead>
			    		<tr>
			    			<th>Store ID</th>
			    			<th>Store Number</th>
			    			<th>Store Name</th>
			    			<th>Store Location Address</th>
			    			<th>Store Latitude</th>
			    			<th>Store Longitude</th>
			    			<th>Online Only</th>
			    			<th>Edit</th>
			    			<th>Delete</th>
			    		</tr>
				</thead>
			EOD;
			
			foreach ($stores as $store) {
				$online_only = $store['onlineOnly'] ? "Yes" : "No";

				$table .= <<<EOD
				<tr>
					<td>{$store['id']}</td>
					<td>{$store['storeNumber']}</td>
					<td>{$store['name']}</td>
					<td>{$store['physicalAddress']}</td>
					<td>{$store['latitude']}°</td>
					<td>{$store['longitude']}°</td>
					<td>{$online_only}</td>
					<td>
						<form method="post" action="/admin.php?page=store_edit">
							<input type="hidden" name="store_id" value="{$store['id']}">
							<button type="submit" value="Edit" class="btn py-0"><i class="icon-edit"></i></button>
						</form>
					</td>
				
					<td>
						<form method="post" class="delete-form" action="store_delete.php">
							<input type="hidden" name="store_id" value="{$store['id']}">
							<button type="submit" value="Delete" class="btn py-0"><i class="icon-remove"></i></button>
						</form>
					</td>
				</tr>
				EOD;
		    }
		    $table .= '</table></div>';
		} else {
		    $table = "<p>No stores found.</p>";
		}
	} catch (PDOException $e) {
		return "Database error: " . $e->getMessage();
	}

	$html = <<<EOD
	<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
		<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-0 border-bottom">
			<h1 class="h2">Stores</h1>
		</div>
	
		<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
			<form action="store_insert.php" method="post">
				<div class="mb-3">
					<label for="inputStoreName" class="form-label">Name</label>
					<input type="text" class="form-control" id="inputStoreName" name="name">
				</div>
	
				<div class="mb-3">
					<label for="inputStoreAddress" class="form-label">Physical Address</label>
					<input type="text" class="form-control" id="inputStoreAddress" name="physicalAddress">
				</div>
	
				<div class="mb-3">
					<label for="inputStoreLatitude" class="form-label">Latitude</label>
					<input type="text" class="form-control" id="inputStoreLatitude" name="latitude">
				</div>
	
				<div class="mb-3">
					<label for="inputStoreLongitude" class="form-label">Longitude</label>
					<input type="text" class="form-control" id="inputStoreLongitude" name="longitude">
				</div>
	
				<div class="mb-3">
					<label for="selectStoreOnlineOnly" class="form-label">Online Only</label>
					<select class="form-select" id="selectStoreOnlineOnly" name="onlineOnly">
						<option value="0">No</option>
						<option value="1">Yes</option>
					</select>
				</div>
	
				<div class="mb-3">
					<label for="inputStoreNumber" class="form-label">Store Number</label>
					<input type="text" class="form-control" id="inputStoreNumber" name="storeNumber">
				</div>
	
				<button type="submit" class="btn btn-primary mb-2" value="Add Store">Add Store</button>
			</form>
		</div>

		{$table}
	</main>
	EOD;
	return $html;
?>
