<?php


require_once "../controllers/negocios.controlador.php";
require_once "../models/negocios.modelo.php";

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