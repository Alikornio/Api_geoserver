<?php
	class ServerConnection {
		#Info database
		var $dbName= '';
		var $dbUser= '';
		var $dbPass= '';
		var $dbHost= '';
		#Info geoserver
		var $gsHost= '';
		var $gsUser= '';
		var $gsPassword= '';
		var $wsName= '';
		var $dsName= '';

		public function __construct(){
			$this->dbName="Localgis";
			$this->dbUser="postgres";
			$this->dbPass="1234";
			$this->dbHost="localhost";
			$this->gsHost="localhost";
			$this->gsUser="admin";
			$this->gsPassword="geoserver";
			$this->wsName="Concejo1";
			$this->dsName="Mapas_".$this->wsName;
		}
	}
?>