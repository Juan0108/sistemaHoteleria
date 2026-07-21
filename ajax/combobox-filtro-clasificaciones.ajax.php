<?php


require_once "../controllers/clasificaciones.controlador.php";
require_once "../models/clasificaciones.modelo.php";

class ComboClasificaciones
{

	public $id;
	public function mostrarComboClasifiaciones(){

	   $valor = $this->id;
	   $Consulta = ModeloClasificaciones::MdlJsonObtenerClasificaciones($valor);
	   echo ($Consulta); 
       
	}
}


$Mostar = new ComboClasificaciones();
$Mostar -> id = $_POST['id'];
$Mostar -> mostrarComboClasifiaciones();