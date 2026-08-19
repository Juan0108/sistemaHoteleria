<?php
session_start();

require_once "../controllers/negocios.controlador.php";
require_once "../models/negocios.modelo.php";

class AjaxHabitacionesOcupadas
{

    public function mostrarHabitacionesOcupadas(){

        header('Content-Type: application/json; charset=utf-8');

        // El Id_Negocio SIEMPRE se toma de la sesión del usuario logeado, nunca
        // de un parámetro enviado por el cliente, para que no se pueda manipular
        // y ver habitaciones de un hotel ajeno.
        $respuesta = ControladorHoteles::crtJsonObtenerHabitacionesOcupadas($_SESSION["IdNegocio"]);
        echo json_encode($respuesta);

    }

}

$Mostrar = new AjaxHabitacionesOcupadas();
$Mostrar->mostrarHabitacionesOcupadas();
