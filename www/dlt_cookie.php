<?php
	setcookie('shoppingCart', 0, time() - 3600, "/");
	echo 'Cookies deleted successfully.';
?>