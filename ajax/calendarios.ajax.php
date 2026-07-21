<?php

require_once "../controllers/calendarios.controlador.php";
require_once "../models/calendarios.modelo.php";

class AjaxCalendarios
{
/**
 *Editar Marca
 */
    
    public $idNegocio;
	public $idEvento;

	public function ajaxEditarCalendario(){
        
        $_idNegocio = $this->idNegocio;
		$_idEvento = $this->idEvento;
		$respuesta = ModeloCalendarios::MdlObtenerCalendario($_idNegocio, $_idEvento);
		echo json_encode($respuesta);
	}

}

if(isset($_POST["IdNegocio"], $_POST["Id_Evento"])){

	$Editar = new AjaxCalendarios();
	$Editar -> idNegocio = $_POST["IdNegocio"];
    $Editar -> idEvento = $_POST["Id_Evento"];
	$Editar -> ajaxEditarCalendario();

}