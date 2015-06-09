<?php
	include "GeoserverWrapper.php";
	include "connection.php";

	listMaps();

	function listMaps() {
		$connection = new ServerConnection();
		print $connection->dbHost;
		$dbconn = pg_connect("host=".$connection->dbHost." dbname=".$connection->dbName." user=".$connection->dbUser." password=".$connection->dbPass)  or die('Error: '.pg_last_error());

		// Realizando una consulta SQL
		$layer="maps";
		$query = 'SELECT id_map, id_name, xml, id_entidad, projection_id, fecha_ins, fecha_mod  FROM '.$layer;
		$result = pg_query($query) or die('Error: '.pg_last_error());
		$maps=array();
		$i=0;
		$j=0;
		while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
			$maps[$i]=array();
			$j=0;
		    foreach ($line as $col_value) {
		        $maps[$i][$j]=$col_value;
		        $j=$j+1;
		    }
		    $i=$i+1;
		}
		var_dump($maps);
		//return json_decode();
	}

	/*
		Un mapa tiene una o varias familias de capas. Y estas a su vez una o varias capas
	*/
	/*function createGeoserverMap(idMap) {
		return json_decode();
	}*/
?>