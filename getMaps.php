<?php

	include "ApiRest.php";
	include "Connection.php";
	include "LocalgisMap.php";

	$connection = new ServerConnection();
	print(getMaps());
	$connection->dbClose();

	function getMaps() {
		$query = 'SELECT * FROM maps';
		$result = pg_query($query) or die('Error: '.pg_last_error());
		$maps = array();
		$i = 0;
		while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
			$map_attributes = array();
			$j = 0;
		    foreach ($line as $col_value) {
		        $map_attributes[$j++]=$col_value;
		    }
		    $maps[$i++]= new LocalgisMap($map_attributes[0],false);
		}
		return json_encode($maps);
	}
?>