<?php


require_once "../controllers/submarcas.controlador.php";
require_once "../models/submarcas.modelo.php";

class ComboSubMarcas
{

	public $id;
	public function mostrarComboSubMarcas(){

	   $valor = $this->id;
	   $Consulta = ModeloSubMarcas::MdlJsonObtenerSubMarcasCategoria($valor);
	   echo ($Consulta); 
       
	}
}


$Mostar = new ComboSubMarcas();
$Mostar -> id = $_POST['id'];
$Mostar -> mostrarComboSubMarcas();