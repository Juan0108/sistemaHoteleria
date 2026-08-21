<?php

session_start();
header('Content-Type: application/json');

require_once "../controllers/mantenimiento.controlador.php";
require_once "../models/mantenimiento.modelo.php";

class MoverOrdenMantenimientoAjax
{
	public function ajaxMoverOrden()
	{
		$id_mantenimiento = isset($_POST["idMantenimiento"]) ? (int) $_POST["idMantenimiento"] : 0;
		$direccion = $_POST["direccion"] ?? "";

		if($id_mantenimiento <= 0 || !in_array($direccion, ["arriba", "abajo"], true)){
			http_response_code(400);
			echo json_encode([
				"status" => "error",
				"message" => "Faltan datos para reordenar la incidencia"
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

		$respuesta = ModeloMantenimiento::MdlMoverOrdenMantenimiento($id_mantenimiento, $id_hotel, $direccion);

		if($respuesta && $respuesta["Id_Vecino"] !== null){
			echo json_encode([
				"status" => "success",
				"message" => "Orden actualizado correctamente"
			]);
		}else{
			echo json_encode([
				"status" => "sin_cambios",
				"message" => "La incidencia ya está en un extremo de la columna"
			]);
		}
	}
}

if(!empty($_POST)){
	$ajax = new MoverOrdenMantenimientoAjax();
	$ajax->ajaxMoverOrden();
}else{
	http_response_code(400);
	echo json_encode([
		"status" => "error",
		"message" => "Faltan datos para reordenar la incidencia"
	]);
}
