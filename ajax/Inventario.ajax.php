<?php

require_once "../controllers/inventarios.controlador.php";
require_once "../models/inventarios.modelo.php";

class AjaxInventario 
{
/**
 *Editar Marca
 */
	public $idQbarra;
	public $idUsuario;

	public function ajaxObtenerInventario(){

		$valor = $this->idQbarra;
		$valorN = $this->idUsuario;
		$respuesta = ModeloInventarios::MdlObtenerInventario($valorN,$valor);
		echo json_encode($respuesta);
	}

}

if(isset($_POST["id"])){

	$Editar = new AjaxInventario();
	$Editar -> idQbarra = $_POST["id"];
	$Editar -> idUsuario = $_POST["idN"];
	$Editar -> ajaxObtenerInventario();

}