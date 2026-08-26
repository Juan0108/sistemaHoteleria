<?php

session_start();
header('Content-Type: application/json');

require_once "../controllers/mantenimiento.controlador.php";
require_once "../models/mantenimiento.modelo.php";

class RestaurarMantenimientoAjax
{
	public function ajaxRestaurar()
	{
		$id_mantenimiento = isset($_POST["idMantenimiento"]) ? (int) $_POST["idMantenimiento"] : 0;

		if($id_mantenimiento <= 0){
			http_response_code(400);
			echo json_encode([
				"status" => "error",
				"message" => "Falta el identificador de la incidencia"
			]);
			return;
		}

		$archivoFoto = $_FILES["foto"] ?? null;
		$respuesta = ControladorMantenimiento::crtRestaurarMantenimiento($id_mantenimiento, $archivoFoto);

		if($respuesta["status"] !== "success"){
			http_response_code(400);
		}

		echo json_encode($respuesta);
	}
}

if(!empty($_POST)){
	$ajax = new RestaurarMantenimientoAjax();
	$ajax->ajaxRestaurar();
}else{
	http_response_code(400);
	echo json_encode([
		"status" => "error",
		"message" => "Falta el identificador de la incidencia"
	]);
}
