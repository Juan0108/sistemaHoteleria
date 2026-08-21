<?php

session_start();
header('Content-Type: application/json');

require_once "../controllers/mantenimiento.controlador.php";
require_once "../models/mantenimiento.modelo.php";

class CambiarEstatusMantenimientoAjax
{
	public function ajaxCambiarEstatus()
	{
		$id_mantenimiento = isset($_POST["idMantenimiento"]) ? (int) $_POST["idMantenimiento"] : 0;
		$id_estatus = isset($_POST["idEstatus"]) ? (int) $_POST["idEstatus"] : 0;

		$estatusValidos = [
			ControladorMantenimiento::ESTATUS_PENDIENTE,
			ControladorMantenimiento::ESTATUS_PROCESO,
			ControladorMantenimiento::ESTATUS_RESUELTO,
		];

		if($id_mantenimiento <= 0 || !in_array($id_estatus, $estatusValidos, true)){
			http_response_code(400);
			echo json_encode([
				"status" => "error",
				"message" => "Faltan datos para actualizar la incidencia"
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

		$respuesta = ModeloMantenimiento::MdlActualizarEstatusMantenimiento($id_mantenimiento, $id_hotel, $id_estatus);

		if($respuesta && (int) $respuesta["Afectados"] > 0){

			// Se regresa la tarjeta ya renderizada en su nuevo estado para que el
			// JS solo la mueva de columna, sin recargar toda la página.
			$fila = ControladorMantenimiento::crtObtenerFilaTablero($id_mantenimiento);

			echo json_encode([
				"status" => "success",
				"message" => "Incidencia actualizada correctamente",
				"columna" => $fila["columna"] ?? null,
				"html" => $fila["html"] ?? null
			]);
		}else{
			http_response_code(400);
			echo json_encode([
				"status" => "error",
				"message" => "No se pudo actualizar la incidencia"
			]);
		}
	}
}

if(!empty($_POST)){
	$ajax = new CambiarEstatusMantenimientoAjax();
	$ajax->ajaxCambiarEstatus();
}else{
	http_response_code(400);
	echo json_encode([
		"status" => "error",
		"message" => "Faltan datos para actualizar la incidencia"
	]);
}
