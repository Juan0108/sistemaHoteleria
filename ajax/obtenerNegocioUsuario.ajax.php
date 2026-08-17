<?php

require_once "../controllers/negocios.controlador.php";
require_once "../models/negocios.modelo.php";

class AjaxHoteles
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

	$Editar = new AjaxHoteles();
	$Editar -> idHotel = $_POST["idHotel"];
	$Editar -> ajaxEditarHotel();

}