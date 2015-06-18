<?php

include "ApiRest.php";

$username="admin";
$password="geoserver";
$workspaceName="Unit_DS_WS";
$dataStore="Unit_DS_DS";
$geoserver = new ApiRest('http://localhost:8080/geoserver',$username, $password);

if(($result=$geoserver->createWorkspace($workspaceName))!="")
	print("Advice".$result."\n");
else
	print("Workspace creado\n");

$listDataStores=$geoserver->listDataStores($workspaceName);
echo "#### Lista de DataStores ####\n";
if(isset($listDataStores->dataStores->dataStore)){
	foreach ($listDataStores->dataStores->dataStore as $ds)
	{
	    echo " Workspace:". $ds->name .", ".$ds->href."\n";
	};
}
else
	echo " La lista está vacia\n";
echo "#############################\n";
// public function createPostGISDataStore($datastoreName, $workspaceName,$databaseName, $databaseUser, $databasePass, $databaseHost = 'localhost',$databasePort = '5432')
if(($result=$geoserver->createPostGISDataStore($workspaceName, $dataStore, 'Localgis', 'postgres', '1234', 'localhost'))!=""){
	print("Advice".$result."\n");
	if(($result=$geoserver->deleteDataStore($workspaceName, $dataStore))!="")
		print("Advice:".$result."\n");
	else
		print("Store eliminado\n");
}
else
	print("Store creado\n");

?>