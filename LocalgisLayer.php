<?php
	include "LocalgisStyle.php";

	class LocalgisLayer {
		var $id;
		var $name;
		var $acl;
		var $id_default_style;
		var $styles;

		public function __construct($id){
			$this->id=$id;

			$connection = new ServerConnection();
			$query = "SELECT name,acl,id_styles FROM layers WHERE id_layer='".$id."'";
			$result = pg_query($query) or die('Error: '.pg_last_error());
			$connection->dbClose();
			$attributes = pg_fetch_all($result);

			$this->name=$attributes[0]["name"];
			$this->acl=$attributes[0]["acl"];
			$this->id_default_style=$attributes[0]["id_styles"];
			$this->styles=$this->getStyles($this->id);
		}

		public function getStyles($idLayer){
			$connection = new ServerConnection();
			$query = "SELECT s.id_style, s.xml FROM layers as l,styles as s WHERE l.id_styles = s.id_style AND l.id_layer='".$idLayer."'";
			$result = pg_query($query) or die('Error: '.pg_last_error());
			$connection->dbClose();
			$styles = array();
			$i = 0;
			while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
				$style_attributes = array();
				$j = 0;
			    foreach ($line as $col_value) {
			        $style_attributes[$j++]=$col_value;
			    }
			    $styles[$i++]= new LocalgisStyle($style_attributes);
			}
			return json_encode($styles);
		}
	}
?>