<?php
session_start();
require_once "../controllers/clientes.controlador.php";
require_once "../models/clientes.modelo.php";

$id = $_SESSION["IdNegocio"]; // aquí obtienes el id del negocio
$respuesta = ControladorClientes::ctrMostrarClientes("Negocio", $id);

$datosJson = '{
    "data": [';

    for($i = 0; $i < count($respuesta); $i++){

        $Boton =  "<div class='checkbox'><button class='btn btn btn-info btnEditarCliente' data-id-cliente='".$respuesta[$i]["id_Cliente"]."'data-toggle='modal' data-target='#modalEditarCliente'><i class='fa fa-pencil'></i> </button></div>";

        $datosJson .= '[
            "'.$respuesta[$i]["id_Cliente"].'",
            "'.$respuesta[$i]["Nombre"].'",
            "'.$respuesta[$i]["APaterno"].'",
            "'.$respuesta[$i]["AMaterno"].'",
            "'.$respuesta[$i]["Telefono"].'",
            "'.$Boton.'"
        ],';
    }

$datosJson = substr($datosJson, 0, -1);
$datosJson .= ']}';

echo $datosJson;
