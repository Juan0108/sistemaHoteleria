<?php


require_once "../controllers/negocios.controlador.php";
require_once "../models/negocios.modelo.php";

class ComboEstatus
{

	public function mostrarComboEstatus(){

	   $Consulta = ControladorHoteles::crtJsonObtenerEstatus();
	   echo ($Consulta);	 
       
	}
}


$Mostar = new ComboEstatus();
$Mostar -> mostrarComboEstatus();