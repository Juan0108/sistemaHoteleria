<?php


require_once "../controllers/marcas.controlador.php";
require_once "../models/marcas.modelo.php";

class ComboMarcas
{

	public function mostrarComboMarcas(){

	   $Consulta = ControladorMarcas::crtJsonObtenerMarcas();
	   echo ($Consulta);	 
       
	}
}


$Mostar = new ComboMarcas();
$Mostar -> mostrarComboMarcas();