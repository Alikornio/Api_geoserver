<?php
	//php crearMapa.php '[{"id":"bezana_ign25_cultivos","features":["id_municipio","GEOMETRY","etiqueta"]},{"id":"bez_alumbrado_eiel","features":["id_municipio","GEOMETRY","tipo","potencia"]},{"id":"bez_arbolado_1","features":["id_municipio","GEOMETRY","tipo","observaciones"]},{"id":"bez_parcelario_planta00","features":["id_municipio","GEOMETRY","cargo_control","ref_catastral","superficie_subparcela","codigo_destino","observaciones","denominacion","planta","ref_catastral_aux","fecha_alteracion","fecha_antiguedad","municipio","pedania","tipo_via","nombre_via","bloque","escalera","puerta","texto","paraje","direccion_estructurada","numero","letra","tipo_parcela"]}]'

	include "ApiRest.php";
	include "Connection.php";

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
		$geoserver = new ApiRest('http://'.$connection->gsHost.':8080/geoserver',$connection->gsUser, $connection->gsPassword);

		if(($result=$geoserver->createWorkspace($connection->wsName))!="")
			print("Advice".$result."\n");
		else
			print("Workspace creado\n");

		if(($result=$geoserver->createPostGISDataStore($connection->wsName, $connection->dsName, $connection->dbSchema, $connection->dbName, $connection->dbUser, $connection->dbPass, $connection->dbHost))!="")
			print("Advice".$result."\n");
		else
			print("Store creado\n");

		#Activacion WS
		$geoserver->enableWms($connection->wsName);
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