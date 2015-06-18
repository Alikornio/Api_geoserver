<?php
	include "ApiRest.php";

	$username="admin";
	$password="geoserver";
	$workspaceName="Localgis2";
	$groupName="Prueba";

	$geoserver = new ApiRest('http://localhost:8080/geoserver',$username, $password);

	$listGroup=$geoserver->listLayersOfGroup($workspaceName, $groupName);
	echo "#### Lista de Grupos ####\n";
	if(isset($listGroup->layerGroup->publishables->published)){
		foreach ($listGroup->layerGroup->publishables->published as $ft)
		{
		    echo " Layer:". $ft->name .", ".$ft->href."\n";
		};
	}
	else
		echo " La lista está vacia\n";
	echo "#############################\n";
?>