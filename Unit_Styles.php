<?php

include "ApiRest.php";

$username="admin";
$password="geoserver";
$workspaceName="Localgis2";
$datastoreName="Localgis2";
$layerName="bez_parcelario_planta00";
$layerDescription="Unit_L description";
$geoserver = new ApiRest('http://localhost:8080/geoserver',$username, $password);

$listStyles=$geoserver->listStyles();
echo "#### Lista de Estilos ####\n";
var_dump($listStyles);
/*
if(isset($listLayers->featureTypes->featureType)){
	foreach ($listLayers->featureTypes->featureType as $ft)
	{
	    echo " Layer:". $ft->name .", ".$ft->href."\n";
	    //print($geoserver->viewLayerLegend($workspaceName, $width = 20, $height = 20, $layerName)."\n");
	    //print($geoserver->viewLayer($layerName, $workspaceName, $format = 'GML', $maxGMLFeatures = 1000000, $overrideServerURL = '')."\n\n");
	};
}
else
	echo " La lista está vacia\n";
echo "#############################\n";
*/
?>