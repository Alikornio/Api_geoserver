<?php

include "GeoserverWrapper.php";

#Info database
$dbName="Localgis";
$dbUser="postgres";
$dbPass="1234";
$dbHost="localhost";
#Info geoserver
$gsHost="localhost";
$gsUser="admin";
$gsPassword="geoserver";
$wsName="Concejo1";
$dsName="Mapas_".$wsName;

$layerDescription="Capa manual";

if(count($argv)!=2){
	print("Use: ".$argv[0]." <layerInfo_format>\n");
	exit;
}
else{
	#Info layers
	$layers=json_decode($argv[1]);
	$conn=pg_connect('host='.$dbHost.' user='.$dbUser.' password='.$dbPass.' dbname='.$dbName.' connect_timeout=5'); 
	if($conn){
		pg_query($conn, 'CREATE SCHEMA "'.$dsName.'" AUTHORIZATION '.$dbUser);

		#Creamos una vista por cada capa
		foreach($layers as $layer){
			$layerName=$layer->id;
			print_r($layerName."\n");
			pg_query($conn, 'CREATE OR REPLACE VIEW "'.$dsName.'"."'.$layerName.'" AS SELECT * FROM public."'.$layerName.'"');
			#pg_query($conn, 'ALTER TABLE "'.$dsName.'.'.$layerName.'" OWNER TO postgres');
		}
	}
	else{
		print("Database error: ".$e);
	}
	pg_close($conn);

	#Creación del WS y el DS
	$geoserver = new GeoserverWrapper('http://'.$gsHost.':8080/geoserver',$gsUser, $gsPassword);

	if(($result=$geoserver->createWorkspace($wsName))!="")
		print("Advice".$result."\n");
	else
		print("Workspace creado\n");

	if(($result=$geoserver->createPostGISDataStore($wsName, $dsName, $dbName, $dbUser, $dbPass, $dbHost))!="")
		print("Advice".$result."\n");
	else
		print("Store creado\n");


	#Añadimos las capas al DS
	foreach($layers as $layer){
		$layerName=$layer->id;
		print_r($wsName.", ".$dsName.", ".$layerName.", ".$layerDescription."\n");
		if(($result=$geoserver->addLayer($wsName, $dsName, $layerName, $layerDescription))!=""){
			print("Advice".$result."\n");
		}
		else
			print("Capa añadida\n");
	}
}
?>