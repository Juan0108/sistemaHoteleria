<?php


require_once "../controllers/negocios.controlador.php";
require_once "../models/negocios.modelo.php";

class ComboEstados
{

	public function mostrarEstados(){

	   $Consulta = ControladorNegocios::crtJsonObtenerEstados();
	   echo ($Consulta);
       
	}
}


$Mostar = new ComboEstados();
$Mostar -> mostrarEstados();