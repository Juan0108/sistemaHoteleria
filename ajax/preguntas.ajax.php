<?php

session_start();

require_once "../controllers/preguntas.controlador.php";
require_once "../models/preguntas.modelo.php";

class PreguntasAjax
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

		$Preguntas = ControladorPreguntas::crtObtenerPreguntas();

		$datosJason = '{"data":[';

		for ($i = 0; $i < count($Preguntas); $i++){
			$datosJason .= '{
				"id": '.(int) $Preguntas[$i]["Id_pregunta"].',
				"pregunta": "'.$this->jsonEscape($Preguntas[$i]["Pregunta"]).'",
				"idEstatus": '.(int) $Preguntas[$i]["Id_Estatus"].'
			},';
		}

		if (count($Preguntas) > 0) {
			$datosJason = substr($datosJason, 0, -1);
		}

		$datosJason .= ']}';

		echo $datosJason;
	}

	public function agregar(){

		$pregunta = isset($_POST["pregunta"]) ? $_POST["pregunta"] : "";
		$respuesta = ControladorPreguntas::crtInsertarPregunta($pregunta);

		$ok = $respuesta["ok"] ? "true" : "false";
		$mensaje = isset($respuesta["mensaje"]) ? $this->jsonEscape($respuesta["mensaje"]) : "";

		echo '{"ok": '.$ok.', "mensaje": "'.$mensaje.'"}';
	}

	public function cambiarEstatus(){

		$id_pregunta = isset($_POST["id_pregunta"]) ? $_POST["id_pregunta"] : 0;
		$id_estatus_actual = isset($_POST["id_estatus_actual"]) ? $_POST["id_estatus_actual"] : 0;
		$respuesta = ControladorPreguntas::crtCambiarEstatusPregunta($id_pregunta, $id_estatus_actual);

		$ok = $respuesta["ok"] ? "true" : "false";
		$mensaje = isset($respuesta["mensaje"]) ? $this->jsonEscape($respuesta["mensaje"]) : "";
		$idEstatus = isset($respuesta["idEstatus"]) ? (int) $respuesta["idEstatus"] : 0;

		echo '{"ok": '.$ok.', "mensaje": "'.$mensaje.'", "idEstatus": '.$idEstatus.'}';
	}

	// Para el modal de Check Out: solo el texto de las preguntas activas, sin el Id.
	public function activas(){

		$Preguntas = ControladorPreguntas::crtObtenerPreguntasActivasTexto();

		$datosJason = '{"data":[';

		for ($i = 0; $i < count($Preguntas); $i++){
			$datosJason .= '"'.$this->jsonEscape($Preguntas[$i]).'",';
		}

		if (count($Preguntas) > 0) {
			$datosJason = substr($datosJason, 0, -1);
		}

		$datosJason .= ']}';

		echo $datosJason;
	}

	public function editar(){

		$id_pregunta = isset($_POST["id_pregunta"]) ? $_POST["id_pregunta"] : 0;
		$pregunta = isset($_POST["pregunta"]) ? $_POST["pregunta"] : "";
		$id_estatus = isset($_POST["id_estatus"]) ? $_POST["id_estatus"] : 0;
		$respuesta = ControladorPreguntas::crtEditarPregunta($id_pregunta, $pregunta, $id_estatus);

		$ok = $respuesta["ok"] ? "true" : "false";
		$mensaje = isset($respuesta["mensaje"]) ? $this->jsonEscape($respuesta["mensaje"]) : "";

		echo '{"ok": '.$ok.', "mensaje": "'.$mensaje.'"}';
	}
}

$Accion = isset($_REQUEST["accion"]) ? $_REQUEST["accion"] : "";
$Ajax = new PreguntasAjax();

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
