<?php

include "ApiRest.php";
#Info geoserver
$gsHost="localhost";
$gsUser="admin";
$gsPassword="geoserver";
$wsName="Concejo1";
$dsName="Mapas_".$wsName;

#Creación del WS y el DS
$geoserver = new ApiRest('http://'.$gsHost.':8080/geoserver',$gsUser, $gsPassword);
if(($result=$geoserver->enableWms($wsName))!="")
	print("Advice".$result."\n");
else
	print("Worspace ".$wsName.": Wms activado.\n");
?>