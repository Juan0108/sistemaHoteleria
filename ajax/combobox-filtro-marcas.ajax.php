<?php


require_once "../controllers/marcas.controlador.php";
require_once "../models/marcas.modelo.php";

class ComboMarcas
{

	public $id;
	public function mostrarComboMarcas(){

	   $valor = $this->id;
	   $Consulta = ModeloMarcas::MdlJsonObtenerMarcasCategoria($valor);
	   echo ($Consulta); 
       
	}
}


$Mostar = new ComboMarcas();
$Mostar -> id = $_POST['id'];
$Mostar -> mostrarComboMarcas();