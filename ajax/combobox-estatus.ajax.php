<?php


require_once "../controllers/hoteles.controlador.php";
require_once "../models/hoteles.modelo.php";

class ComboEstatus
{

	public function mostrarComboEstatus(){

	   $Consulta = ControladorHoteles::crtJsonObtenerEstatus();
	   echo ($Consulta);	 
       
	}
}


$Mostar = new ComboEstatus();
$Mostar -> mostrarComboEstatus();