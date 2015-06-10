<?php
	include "GeoserverWrapper.php";
	include "Connection.php";
	include "LocalgisMap.php";

	print(listMaps());

	function listMaps() {
		$connection = new ServerConnection();
		$dbconn = pg_connect("host=".$connection->dbHost." dbname=".$connection->dbName." user=".$connection->dbUser." password=".$connection->dbPass)  or die('Error: '.pg_last_error());

		// Realizando una consulta SQL
		$layer="maps";
		$query = 'SELECT *  FROM '.$layer;
		$result = pg_query($query) or die('Error: '.pg_last_error());
		$maps = array();
		$i = 0;
		while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
			$col = array();
			$j = 0;
		    foreach ($line as $col_value) {
		        $col[$j]=$col_value;
		        $j=$j+1;
		    }
		    $maps[$i]= new LocalgisMap($col);
		    $i=$i+1;
		}
		return json_encode($maps);
	}

	/*
		Un mapa tiene una o varias familias de capas. Y estas a su vez una o varias capas
	*/
	/*function createGeoserverMap(idMap) {
		return json_decode();
	}*/
?>