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

		$id_hotel = ControladorMantenimiento::crtObtenerIdHotelSesion();

		if($id_hotel === null){
			http_response_code(400);
			echo json_encode([
				"status" => "error",
				"message" => "No se encontró el hotel de tu negocio"
			]);
			return;
		}

		$respuesta = ModeloMantenimiento::MdlRestaurarMantenimiento($id_mantenimiento, $id_hotel);

		if($respuesta && (int) $respuesta["Afectados"] > 0){
			echo json_encode([
				"status" => "success",
				"message" => "Incidencia restaurada correctamente"
			]);
		}else{
			http_response_code(400);
			echo json_encode([
				"status" => "error",
				"message" => "No se pudo restaurar la incidencia"
			]);
		}
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
