<?php

session_start();
header('Content-Type: application/json');

require_once "../controllers/mantenimiento.controlador.php";
require_once "../models/mantenimiento.modelo.php";

class HistorialIncidenciasAjax
{
	public function ajaxHistorial()
	{
		$id_habitacion = isset($_REQUEST["idHabitacion"]) ? (int) $_REQUEST["idHabitacion"] : 0;

		// Sin habitación seleccionada (filtro sin tocar): historial de todo el hotel junto.
		$historial = $id_habitacion > 0
			? ControladorMantenimiento::crtObtenerHistorialIncidenciasHabitacion($id_habitacion)
			: ControladorMantenimiento::crtObtenerHistorialIncidenciasHotel();

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

$ajax = new HistorialIncidenciasAjax();
$ajax->ajaxHistorial();
