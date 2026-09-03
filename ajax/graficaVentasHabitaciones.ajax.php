<?php
session_start();

require_once "../controllers/habitaciones.controlador.php";
require_once "../models/habitaciones.modelo.php";

class AjaxVentasHabitaciones
{

    public function mostrar(){

        header('Content-Type: application/json; charset=utf-8');

        // El hotel se resuelve del negocio en sesión (crtObtenerIdHotelSesion), nunca de
        // un parámetro enviado por el cliente. El año sí es opcional por si en el futuro
        // se agrega un selector de año en el dashboard; sin él, usa el año en curso.
        $anio = isset($_POST["anio"]) ? $_POST["anio"] : null;
        $respuesta = ControladorHabitaciones::crtObtenerVentasPorTipoHabitacionMensual($anio);
        echo json_encode($respuesta);

    }
}

$Mostrar = new AjaxVentasHabitaciones();
$Mostrar->mostrar();
