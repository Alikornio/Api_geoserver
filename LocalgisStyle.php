<?php
	class LocalgisStyle {
		var $id;
		var $sld;

		public function __construct($attributes){
			$this->id=$attributes[0];
			$this->sld=$attributes[1];
		}
	}
?>