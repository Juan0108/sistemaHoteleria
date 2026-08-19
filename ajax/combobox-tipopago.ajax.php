<?php


require_once "../controllers/hoteles.controlador.php";
require_once "../models/hoteles.modelo.php";

class ComboTipoPago
{

	public function mostrarComboTipoPago(){

	   $Consulta = ControladorHoteles::crtJsonObtenerTipoPago();
	   echo ($Consulta);	 
       
	}
}


$Mostar = new ComboTipoPago();
$Mostar -> mostrarComboTipoPago();