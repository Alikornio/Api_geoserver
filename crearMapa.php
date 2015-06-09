<?php

include "GeoserverWrapper.php";
include "connection.php";

$layerDescription="Capa manual";

if(count($argv)!=2){
	print("Use: ".$argv[0]." <layerInfo_format>\n");
	exit;
}
else{
	#Info layers
	$layers=json_decode($argv[1]);
	$connection = new ServerConnection();
	$conn=pg_connect('host='.$connection->dbHost.' user='.$connection->dbUser.' password='.$connection->dbPass.' dbname='.$connection->dbName.' connect_timeout=5'); 
	if($conn){
		pg_query($conn, 'CREATE SCHEMA "'.$connection->dsName.'" AUTHORIZATION '.$connection->dbUser);

		#Creamos una vista por cada capa
		foreach($layers as $layer){
			$layerName=$layer->id;
			print_r($layerName."\n");
			pg_query($conn, 'CREATE OR REPLACE VIEW "'.$connection->dsName.'"."'.$layerName.'" AS SELECT * FROM public."'.$layerName.'"');
			#pg_query($conn, 'ALTER TABLE "'.$dsName.'.'.$layerName.'" OWNER TO postgres');
		}
	}
	else{
		print("Database error: ".$e);
	}
	pg_close($conn);

	#Creación del WS y el DS
	$geoserver = new GeoserverWrapper('http://'.$connection->gsHost.':8080/geoserver',$connection->gsUser, $connection->gsPassword);

	if(($result=$geoserver->createWorkspace($connection->wsName))!="")
		print("Advice".$result."\n");
	else
		print("Workspace creado\n");

	if(($result=$geoserver->createPostGISDataStore($connection->wsName, $connection->dsName, $connection->dbName, $connection->dbUser, $connection->dbPass, $connection->dbHost))!="")
		print("Advice".$result."\n");
	else
		print("Store creado\n");

	#Activacion WS
	
	#Añadimos las capas al DS
	foreach($layers as $layer){
		$layerName=$layer->id;
		print_r($connection->wsName.", ".$connection->dsName.", ".$layerName.", ".$layerDescription."\n");
		if(($result=$geoserver->addLayer($connection->wsName, $connection->dsName, $layerName, $layerDescription))!=""){
			print("Advice".$result."\n");
		}
		else
			print("Capa añadida\n");
	}
}
?>