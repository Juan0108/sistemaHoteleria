<?php


require_once "../controllers/hoteles.controlador.php";
require_once "../models/hoteles.modelo.php";

class ComboGiro
{

	public function mostrarComboGiro(){

	   $Consulta = ControladorHoteles::crtJsonObtenerGiro();
	   echo ($Consulta);	 
       
	}
}


$Mostar = new ComboGiro();
$Mostar -> mostrarComboGiro();