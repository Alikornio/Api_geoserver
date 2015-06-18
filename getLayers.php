<?php
	include "ApiRest.php";
	include "Connection.php";
	include "LocalgisFamily.php";

	$idFamily=400;
	$family = new LocalgisFamily($idFamily);
	print(json_encode($family));
?>