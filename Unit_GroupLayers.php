<?php
	include "ApiRest.php";

	$username="admin";
	$password="geoserver";
	$workspaceName="Concejo1";
	$groupName="Prueba3";

	$geoserver = new ApiRest('http://localhost:8080/geoserver',$username, $password);

	$listGroups=$geoserver->listLayerGroups($workspaceName);
	echo ">Listando grupos...\n";
	echo "###### Lista de Grupos ######\n";
	if(isset($listGroups->layerGroups->layerGroup)){
		foreach ($listGroups->layerGroups->layerGroup as $lg)
		{
		    echo " Layer:". $lg->name ."\n";//, Href:".$ft->href."\n";
		};
	}
	else
		echo " La lista está vacia\n";
	echo "#############################\n\n";

	echo ">Creando grupo...\n";
	$layers=array('bezana_ign25_cultivos','bezana_ign25_cultivos','bez_arbolado_1');
	print($geoserver->createLayerGroup($workspaceName, $groupName, $layers)."\n");

	$listGroup=$geoserver->listLayerGroup($workspaceName, $groupName);
	echo "###### Lista de Capas del grupo:".$groupName." ######\n";
	if(isset($listGroup->layerGroup->publishables->published)){
		$published=$listGroup->layerGroup->publishables->published;
		if(is_array($published)){
			foreach ($published as $layer)
			{
				echo " Layer:". $layer->name ."\n";//, Href:".$ft->href."\n";
			};
		}
		else
			echo " Layer:". $published->name ."\n";//, Href:".$ft->href."\n";
	}
	else
		echo " La lista está vacia\n";
	echo "##############################################\n\n";

	echo ">Añadiendo capas al grupo...\n";

	$geoserver->addLayerToLayerGroup($workspaceName, $groupName, 'bez_alumbrado_eiel');

	$listGroup=$geoserver->listLayerGroup($workspaceName, $groupName);
	echo "###### Lista de Capas del grupo:".$groupName." ######\n";
	//var_dump($listGroup);
	if(isset($listGroup->layerGroup->publishables->published)){
		$published=$listGroup->layerGroup->publishables->published;
		if(is_array($published)){
			foreach ($published as $layer)
			{
			    echo " Layer:". $layer->name ."\n";//, Href:".$ft->href."\n";
			};
		}
		else
			echo " Layer:". $published->name ."\n";//, Href:".$ft->href."\n";
	}
	else
		echo " La lista está vacia\n";
	echo "##############################################\n\n";

?>