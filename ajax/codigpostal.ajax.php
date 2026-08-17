<?php

require_once "../controllers/negocios.controlador.php";
require_once "../models/negocios.modelo.php";

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