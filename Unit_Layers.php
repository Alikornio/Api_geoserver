<?php

include "ApiRest.php";

$username="admin";
$password="geoserver";
$workspaceName="Localgis3";
$datastoreName="Localgis3";
$layerName="bez_parcelario_planta00";
$layerDescription="Unit_L description";
$geoserver = new ApiRest('http://localhost:8080/geoserver',$username, $password);

$listLayers=$geoserver->listLayers($workspaceName,$datastoreName);
echo "#### Lista de DataStores ####\n";
if(isset($listLayers->featureTypes->featureType)){
	foreach ($listLayers->featureTypes->featureType as $ft){
	    echo " Layer:". $ft->name .", ".$ft->href."\n";
	};
}
else
	echo " La lista está vacia\n";
echo "#############################\n";

if(($result=$geoserver->createWorkspace($workspaceName))!="")
	print("Advice".$result."\n");
else
	print("Workspace creado\n");
if(($result=$geoserver->createPostGISDataStore($workspaceName, $datastoreName, 'public', 'Localgis', 'postgres', '1234', 'localhost'))!="")
	print("Advice".$result."\n");
else
	print("Store creado\n");

print_r($workspaceName.", ".$datastoreName.", ".$layerName.", ".$layerDescription."\n");
if(($result=$geoserver->addLayer($workspaceName, $datastoreName, $layerName, $layerDescription))!=""){
	print("Advice".$result."\n");
	print($geoserver->deleteLayer($workspaceName, $datastoreName, $layerName)."\n");
}
else
	print("Capa añadida\n");
?>