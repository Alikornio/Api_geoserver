<?php
	class LocalgisMap {
		var $id_map;
		var $id_name;
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

		public function __construct($attributes){
			$this->id_map=$attributes[0];
			$this->id_name=$attributes[1];
			$xml=simplexml_load_string(utf8_encode($attributes[2]));
			$this->mapDescription=$xml->description;
			$this->mapUnits=$xml->mapUnits;
			$this->mapScale=$xml->mapScale;
			$this->mapProjection=$xml->mapProjection;
			$this->mapSrid=$xml->mapSrid;
			$this->mapName=$xml->mapName;
			$this->image=$attributes[3];
			$this->id_entidad=$attributes[4];
			$this->projection_id=$attributes[5];
			$this->fecha_ins=$attributes[6];
			$this->fecha_mod=$attributes[7];
		}
	}
?>