<?php

session_start();
header('Content-Type: application/json');

require_once "../controllers/mantenimiento.controlador.php";
require_once "../models/mantenimiento.modelo.php";

class EliminarMantenimientoAjax
{
	public function ajaxEliminar()
	{
		$id_mantenimiento = isset($_POST["idMantenimiento"]) ? (int) $_POST["idMantenimiento"] : 0;
		$id_motivo = isset($_POST["idMotivo"]) ? (int) $_POST["idMotivo"] : 0;

		if($id_mantenimiento <= 0 || $id_motivo <= 0){
			http_response_code(400);
			echo json_encode([
				"status" => "error",
				"message" => "Selecciona el motivo de la eliminación"
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

		$respuesta = ModeloMantenimiento::MdlEliminarMantenimiento($id_mantenimiento, $id_hotel, $id_motivo);

		if($respuesta && (int) $respuesta["Afectados"] > 0){
			echo json_encode([
				"status" => "success",
				"message" => "Incidencia eliminada correctamente"
			]);
		}else{
			http_response_code(400);
			echo json_encode([
				"status" => "error",
				"message" => "No se pudo eliminar la incidencia"
			]);
		}
	}
}

if(!empty($_POST)){
	$ajax = new EliminarMantenimientoAjax();
	$ajax->ajaxEliminar();
}else{
	http_response_code(400);
	echo json_encode([
		"status" => "error",
		"message" => "Falta el identificador de la incidencia"
	]);
}
