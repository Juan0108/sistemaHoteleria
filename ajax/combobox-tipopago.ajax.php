<?php


require_once "../controllers/negocios.controlador.php";
require_once "../models/negocios.modelo.php";

class ComboTipoPago
{

	public function mostrarComboTipoPago(){

	   $Consulta = ControladorNegocios::crtJsonObtenerTipoPago();
	   echo ($Consulta);	 
       
	}
}


$Mostar = new ComboTipoPago();
$Mostar -> mostrarComboTipoPago();