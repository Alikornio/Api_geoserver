<?php

	include "ApiRest.php";
	include "Connection.php";
	include "LocalgisMap.php";


	createGeoserverMap();

	/*
		Un mapa tiene una o varias familias de capas. Y estas a su vez una o varias capas
	*/
	function createGeoserverMap($mapId=537, $t=23030, $m=39073) {
		$connection = new ServerConnection();
		$dbconn = pg_connect("host=".$connection->dbHost." dbname=".$connection->dbName." user=".$connection->dbUser." password=".$connection->dbPass)  or die('Error: '.pg_last_error());
		$query = 'SELECT DISTINCT(layers.name),layers.id_layer FROM maps_layerfamilies_relations, layerfamilies_layers_relations, layers WHERE maps_layerfamilies_relations.id_map='.$mapId.' AND maps_layerfamilies_relations.id_layerfamily=layerfamilies_layers_relations.id_layerfamily AND layerfamilies_layers_relations.id_layer = layers.id_layer';
		$result = pg_query($query) or die('Error: '.pg_last_error());
		$layerList=array();
		while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
			$layer=array();
		    foreach ($line as $col_value) {
		    	array_push($layer, $col_value);
		    }
		    array_push($layerList,$layer);
		}
		//var_dump($layerList);
		$layerDescription="Capa localgis";


		pg_query($dbconn, 'CREATE SCHEMA "'.$connection->dsName.'" AUTHORIZATION '.$connection->dbUser);

		

		#Creación del WS y el DS
		$geoserver = new ApiRest('http://'.$connection->gsHost.':8080/geoserver',$connection->gsUser, $connection->gsPassword);

		if(($result=$geoserver->createWorkspace($connection->wsName))!="")
			print("Advice".$result."\n");
		else
			print("Workspace creado\n");

		if(($result=$geoserver->createPostGISDataStore($connection->wsName, $connection->dsName, $connection->dsName, $connection->dbName, $connection->dbUser, $connection->dbPass, $connection->dbHost))!="")
			print("Advice".$result."\n");
		else
			print("Store creado\n");

		#Activacion WS
		print $geoserver->enableWms($connection->wsName);

		//SAVE MÁGICO
		//???
		//

		#Creamos una vista por cada capa y Añadimos las capas al DS
		foreach($layerList as $layer){
			$layerName=$layer[0];
			$layerId=$layer[1];
			print $layerName.": #".$layerId."\n";
			$query = "SELECT selectquery FROM queries WHERE id_layer='".$layerId."'";
			$result = pg_query($query) or die('Error: '.pg_last_error());
			$select_layer = pg_fetch_result($result, 0,0);
			//print $select_layer."\n";
			$select_layer=str_replace("?T","'".$t."'",$select_layer);
			$select_layer=str_replace("?M","'".$m."'",$select_layer);
			//print $select_layer."\n";
			pg_query($dbconn, 'CREATE OR REPLACE VIEW "'.$connection->dsName.'"."'.$layerName.'" AS '.$select_layer);
			//print_r($connection->wsName.", ".$connection->dsName.", ".$layerName.", ".$layerDescription."\n");
			if(($result=$geoserver->addLayer($connection->wsName, $connection->dsName, $layerName, $layerDescription))!=""){
				print("Advice: ".$result."\n");
			}
			else
				print("Capa añadida\n");

			//Asociamos el estilo
			$query = "SELECT l.stylename, s.xml FROM layers_styles as l, styles as s WHERE l.id_style=s.id_style AND id_layer='".$layerId."' AND id_map='".$mapId."'";
			print $query;
			$result = pg_query($query) or die('Error: '.pg_last_error());
			$styles = array();
			while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
			    $styleName = $line["stylename"];
			    $SLD = $line["xml"];
			    $geoserver->createStyle($styleName, $SLD);
			    $geoserver->addStyleToLayer($layerName, $connection->wsName, $styleName);
			}
		}

		pg_close($dbconn);
	}

?>