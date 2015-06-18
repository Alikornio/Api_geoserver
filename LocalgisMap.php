<?php
	include "LocalgisFamily.php";

	class LocalgisMap {
		var $id;
		var $mapDescription;
		var $mapUnits;
		var $mapScale;
		var $mapProjection;
		var $mapSrid;
		var $mapName;
		var $image;
		var $id_entidad;
		var $projection_id;
		var $fecha_ins;
		var $fecha_mod;
		var $families;

		public function __construct($id,$getFamilies=true){
			$this->id=$id;
			$connection = new ServerConnection();

			$query = "SELECT * FROM maps WHERE id_map='".$id."'";
			$result = pg_query($query) or die('Error: '.pg_last_error());
			$connection->dbClose();
			$attributes = pg_fetch_all($result);
			if($attributes[0]["xml"]!=""){
				$xml=simplexml_load_string(utf8_encode($attributes[0]["xml"]));
				$this->mapDescription=$xml->description;
				$this->mapUnits=$xml->mapUnits;
				$this->mapScale=$xml->mapScale;
				$this->mapProjection=$xml->mapProjection;
				$this->mapSrid=$xml->mapSrid;
				$this->mapName=$xml->mapName;
			}
			$this->image=$attributes[0]["image"];
			$this->id_entidad=$attributes[0]["id_entidad"];
			$this->projection_id=$attributes[0]["projection_id"];
			$this->fecha_ins=$attributes[0]["fecha_ins"];
			$this->fecha_mod=$attributes[0]["fecha_mod"];
			if($getFamilies){
				$this->families=$this->getFamilies($this->id);
			}
		}

		public function getFamilies($idMap) {
			$connection = new ServerConnection();
			$query = "SELECT lf.id_layerfamily,lf.id_name FROM maps_layerfamilies_relations as r, layerfamilies as lf WHERE r.id_map = '".$idMap."' AND r.id_layerfamily=lf.id_layerfamily";
			$result = pg_query($query) or die('Error: '.pg_last_error());
			$families = array();
			$i = 0;
			while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
				$family_attributes = array();
				$j = 0;
			    foreach ($line as $col_value) {
			        $family_attributes[$j++]=$col_value;
			    }
			    $families[$i++]= new LocalgisFamily($family_attributes[0]);
			}
			return json_encode($families);
		}
	}
?>