<?php

include "GeoserverWrapper.php";

$username="admin";
$password="geoserver";
$workspaceName="Unit_WS";
$geoserver = new GeoserverWrapper('http://localhost:8080/geoserver',$username, $password);

$listWorkspaces=$geoserver->listWorkspaces();
if($geoserver->existsWorkspace($workspaceName))
	echo "El espacio de trabajo ya existe\n";
//var_dump($listWorkspaces);
echo "#### Lista de Workspaces ####\n";
if(isset($listWorkspaces->workspaces)){
	foreach ($listWorkspaces->workspaces->workspace as $ws)
	{
	    echo " Workspace:". $ws->name .", ".$ws->href."\n";
	};
}
else
	echo "La lista está vacia\n";
echo "#############################\n";

if(($result=$geoserver->createWorkspace($workspaceName))!=""){
	print("Advice".$result."\n");
	if(($result=$geoserver->deleteWorkspace($workspaceName))!="")
		print("Advice:".$result."\n");
	else
		print("Workspace eliminado\n");
}
else
	print("Workspace creado\n");

?>
