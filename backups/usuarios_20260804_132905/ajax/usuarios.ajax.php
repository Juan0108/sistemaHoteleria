<?php

require_once "../controllers/usuarios.controlador.php";
require_once "../models/usuarios.modelo.php";

class AjaxUsuarios 
{
/**
 *Editar Usuario
 */
	public $idUsuario;

	public function ajaxEditarUsuario(){

		$valor = $this->idUsuario;
		$respuesta = ModeloUsuarios::MdlObtenerUsuario($valor);
		echo json_encode($respuesta);
	}

}

if(isset($_POST["idUsuario"])){

	$Editar = new AjaxUsuarios();
	$Editar -> idUsuario = $_POST["idUsuario"];
	$Editar -> ajaxEditarUsuario();

}