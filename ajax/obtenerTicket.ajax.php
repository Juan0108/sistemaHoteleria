<?php

require_once "../controllers/ventas.controlador.php";
require_once "../models/ventas.modelo.php";

class AjaxObtenerTicket 
{
/**
 *Editar Marca
 */
	public $Idusuario;

	public function AjaxObtenerNticket(){

		$vIdUsuario = $this->Idusuario;
		$respuesta = ModeloVentas::MdlObtenerNuevoTicket($vIdUsuario);
		echo json_encode($respuesta);
	}

}

if(isset($_POST["idUsuario"])){

	$Editar = new AjaxObtenerTicket();
	$Editar -> Idusuario = $_POST["idUsuario"];
	$Editar -> AjaxObtenerNticket();

}