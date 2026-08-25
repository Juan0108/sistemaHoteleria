<?php

session_start();
header('Content-Type: application/json');

require_once "../controllers/mantenimiento.controlador.php";
require_once "../models/mantenimiento.modelo.php";

class InfoRegistroAjax
{
	public function ajaxInfo()
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

		$info = ControladorMantenimiento::crtObtenerInfoRegistroIncidencia($id_mantenimiento);

		if($info === null){
			http_response_code(400);
			echo json_encode([
				"status" => "error",
				"message" => "No se encontró la incidencia"
			]);
			return;
		}

		echo json_encode([
			"status" => "success",
			"data" => $info
		], JSON_UNESCAPED_UNICODE);
	}
}

$ajax = new InfoRegistroAjax();
$ajax->ajaxInfo();
