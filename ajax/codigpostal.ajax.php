<?php

require_once "../controllers/hoteles.controlador.php";
require_once "../models/hoteles.modelo.php";

class AjaxCodigoPostal 
{
/**
 *Editar Usuario
 */
	public $id;

	public function ajaxCP(){

		$valor = $this->id;
		$respuesta = ModeloHoteles::MdlObtenerCodigoPostal($valor);
		echo json_encode($respuesta);
	}

}

$Mostar = new AjaxCodigoPostal();
$Mostar -> id = $_POST['id'];
$Mostar -> ajaxCP();