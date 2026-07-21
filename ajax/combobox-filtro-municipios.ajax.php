<?php


require_once "../controllers/negocios.controlador.php";
require_once "../models/negocios.modelo.php";

class ComboMunicipios
{

	public $id;
	public function mostrarComboMunicipios(){

	   $valor = $this->id;
	   $Consulta = ModeloNegocios::MdlJsonObtenerMunicipios($valor);
	   echo ($Consulta); 
       
	}
}


$Mostar = new ComboMunicipios();
$Mostar -> id = $_POST['id'];
$Mostar -> mostrarComboMunicipios();