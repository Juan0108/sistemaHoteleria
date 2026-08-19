<?php


require_once "../controllers/hoteles.controlador.php";
require_once "../models/hoteles.modelo.php";

class ComboColonias
{

	public $id;
	public function mostrarComboColonias(){

	   $valor = $this->id;
	   $Consulta = ModeloHoteles::MdlJsonObtenerColonias($valor);
	   echo ($Consulta); 
       
	}
}


$Mostar = new ComboColonias();
$Mostar -> id = $_POST['id'];
$Mostar -> mostrarComboColonias();