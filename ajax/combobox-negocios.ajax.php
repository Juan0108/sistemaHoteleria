<?php


require_once "../controllers/negocios.controlador.php";
require_once "../models/negocios.modelo.php";

class ComboNegocios
{

	public function mostrarComboNegocios(){

	   $Consulta = ControladorNegocios::crtJsonObtenerNegocios();
	   echo ($Consulta);	 
       
	}
}


$Mostar = new ComboNegocios();
$Mostar -> mostrarComboNegocios();