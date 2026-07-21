<?php

require_once "../controllers/categorias.controlador.php";
require_once "../models/categorias.modelo.php";

class AjaxCategorias 
{
/**
 *Editar Usuario
 */
	public $idCategoria;

	public function ajaxEditarCategoria(){

		$valor = $this->idCategoria;
		$respuesta = ModeloCategorias::MdlObtenerCategoria($valor);
		echo json_encode($respuesta);
	}

}

if(isset($_POST["id_categoria"])){

	$Editar = new AjaxCategorias();
	$Editar -> idCategoria = $_POST["id_categoria"];
	$Editar -> ajaxEditarCategoria();

}