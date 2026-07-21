<?php

require_once "../controllers/clasificaciones.controlador.php";
require_once "../models/clasificaciones.modelo.php";

class AjaxClasificaciones
{
/**
 *Editar Marca
 */
	public $idClasificacion;

	public function ajaxEditarClasificacion(){

		$valor = $this->idClasificacion;
		$respuesta = ModeloClasificaciones::MdlObtenerClasificacion($valor);
		echo json_encode($respuesta);
	}

}

if(isset($_POST["id_clasificacion"])){

	$Editar = new AjaxClasificaciones();
	$Editar -> idClasificacion = $_POST["id_clasificacion"];
	$Editar -> ajaxEditarClasificacion();

}