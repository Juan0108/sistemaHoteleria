<?php


require_once "../controllers/negocios.controlador.php";
require_once "../models/negocios.modelo.php";

class ComboEstados
{

	public function mostrarComboEstados(){

	   $Consulta = ControladorHoteles::crtJsonObtenerEstados();
	   echo ($Consulta);
       
	}
}


$Mostar = new ComboEstados();
$Mostar -> mostrarComboEstados();