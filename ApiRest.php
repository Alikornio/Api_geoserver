<?php
ini_set("display_errors", "On");
error_reporting(E_ALL);

class ApiRest {
	var $serverUrl = '';
	var $username = '';
	var $password = '';

// Internal stuff
	public function __construct($serverUrl, $username = '', $password = '') {
		if (substr($serverUrl, -1) !== '/') $serverUrl .= '/';
		$this->serverUrl = $serverUrl;
		$this->username = $username;
		$this->password = $password;
	}

	private function authGet($apiPath) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->serverUrl.$apiPath);
		curl_setopt($ch, CURLOPT_USERPWD, $this->username.":".$this->password); 
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$rslt = curl_exec($ch);
		$info = curl_getinfo($ch);
		
		if ($info['http_code'] == 401) {
			return 'Access denied. Check login credentials.';
		} else {
			return $rslt;
		}
	}

	private function runApi($apiPath, $method = 'GET', $data = '', $contentType = 'text/xml') {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->serverUrl.'rest/'.$apiPath);
		curl_setopt($ch, CURLOPT_USERPWD, $this->username.":".$this->password); 
		if ($method == 'POST') {
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		} else if ($method == 'DELETE' || $method == 'PUT') {
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
		}
		if ($data != '') {
			curl_setopt($ch, CURLOPT_HTTPHEADER, 
				array("Content-Type: $contentType",
				'Content-Length: '.strlen($data))
			);
		}
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		$rslt = curl_exec($ch);
		$info = curl_getinfo($ch);
		if ($info['http_code'] == 401) {
			return 'Access denied. Check login credentials.';
		} else {
			return $rslt;
		}
	}

// Workspace APIs
	public function listWorkspaces() {
		return json_decode($this->runApi('workspaces.json'));
	}

	public function createWorkspace($workspaceName) {
		return $this->runApi('workspaces', 'POST', '<workspace><name>'.htmlentities($workspaceName, ENT_COMPAT).'</name></workspace>');
	}

	public function deleteWorkspace($workspaceName) {
		return $this->runApi('workspaces/'.urlencode($workspaceName), 'DELETE');
	}

	public function existsWorkspace($workspaceName){
		$listWorkspaces=$this->listWorkspaces();
		if(isset($listWorkspaces->workspaces->workspace)){
			foreach ($listWorkspaces->workspaces->workspace as $ws)
			{
			    if($ws->name==$workspaceName)
			    	return true;
			};
		}
		return false;
	}
	public function enableWms($workspaceName){
		return $this->runApi('services/wms/workspaces/'.urlencode($workspaceName).'/settings.xml', 'PUT', '<wms><enabled>true</enabled></wms>');
	}

	public function disableWms($workspaceName){
		return $this->runApi('services/wms/workspaces/'.urlencode($workspaceName).'/settings.xml', 'PUT', '<wms><enabled>false</enabled></wms>');
	}

