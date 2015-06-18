<?php
	include "ApiRest.php";
	include "Connection.php";
	include "LocalgisLayer.php";

	$idLayer=12547;
	$layer = new LocalgisLayer($idLayer);
	print(json_encode($layer));
?>