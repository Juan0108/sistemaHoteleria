<?php


require_once "../controllers/hoteles.controlador.php";
require_once "../models/hoteles.modelo.php";

class ComboMunicipios
{

	public $id;
	public function mostrarComboMunicipios(){

	   $valor = $this->id;
	   $Consulta = ModeloHoteles::MdlJsonObtenerMunicipios($valor);
	   echo ($Consulta); 
       
	}
}


$Mostar = new ComboMunicipios();
$Mostar -> id = $_POST['id'];
$Mostar -> mostrarComboMunicipios();