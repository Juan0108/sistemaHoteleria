<?php

session_start();
header('Content-Type: application/json');

require_once "../controllers/mantenimiento.controlador.php";
require_once "../models/mantenimiento.modelo.php";

class AbonosMantenimientoAjax
{
	public function ajaxAbonos()
	{
		$id_mantenimiento = isset($_REQUEST["idMantenimiento"]) ? (int) $_REQUEST["idMantenimiento"] : 0;

		if($id_mantenimiento <= 0){
			http_response_code(400);
			echo json_encode([
				"status" => "error",
				"message" => "Falta el identificador de la incidencia"
			]);
			return;
		}

		$abonos = ControladorMantenimiento::crtObtenerAbonos($id_mantenimiento);

		if($abonos === null){
			http_response_code(404);
			echo json_encode([
				"status" => "error",
				"message" => "No se encontró la incidencia"
			]);
			return;
		}

		echo json_encode([
			"status" => "success",
			"data" => $abonos
		]);
	}
}

$ajax = new AbonosMantenimientoAjax();
$ajax->ajaxAbonos();
