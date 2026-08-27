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

		$archivoFoto = null;

		if($id_estatus === ControladorMantenimiento::ESTATUS_RESUELTO){
			$archivoFoto = $_FILES["fotoResuelto"] ?? null;

			if(!is_array($archivoFoto) || empty($archivoFoto["tmp_name"])){
				http_response_code(400);
				echo json_encode(["status" => "error", "message" => "Debes adjuntar la foto de cómo quedó resuelta la incidencia"]);
				return;
			}
		}elseif($id_estatus === ControladorMantenimiento::ESTATUS_PENDIENTE){
			$archivoFoto = $_FILES["fotoReapertura"] ?? null;

			if(!is_array($archivoFoto) || empty($archivoFoto["tmp_name"])){
				http_response_code(400);
				echo json_encode(["status" => "error", "message" => "Debes adjuntar una foto para reabrir la incidencia"]);
				return;
			}

			// El presupuesto (costo + fechas) también se valida ANTES de tocar el estatus,
			// igual que la foto: si algo falla aquí, la incidencia no debe quedar reabierta
			// a medias sin su nuevo presupuesto.
			$costoReapertura = $_POST["costoReapertura"] ?? "";
			$fechaInicioReapertura = $_POST["fechaInicioReapertura"] ?? "";
			$fechaFinReapertura = $_POST["fechaFinReapertura"] ?? "";

			$validacionPresupuesto = ControladorMantenimiento::crtValidarPresupuestoReapertura($costoReapertura, $fechaInicioReapertura, $fechaFinReapertura);

			if($validacionPresupuesto["status"] !== "success"){
				http_response_code(400);
				echo json_encode($validacionPresupuesto);
				return;
			}
		}

		// El estatus se actualiza PRIMERO (eso es lo que inserta el nuevo renglón en el
		// historial de esta transición); la foto se guarda después, para que quede
		// registrada en ESE renglón nuevo y no en el de la transición anterior.
		$respuesta = ModeloMantenimiento::MdlActualizarEstatusMantenimiento($id_mantenimiento, $id_hotel, $id_estatus);

		if(!$respuesta || (int) $respuesta["Afectados"] <= 0){
			http_response_code(400);
			echo json_encode([
				"status" => "error",
				"message" => "No se pudo actualizar la incidencia"
			]);
			return;
		}

		if($id_estatus === ControladorMantenimiento::ESTATUS_RESUELTO){
			$resultadoFoto = ControladorMantenimiento::crtGuardarFotoResuelta($id_mantenimiento, $archivoFoto);

			if($resultadoFoto["status"] !== "success"){
				http_response_code(400);
				echo json_encode($resultadoFoto);
				return;
			}
		}elseif($id_estatus === ControladorMantenimiento::ESTATUS_PENDIENTE){
			$resultadoFotoReapertura = ControladorMantenimiento::crtGuardarFotoReapertura($id_mantenimiento, $archivoFoto);

			if($resultadoFotoReapertura["status"] !== "success"){
				http_response_code(400);
				echo json_encode($resultadoFotoReapertura);
				return;
			}

			// Reabrir: guarda el motivo capturado en el prompt del botón "Reabrir"
			if(!empty($_POST["notaReapertura"])){
				ControladorMantenimiento::crtActualizarNotaReapertura($id_mantenimiento, $_POST["notaReapertura"]);
			}

			// El presupuesto ya se validó arriba, antes de tocar el estatus; aquí solo se guarda.
			$resultadoPresupuesto = ControladorMantenimiento::crtActualizarPresupuestoReapertura(
				$id_mantenimiento, $costoReapertura, $fechaInicioReapertura, $fechaFinReapertura
			);

			if($resultadoPresupuesto["status"] !== "success"){
				http_response_code(400);
				echo json_encode($resultadoPresupuesto);
				return;
			}
		}

		echo json_encode([
			"status" => "success",
			"message" => "Incidencia actualizada correctamente"
		]);
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
