<?php

	include "ApiRest.php";
	include "Connection.php";
	include "LocalgisFamily.php";

	print(listFamilies());

	function listFamilies() {
		$connection = new ServerConnection();
		$dbconn = pg_connect("host=".$connection->dbHost." dbname=".$connection->dbName." user=".$connection->dbUser." password=".$connection->dbPass)  or die('Error: '.pg_last_error());

		// Realizando una consulta SQL
		$layer="";
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
		    $maps[$i]= new LocalgisFamily($col);
		    $i=$i+1;
		}
		pg_close($dbconn);
		return json_encode($maps);
	}
?>