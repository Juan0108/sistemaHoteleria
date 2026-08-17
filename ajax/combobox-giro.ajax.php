<?php


require_once "../controllers/negocios.controlador.php";
require_once "../models/negocios.modelo.php";

class ComboGiro
{

	public function mostrarComboGiro(){

	   $Consulta = ControladorHoteles::crtJsonObtenerGiro();
	   echo ($Consulta);	 
       
	}
}


$Mostar = new ComboGiro();
$Mostar -> mostrarComboGiro();