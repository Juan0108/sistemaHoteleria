<?php


require_once "../controllers/categorias.controlador.php";
require_once "../models/categorias.modelo.php";

class ComboCategorias
{

	public function mostrarComboCategorias(){

	   $Consulta = ControladorCategorias::crtJsonObtenerCategorias();
	   echo ($Consulta);	 
       
	}
}


$Mostar = new ComboCategorias();
$Mostar -> mostrarComboCategorias();