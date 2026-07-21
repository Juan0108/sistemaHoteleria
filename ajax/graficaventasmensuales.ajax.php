<?php

require_once "../controllers/ventas.controlador.php";
require_once "../models/ventas.modelo.php";

class AjaxGrafica 
{
/**
 *Editar Marca
 */
	public $id;

	public function AjaxGraficaVentas(){

		$idusuario = $this->id;
		$respuesta = ControladorVentas::crtObtenerGraficaVentas($idusuario);
		echo ($respuesta);
	}

}

if(isset($_POST["id"])){

	$Editar = new AjaxGrafica();
	$Editar -> id = $_POST['id'];
	$Editar -> AjaxGraficaVentas();

}