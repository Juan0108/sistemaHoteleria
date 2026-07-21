<?php

require_once "../controllers/productos.controlador.php";
require_once "../models/productos.modelo.php";

class AjaxProductos 
{
/**
 *Editar Marca
 */
	public $idProducto;

	public function ajaxEditarProducto(){

		$valor = $this->idProducto;
		$respuesta = ModeloProductos::MdlObtenerProducto($valor);
		echo json_encode($respuesta);
	}

}

if(isset($_POST["id_producto"])){

	$Editar = new AjaxProductos();
	$Editar -> idProducto = $_POST["id_producto"];
	$Editar -> ajaxEditarProducto();

}