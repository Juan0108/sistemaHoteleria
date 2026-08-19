<?php

require_once "../controllers/hoteles.controlador.php";
require_once "../models/hoteles.modelo.php";

class AjaxHoteles
{
/**
 *Editar Usuario
 */
	public $idHotel;

	public function ajaxEditarHotel(){

		$valor = $this->idHotel;
		$respuesta = ModeloHoteles::MdlObtenerHotel($valor);
		echo json_encode($respuesta);
	}

	public function ajaxEliminarHotel(){

		echo ControladorHoteles::ctrEliminarHotel();
	}

}

if(isset($_POST["idHotel"])){

	$Editar = new AjaxHoteles();
	$Editar -> idHotel = $_POST["idHotel"];
	$Editar -> ajaxEditarHotel();

}

if(isset($_POST["idEliminarHotel"])){

	$Eliminar = new AjaxHoteles();
	$Eliminar -> ajaxEliminarHotel();

}