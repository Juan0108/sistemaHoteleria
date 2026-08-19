<?php

session_start();
header('Content-Type: application/json');

require_once "../controllers/habitaciones.controlador.php";
require_once "../models/habitaciones.modelo.php";

class SuspenderHabitacionAjax
{
    public $idHabitacion;

    private function obtenerIdHabitacionDesdeSolicitud()
    {
        if (isset($this->idHabitacion) && $this->idHabitacion !== "") {
            return $this->idHabitacion;
        }

        if (isset($_POST["idHabitacion"]) && $_POST["idHabitacion"] !== "") {
            return $_POST["idHabitacion"];
        }

        if (isset($_REQUEST["idHabitacion"]) && $_REQUEST["idHabitacion"] !== "") {
            return $_REQUEST["idHabitacion"];
        }

        return null;
    }

    public function ajaxSuspenderHabitacion()
    {
        $id_habitacion = (int) $this->obtenerIdHabitacionDesdeSolicitud();

        if ($id_habitacion <= 0) {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => "Faltan datos para inhabilitar la habitación"
            ]);
            return;
        }

        $id_hotel = ControladorHabitaciones::crtObtenerIdHotelSesion();

        if ($id_hotel === null) {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => "No se encontró el hotel de tu negocio"
            ]);
            return;
        }

        $respuesta = ModeloHabitaciones::MdlSuspenderHabitacion($id_habitacion, $id_hotel);

        if ($respuesta) {
            echo json_encode([
                "status" => "success",
                "message" => "Habitación inhabilitada correctamente"
            ]);
        } else {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => "No se pudo inhabilitar la habitación"
            ]);
        }
    }
}

if (!empty($_POST) || !empty($_REQUEST)) {
    $suspender = new SuspenderHabitacionAjax();
    $suspender->idHabitacion = null;
    $suspender->ajaxSuspenderHabitacion();
} else {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "message" => "Faltan datos para inhabilitar la habitación"
    ]);
}
