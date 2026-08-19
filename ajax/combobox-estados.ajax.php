<?php


require_once "../controllers/hoteles.controlador.php";
require_once "../models/hoteles.modelo.php";

class ComboEstados
{

	public function mostrarComboEstados(){

	   $Consulta = ControladorHoteles::crtJsonObtenerEstados();
	   echo ($Consulta);
       
	}
}


$Mostar = new ComboEstados();
$Mostar -> mostrarComboEstados();