// Datastore APIs
	public function listDataStores($workspaceName) {
		return json_decode($this->runApi('workspaces/'.urlencode($workspaceName).'/datastores.json'));
	}

	public function createPostGISDataStore($workspaceName, $datastoreName, $databaseSchema, $databaseName, $databaseUser, $databasePass, $databaseHost = 'localhost', $databasePort = '5432') {
		return $this->runApi('workspaces/'.urlencode($workspaceName).'/datastores', 'POST', '<dataStore>
			<name>'.htmlentities($datastoreName, ENT_COMPAT).'</name>
			<type>PostGIS</type>
			<enabled>true</enabled>
			<connectionParameters>
				<entry key="port">'.htmlentities($databasePort, ENT_COMPAT).'</entry>
				<entry key="Connection timeout">20</entry>
				<entry key="passwd">'.htmlentities($databasePass, ENT_COMPAT).'</entry>
				<entry key="dbtype">postgis</entry>
				<entry key="host">'.htmlentities($databaseHost, ENT_COMPAT).'</entry>
				<entry key="validate connections">true</entry>
				<entry key="encode functions">false</entry>
				<entry key="max connections">10</entry>
				<entry key="database">'.htmlentities($databaseName, ENT_COMPAT).'</entry>
				<entry key="namespace">http://'.htmlentities($workspaceName, ENT_COMPAT).'</entry>
				<entry key="schema">'.htmlentities($databaseSchema, ENT_COMPAT).'</entry>
				<entry key="Loose bbox">true</entry>
				<entry key="Expose primary keys">false</entry>
				<entry key="fetch size">1000</entry>
				<entry key="Max open prepared statements">50</entry>
				<entry key="preparedStatements">false</entry>
				<entry key="Estimated extends">true</entry>
				<entry key="user">'.htmlentities($databaseUser, ENT_COMPAT).'</entry>
				<entry key="min connections">1</entry>
			</connectionParameters>
			</dataStore>');
	}

	public function createShpDirDataStore($workspaceName, $datastoreName, $location) {
		return $this->runApi('workspaces/'.urlencode($workspaceName).'/datastores', 'POST', '<dataStore>
			<name>'.htmlentities($datastoreName, ENT_COMPAT).'</name>
			<type>Directory of spatial files (shapefiles)</type>
			<enabled>true</enabled>
			<connectionParameters>
				<entry key="memory mapped buffer">false</entry>
				<entry key="timezone">America/Boise</entry>
				<entry key="create spatial index">true</entry>
				<entry key="charset">ISO-8859-1</entry>
				<entry key="filetype">shapefile</entry>
				<entry key="cache and reuse memory maps">true</entry>
				<entry key="url">file:'.htmlentities($location, ENT_COMPAT).'</entry>
				<entry key="namespace">'.htmlentities($workspaceName, ENT_COMPAT).'</entry>
			</connectionParameters>
			</dataStore>');
	}

	public function deleteDataStore($workspaceName, $datastoreName) {
		return $this->runApi('workspaces/'.urlencode($workspaceName).'/datastores/'.urlencode($datastoreName), 'DELETE');
	}

	public function existsDatastore($workspaceName, $datastoreName){
		$listDatastores=$this->listDataStores($workspaceName);
		if(isset($listDatastores->dataStores->dataStore)){
			foreach ($listDatastores->dataStores->dataStore as $ds)
			{
			    if($ds->name==$datastoreName)
			    	return true;
			};
		}
		return false;
	}


// Layer APIs
	public function listLayers($workspaceName, $datastoreName) {
		return json_decode($this->runApi('workspaces/'.urlencode($workspaceName).'/datastores/'.urlencode($datastoreName).'/featuretypes.json'));
	}

	public function addLayer($workspaceName, $datastoreName, $layerName, $description = ''){
		if($this->existsWorkspace($workspaceName) && $this->existsDatastore($workspaceName, $datastoreName)){
			return $this->runApi('workspaces/'.urlencode($workspaceName).'/datastores/'.urlencode($datastoreName).'/featuretypes.xml', 'POST', '<featureType>
			<name>'.$layerName.'</name>
			<nativeName>'.$layerName.'</nativeName>
			<description>'.htmlentities($description, ENT_COMPAT).'</description>
			<store class="dataStore"><name>'.htmlentities($datastoreName, ENT_COMPAT).'</name></store>
			</featureType>');
		}
		return "Workspace or Datastore don't exists";
	}

	public function createLayer($workspaceName, $datastoreName, $layerName, $description = '') {
		// Add the store's feature type:
		// If layerName is a shapefile, the shapefile should exist in store already; uploaded via external means
		// If layerName is a postgis database table, that table should already exist

		// Just in case it's a .shp and the .shp was included
		$layerName = str_replace('.shp', '', str_replace('.SHP', '', $layerName));
		return $this->runApi('workspaces/'.urlencode($workspaceName).'/datastores/'.urlencode($datastoreName).'/featuretypes.xml', 'POST', '<featureType>
			<name>'.$layerName.'</name>
			<nativeName>'.$layerName.'</nativeName>
			<description>'.htmlentities($description, ENT_COMPAT).'</description>
			<store class="dataStore"><name>'.htmlentities($datastoreName, ENT_COMPAT).'</name></store>
			</featureType>');
	}

	public function deleteLayer($workspaceName, $datastoreName, $layerName) {
		$this->runApi('layers/'.urlencode($layerName), 'DELETE');
		return $this->runApi('workspaces/'.urlencode($workspaceName).'/datastores/'.urlencode($datastoreName).'/featuretypes/'.urlencode($layerName), 'DELETE');
	}

	public function viewLayer($layerName, $workspaceName, $format = 'GML', $maxGMLFeatures = 1000000, $overrideServerURL = '') {
		// overrideServerURL = useful if using reverseproxy-like configurations
		if ($format == 'GML') {
			//die(urlencode($layerName).'/ows?service=WFS&version=1.0.0&request=GetFeature&typeName='.urlencode($workspaceName).':'.urlencode($layerName).'&maxFeatures='.$maxGMLFeatures);
			return $this->authGet(urlencode($workspaceName).'/ows?service=WFS&version=1.0.0&request=GetFeature&typeName='.urlencode($workspaceName).':'.urlencode($layerName).'&maxFeatures='.$maxGMLFeatures);
		} else if ($format == 'KML') {
			return $this->authGet(urlencode($workspaceName).'/wms/kml?layers='.urlencode($workspaceName).':'.urlencode($layerName));
		}
	}

	public function viewLayerLegend($workspaceName, $width = 20, $height = 20, $layerName) {
		return $this->authGet("wms?REQUEST=GetLegendGraphic&VERSION=1.0.0&FORMAT=image/png&WIDTH=$width&HEIGHT=$height&LAYER=".urlencode($workspaceName).':'.urlencode($layerName));
	}

	public function wfsPost($apiPath, $post) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->serverUrl.'wfs'.$apiPath);
		curl_setopt($ch, CURLOPT_USERPWD, $this->username.":".$this->password); 
		if ($post != '') {
			curl_setopt($ch, CURLOPT_HTTPHEADER, 
				array("Content-Type: text/xml",
				'Content-Length: '.strlen($post))
			);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		}
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$rslt = curl_exec($ch);
		$info = curl_getinfo($ch);

		if ($info['http_code'] == 401) {
			return 'Access denied. Check login credentials.';
		} else {
			return $rslt;
		}
	}

	public function executeWFSTransaction($WFSTRequest) {
		// WFS-T is just WFS really...
		return $this->wfsPost('', $WFSTRequest);
	}

// Group APIs
	public function listLayerGroups($workspaceName){
		return json_decode($this->runApi('workspaces/'.urlencode($workspaceName).'/layergroups.json'));
	}

	public function listLayerGroup($workspaceName, $groupName){
		return json_decode($this->runApi('workspaces/'.urlencode($workspaceName).'/layergroups/'.urlencode($groupName).'/featuretypes.json'));
	}

	public function createLayerGroup($workspaceName, $groupName, $layers){
		$layersXml="";
		$stylesXml="";
		$aux=array();
		foreach ($layers as $layerName) {

			if(!in_array($layerName, $aux, true)){
				array_push($aux,$layerName);
				$layersXml=$layersXml.'<layer>'.htmlentities($workspaceName.':'.$layerName, ENT_COMPAT).'</layer>';
				$stylesXml=$stylesXml.'<style></style>';
			}
		}

		return $this->runApi('workspaces/'.urlencode($workspaceName).'/layergroups', 'POST',
			'<layerGroup>
			  <name>'.htmlentities($groupName, ENT_COMPAT).'</name>
			  <layers>'.$layersXml.'</layers>
			  <styles>'.$stylesXml.'</styles>
			</layerGroup>');
	}
	
	public function deleteLayerGroup($workspaceName, $groupName){
		return json_decode($this->runApi('workspaces/'.urlencode($workspaceName).'/layergroups/'.urlencode($groupName), 'DELETE'));
	}

	public function addLayerToLayerGroup($workspaceName, $groupName, $layerName){
		$layersXml="";
		$stylesXml="";
		$listGroup = $this->listLayerGroup($workspaceName, $groupName);
		$aux=array();
		if(isset($listGroup->layerGroup->publishables->published)){
			$published=$listGroup->layerGroup->publishables->published;
			if(is_array($published)){
				foreach ($published as $ft){
					array_push($aux,$ft->name);
					$layersXml=$layersXml.'<layer>'.htmlentities($workspaceName.':'.$ft->name, ENT_COMPAT).'</layer>';
	    			$stylesXml=$stylesXml.'<style></style>';
				};
			}
		}

		if(!in_array($layerName, $aux, true)){
    		$layersXml=$layersXml.'<layer>'.htmlentities($workspaceName.':'.$layerName, ENT_COMPAT).'</layer>';
    		$stylesXml=$stylesXml.'<style></style>';
    	}

		return $this->runApi('workspaces/'.urlencode($workspaceName).'/layergroups/'.urlencode($groupName), 'PUT',
			  '<layerGroup>
				  <layers>'.$layersXml.'</layers>
				  <styles>'.$stylesXml.'</styles>
			  </layerGroup>');
	}

	public function addStyleToLayerGroup($workspaceName, $groupName, $styleName){
		return $this->runApi('workspaces/'.urlencode($workspaceName).'/layergroups/'.urlencode($groupName), 'PUT','
			  <styles>
			  	<style>'.htmlentities($styleName, ENT_COMPAT).'</style>
			  </styles>');
	}

	public function deleleLayerFromLayerGroup($workspaceName, $groupName){
		return json_decode($this->runApi('workspaces/'.urlencode($workspaceName).'/layergroups/'.urlencode($groupName), 'DELETE'));
	}

// Style APIs
	public function listStyles() {	
		return json_decode($this->runApi('styles.json'));
	}

	public function createStyle($styleName, $SLD) {
		$rv = $this->runApi('styles.xml', 'POST', '<style>
			<name>'.htmlentities($styleName, ENT_COMPAT).'</name>
			<filename>'.htmlentities($styleName, ENT_COMPAT).'.sld</filename>
			</style>');
		$this->runApi('styles/'.urlencode($styleName), 'PUT', stripslashes($SLD), 'application/vnd.ogc.sld+xml');
		return $rv;
	}

	public function addStyleToLayer($layerName, $workspaceName, $styleName) {
		// Just adds style to the list of supported styles - then WMS requests can pass the desired style
		return $this->runApi('layers/'.urlencode($layerName).'/styles', 'POST', '<style><name>'.htmlentities($styleName, ENT_COMPAT).'</name></style>');
	}

	public function deleteStyle($styleName) {
		return $this->runApi('styles/'.urlencode($styleName), 'DELETE');
	}
}
