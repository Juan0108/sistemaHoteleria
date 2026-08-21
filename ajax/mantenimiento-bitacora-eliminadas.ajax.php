<?php

session_start();
header('Content-Type: application/json');

require_once "../controllers/mantenimiento.controlador.php";
require_once "../models/mantenimiento.modelo.php";

class BitacoraEliminadasAjax
{
	public function ajaxBitacora()
	{
		$bitacora = ControladorMantenimiento::crtObtenerBitacoraEliminadas();

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

$ajax = new BitacoraEliminadasAjax();
$ajax->ajaxBitacora();
