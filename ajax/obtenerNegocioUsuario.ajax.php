<?php

require_once "../controllers/negocios.controlador.php";
require_once "../models/negocios.modelo.php";

class AjaxNegocios
{
/**
 *Editar Usuario
 */
	public $idNegocio;

	public function ajaxEditarNegocio(){

		$valor = $this->idNegocio;
		$respuesta = ModeloNegocios::MdlObtenerNegocioUsuario($valor);
		echo json_encode ($respuesta);
	}

}

if(isset($_POST["idNegocio"])){

	$Editar = new AjaxNegocios();
	$Editar -> idNegocio = $_POST["idNegocio"];
	$Editar -> ajaxEditarNegocio();

}