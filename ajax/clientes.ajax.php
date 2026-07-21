<?php

require_once "../controllers/clientes.controlador.php";
require_once "../models/clientes.modelo.php";

class AjaxClientes
{
    public $idCliente;

    public function ajaxEditarCliente()
    {
        $valor = $this->idCliente;
        $respuesta = ModeloClientes::MdlObtenerCliente($valor);
        echo json_encode($respuesta);
    }
}

if (isset($_POST["idCliente"])) {
    $editar = new AjaxClientes();
    $editar->idCliente = $_POST["idCliente"];
    $editar->ajaxEditarCliente();
}
