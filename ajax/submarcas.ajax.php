<?php

require_once "../controllers/submarcas.controlador.php";
require_once "../models/submarcas.modelo.php";

class AjaxSubMarcas 
{
/**
 *Editar Marca
 */
	public $idSubMarca;

	public function ajaxEditarSubMarca(){

		$valor = $this->idSubMarca;
		$respuesta = ModeloSubMarcas::MdlObtenerSubMarca($valor);
		echo json_encode($respuesta);
	}

}

if(isset($_POST["id_submarca"])){

	$Editar = new AjaxSubMarcas();
	$Editar -> idSubMarca = $_POST["id_submarca"];
	$Editar -> ajaxEditarSubMarca();

}