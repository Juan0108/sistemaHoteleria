<?php

session_start();

require_once "../controllers/tareas.controlador.php";
require_once "../models/tareas.modelo.php";
require_once "../controllers/habitaciones.controlador.php";
require_once "../models/habitaciones.modelo.php";

class TareasAjax
{

	// Escapa comillas, backslashes y saltos de línea para no romper el JSON armado a mano
	private function jsonEscape($valor){
		return str_replace(
			array('\\', '"', "\r\n", "\n", "\r"),
			array('\\\\', '\"', '\n', '\n', '\n'),
			(string) $valor
		);
	}

	public function listar(){

		$Tareas = ControladorTareas::crtObtenerTareas();

		$datosJason = '{"data":[';

		for ($i = 0; $i < count($Tareas); $i++){
			$datosJason .= '{
				"id": '.(int) $Tareas[$i]["Id_Tarea"].',
				"tarea": "'.$this->jsonEscape($Tareas[$i]["Tarea"]).'",
				"idEstatus": '.(int) $Tareas[$i]["Id_Estatus"].'
			},';
		}

		if (count($Tareas) > 0) {
			$datosJason = substr($datosJason, 0, -1);
		}

		$datosJason .= ']}';

		echo $datosJason;
	}

	public function agregar(){

		$tarea = isset($_POST["tarea"]) ? $_POST["tarea"] : "";
		$respuesta = ControladorTareas::crtInsertarTarea($tarea);

		$ok = $respuesta["ok"] ? "true" : "false";
		$mensaje = isset($respuesta["mensaje"]) ? $this->jsonEscape($respuesta["mensaje"]) : "";

		echo '{"ok": '.$ok.', "mensaje": "'.$mensaje.'"}';
	}

	public function cambiarEstatus(){

		$id_tarea = isset($_POST["id_tarea"]) ? $_POST["id_tarea"] : 0;
		$id_estatus_actual = isset($_POST["id_estatus_actual"]) ? $_POST["id_estatus_actual"] : 0;
		$respuesta = ControladorTareas::crtCambiarEstatusTarea($id_tarea, $id_estatus_actual);

		$ok = $respuesta["ok"] ? "true" : "false";
		$mensaje = isset($respuesta["mensaje"]) ? $this->jsonEscape($respuesta["mensaje"]) : "";
		$idEstatus = isset($respuesta["idEstatus"]) ? (int) $respuesta["idEstatus"] : 0;

		echo '{"ok": '.$ok.', "mensaje": "'.$mensaje.'", "idEstatus": '.$idEstatus.'}';
	}

	// Para el flujo de validación de servicios: solo el texto de las tareas activas, sin el Id.
	public function activas(){

		$Tareas = ControladorTareas::crtObtenerTareasActivasTexto();

		$datosJason = '{"data":[';

		for ($i = 0; $i < count($Tareas); $i++){
			$datosJason .= '"'.$this->jsonEscape($Tareas[$i]).'",';
		}

		if (count($Tareas) > 0) {
			$datosJason = substr($datosJason, 0, -1);
		}

		$datosJason .= ']}';

		echo $datosJason;
	}

	public function editar(){

		$id_tarea = isset($_POST["id_tarea"]) ? $_POST["id_tarea"] : 0;
		$tarea = isset($_POST["tarea"]) ? $_POST["tarea"] : "";
		$id_estatus = isset($_POST["id_estatus"]) ? $_POST["id_estatus"] : 0;
		$respuesta = ControladorTareas::crtEditarTarea($id_tarea, $tarea, $id_estatus);

		$ok = $respuesta["ok"] ? "true" : "false";
		$mensaje = isset($respuesta["mensaje"]) ? $this->jsonEscape($respuesta["mensaje"]) : "";

		echo '{"ok": '.$ok.', "mensaje": "'.$mensaje.'"}';
	}
}

$Accion = isset($_REQUEST["accion"]) ? $_REQUEST["accion"] : "";
$Ajax = new TareasAjax();

if ($Accion === "agregar") {
	$Ajax->agregar();
} elseif ($Accion === "cambiarEstatus") {
	$Ajax->cambiarEstatus();
} elseif ($Accion === "editar") {
	$Ajax->editar();
} elseif ($Accion === "activas") {
	$Ajax->activas();
} else {
	$Ajax->listar();
}
