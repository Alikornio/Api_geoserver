<?php
	include "ApiRest.php";
	include "Connection.php";
	include "LocalgisMap.php";

	$idMap=537;
	$map = new LocalgisMap($idMap);
	print(json_encode($map));
?>