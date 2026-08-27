<?php

session_start();

require_once "../controllers/servicio.controlador.php";
require_once "../models/servicio.modelo.php";
require_once "../controllers/habitaciones.controlador.php";
require_once "../models/habitaciones.modelo.php";

class ServicioAjax
{

	public function activo(){

		$id_habitacion = isset($_GET["id_habitacion"]) ? $_GET["id_habitacion"] : 0;
		$servicio = ControladorServicio::crtObtenerServicioActivo($id_habitacion);

		echo json_encode(["data" => $servicio], JSON_UNESCAPED_UNICODE);
	}

	public function iniciar(){

		$id_habitacion = isset($_POST["id_habitacion"]) ? $_POST["id_habitacion"] : 0;
		$archivoFoto = $_FILES["fotoInicio"] ?? null;
		$respuesta = ControladorServicio::crtIniciarServicio($id_habitacion, $archivoFoto);

		echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
	}

	public function historial(){
		$historial = ControladorServicio::crtObtenerHistorialServicios();

		echo json_encode(["data" => $historial], JSON_UNESCAPED_UNICODE);
	}

	public function tareas(){

		$id_servicio = isset($_GET["id_servicio"]) ? $_GET["id_servicio"] : 0;
		$tareas = ControladorServicio::crtObtenerServicioTareas($id_servicio);

		echo json_encode(["data" => $tareas], JSON_UNESCAPED_UNICODE);
	}

	public function cambiarEstatusTarea(){

		$id_servicio_tarea = isset($_POST["id_servicio_tarea"]) ? $_POST["id_servicio_tarea"] : 0;
		$realizada = isset($_POST["realizada"]) ? $_POST["realizada"] : 0;
		$respuesta = ControladorServicio::crtCambiarEstatusServicioTarea($id_servicio_tarea, $realizada);

		echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
	}

	public function finalizar(){

		$id_servicio = isset($_POST["id_servicio"]) ? $_POST["id_servicio"] : 0;
		$archivoFoto = $_FILES["evidencia"] ?? null;
		$respuesta = ControladorServicio::crtFinalizarServicio($id_servicio, $archivoFoto);

		echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
	}

}

$Accion = isset($_REQUEST["accion"]) ? $_REQUEST["accion"] : "";
$Ajax = new ServicioAjax();

if ($Accion === "activo") {
	$Ajax->activo();
} elseif ($Accion === "iniciar") {
	$Ajax->iniciar();
} elseif ($Accion === "tareas") {
	$Ajax->tareas();
} elseif ($Accion === "cambiarEstatusTarea") {
	$Ajax->cambiarEstatusTarea();
} elseif ($Accion === "finalizar") {
	$Ajax->finalizar();
} elseif ($Accion === "historial") {
	$Ajax->historial();
}
