<?php
	include "LocalgisLayer.php";

	class LocalgisFamily {
		var $id;
		var $name;
		var $layers;

		public function __construct($id){
			$this->id=$id;
			$connection = new ServerConnection();
			$query = "SELECT id_name FROM layerfamilies WHERE id_layerfamily='".$id."'";
			$result = pg_query($query) or die('Error: '.pg_last_error());
			$connection->dbClose();
			$attributes = pg_fetch_all($result);

			$this->name=$attributes[0]["id_name"];
			$this->layers=$this->getLayers($this->id);
		}


		public function getLayers($idFamily) {
			$connection = new ServerConnection();
			$query = "SELECT l.id_layer,l.name,l.acl,l.id_styles FROM layerfamilies_layers_relations as r,layers as l WHERE r.id_layerfamily='".$idFamily."' AND r.id_layer=l.id_layer";
			$result = pg_query($query) or die('Error: '.pg_last_error());
			$connection->dbClose();
			$layers = array();
			$i = 0;
			while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
				$layer_attributes = array();
				$j = 0;
			    foreach ($line as $col_value) {
			        $layer_attributes[$j++]=$col_value;
			    }
			    $layers[$i++]= new LocalgisLayer($layer_attributes[0]);
			}
			return json_encode($layers);
		}
	}
?>