<?php

require_once "../controllers/marcas.controlador.php";
require_once "../models/marcas.modelo.php";

class AjaxMarcas 
{
/**
 *Editar Marca
 */
	public $idMarca;

	public function ajaxEditarMarca(){

		$valor = $this->idMarca;
		$respuesta = ModeloMarcas::MdlObtenerMarca($valor);
		echo json_encode($respuesta);
	}

}

if(isset($_POST["id_marca"])){

	$Editar = new AjaxMarcas();
	$Editar -> idMarca = $_POST["id_marca"];
	$Editar -> ajaxEditarMarca();

}