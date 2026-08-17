<?php
session_start();

require_once "../controllers/negocios.controlador.php";
require_once "../models/negocios.modelo.php";

class ComboNegocioSesion
{

    public function mostrarComboNegocioSesion(){

        // Definimos el encabezado JSON antes de imprimir cualquier dato
        header('Content-Type: application/json; charset=utf-8');

        // El Id_Negocio SIEMPRE se toma de la sesión del usuario logeado, nunca
        // de un parámetro enviado por el cliente, para que no se pueda manipular
        // y ver/elegir negocios u hoteles ajenos.
        ControladorHoteles::crtJsonObtenerNegocioSesion($_SESSION["IdNegocio"]);

    }
}

$Mostrar = new ComboNegocioSesion();
$Mostrar->mostrarComboNegocioSesion();
