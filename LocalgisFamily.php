<?php
	class LocalgisFamily {
		var $id_family;
		var $name;
		var $description;

		public function __construct($attributes){
			$this->id_map=$attributes[0];
			$this->name=$attributes[1];
			$this->description=$attributes[3];
		}
	}
?>