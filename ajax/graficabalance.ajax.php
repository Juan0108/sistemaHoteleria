<?php

require_once "../controllers/ventas.controlador.php";
require_once "../models/ventas.modelo.php";

class AjaxBalance 
{
/**
 *Editar Marca
 */
	public $id;

	public function AjaxBalanceVentas(){

		$idusuario = $this->id;
		$respuesta = ControladorVentas::crtObtenerGraficaBalance($idusuario);
		echo ($respuesta);
	}

}

if(isset($_POST["id"])){

	$Editar = new AjaxBalance();
	$Editar -> id = $_POST['id'];
	$Editar -> AjaxBalanceVentas();

}