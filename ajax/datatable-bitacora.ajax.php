<?php
session_start();
require_once "../controllers/bitacora.controlador.php";
require_once "../models/bitacora.modelo.php";

$id = $_SESSION["IdUsuario"]; // aquí obtienes el id del Usuario
$respuesta = ControladorBitacora::crtObtenerBitacoraInvetarios($id);

$datosJson = '{
    "data": [';

    for($i = 0; $i < count($respuesta); $i++){

        $datosJson .= '[
            "'.$respuesta[$i]["NombreCompleto"].'",
            "'.$respuesta[$i]["accion"].'",
            "'.$respuesta[$i]["fecha"].'",
            "'.$respuesta[$i]["detalle"].'"
        ],';
    }

$datosJson = substr($datosJson, 0, -1);
$datosJson .= ']}';

echo $datosJson;
