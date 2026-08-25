<?php

session_start();
header('Content-Type: application/json');

require_once "../controllers/mantenimiento.controlador.php";
require_once "../models/mantenimiento.modelo.php";

class HistorialTransicionesAjax
{
	public function ajaxHistorial()
	{
		$id_habitacion = isset($_REQUEST["idHabitacion"]) ? (int) $_REQUEST["idHabitacion"] : 0;

		if($id_habitacion <= 0){
			http_response_code(400);
			echo json_encode([
				"status" => "error",
				"message" => "Falta el identificador de la habitación"
			]);
			return;
		}

		$historial = ControladorMantenimiento::crtObtenerHistorialTransicionesHabitacion($id_habitacion);

		if($historial === null){
			http_response_code(400);
			echo json_encode([
				"status" => "error",
				"message" => "No se encontró el hotel de tu negocio"
			]);
			return;
		}

		echo json_encode([
			"status" => "success",
			"data" => $historial
		]);
	}
}

$ajax = new HistorialTransicionesAjax();
$ajax->ajaxHistorial();
