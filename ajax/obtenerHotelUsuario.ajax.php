<?php

require_once "../controllers/hoteles.controlador.php";
require_once "../models/hoteles.modelo.php";

class AjaxHotelUsuario
{
/**
 *Editar Usuario
 */
	public $idHotel;

	public function ajaxEditarHotel(){

		$valor = $this->idHotel;
		$respuesta = ModeloHoteles::MdlObtenerHotelUsuario($valor);
		echo json_encode ($respuesta);
	}

}

if(isset($_POST["idHotel"])){

	$Editar = new AjaxHotelUsuario();
	$Editar -> idHotel = $_POST["idHotel"];
	$Editar -> ajaxEditarHotel();

}