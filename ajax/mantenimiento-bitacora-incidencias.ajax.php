<?php

session_start();
header('Content-Type: application/json');

require_once "../controllers/mantenimiento.controlador.php";
require_once "../models/mantenimiento.modelo.php";

class BitacoraIncidenciasAjax
{
	public function ajaxBitacora()
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

		$bitacora = ControladorMantenimiento::crtObtenerBitacoraIncidencias($id_mantenimiento);

		if($bitacora === null){
			http_response_code(400);
			echo json_encode([
				"status" => "error",
				"message" => "No se encontró el hotel de tu negocio"
			]);
			return;
		}

		echo json_encode([
			"status" => "success",
			"data" => $bitacora
		]);
	}
}

$ajax = new BitacoraIncidenciasAjax();
$ajax->ajaxBitacora();
