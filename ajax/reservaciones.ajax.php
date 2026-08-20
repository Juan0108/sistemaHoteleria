<?php

session_start();

require_once "../controllers/habitaciones.controlador.php";
require_once "../controllers/reservaciones.controlador.php";
require_once "../models/habitaciones.modelo.php";
require_once "../models/reservaciones.modelo.php";
require_once "../models/hoteles.modelo.php";
require_once "../models/inventarios.modelo.php";
require_once "../models/ventas.modelo.php";

class ReservacionesAjax
{

	// Escapa comillas, backslashes y saltos de línea para no romper el JSON armado a mano
	private function jsonEscape($valor){
		return str_replace(
			array('\\', '"', "\r\n", "\n", "\r"),
			array('\\\\', '\"', '\n', '\n', '\n'),
			(string) $valor
		);
	}

	public function buscarClientes(){

		$termino = isset($_GET["termino"]) ? trim($_GET["termino"]) : "";
		$Clientes = ControladorReservaciones::crtBuscarClientes($termino);

		$datosJason = '{"data":[';

		for ($i = 0; $i < count($Clientes); $i++){
			$datosJason .= '{
				"id": "'.$Clientes[$i]["id_Cliente"].'",
				"nombre": "'.$this->jsonEscape($Clientes[$i]["Nombre"]).'",
				"apaterno": "'.$this->jsonEscape($Clientes[$i]["APaterno"]).'",
				"amaterno": "'.$this->jsonEscape($Clientes[$i]["AMaterno"]).'",
				"telefono": "'.$this->jsonEscape($Clientes[$i]["Telefono"]).'"
			},';
		}

		if (count($Clientes) > 0) {
			$datosJason = substr($datosJason, 0, -1);
		}

		$datosJason .= ']}';

		echo $datosJason;
	}

	public function historial(){

		$id_habitacion = isset($_GET["id_habitacion"]) ? (int) $_GET["id_habitacion"] : 0;
		$Reservaciones = ControladorReservaciones::crtObtenerReservacionesHabitacion($id_habitacion);

		// IDs de cat_estatus usados en reservaciones: 8=Ocupado, 9=Reservado,
		// 12=CancelacionOcupacion, 13=CancelacionReserva.
		$claseParaEstatus = [8 => "ocupada", 9 => "reservada", 12 => "cancelada", 13 => "cancelada"];
		$textoParaEstatus = [12 => "Cancelada (estadía)", 13 => "Cancelada (reserva)"];

		$datosJason = '{"data":[';

		for ($i = 0; $i < count($Reservaciones); $i++){

			$idEstatus = (int) $Reservaciones[$i]["Id_Estatus"];
			$clase = $claseParaEstatus[$idEstatus] ?? "otro";
			$texto = $textoParaEstatus[$idEstatus] ?? $Reservaciones[$i]["EstatusNombre"];
			$nombreCliente = trim($Reservaciones[$i]["Nombre"] . " " . $Reservaciones[$i]["APaterno"] . " " . $Reservaciones[$i]["AMaterno"]);

			$datosJason .= '{
				"folio": "'.$this->jsonEscape($Reservaciones[$i]["Id_Reservacion"]).'",
				"entrada": "'.$this->jsonEscape($Reservaciones[$i]["FechaEntrada"] ? date("d/m/Y g:i a", strtotime($Reservaciones[$i]["FechaEntrada"])) : "").'",
				"salida": "'.$this->jsonEscape($Reservaciones[$i]["FechaSalida"] ? date("d/m/Y g:i a", strtotime($Reservaciones[$i]["FechaSalida"])) : "").'",
				"estadoClase": "'.$clase.'",
				"estadoTexto": "'.$this->jsonEscape($texto).'",
				"cliente": "'.$this->jsonEscape($nombreCliente).'"
			},';
		}

		if (count($Reservaciones) > 0) {
			$datosJason = substr($datosJason, 0, -1);
		}

		$datosJason .= ']}';

		echo $datosJason;
	}

	public function crearReservacion(){

		$respuesta = ControladorReservaciones::crtCrearReservacion($_POST);

		$ok = $respuesta["ok"] ? "true" : "false";
		$mensaje = isset($respuesta["mensaje"]) ? $this->jsonEscape($respuesta["mensaje"]) : "";
		$folio = isset($respuesta["folio"]) ? $this->jsonEscape($respuesta["folio"]) : "";

		echo '{"ok": '.$ok.', "mensaje": "'.$mensaje.'", "folio": "'.$folio.'"}';
	}

	public function cancelar(){

		$id_reservacion = isset($_POST["id_reservacion"]) ? $_POST["id_reservacion"] : "";
		$id_motivo = isset($_POST["id_motivo"]) ? $_POST["id_motivo"] : 0;
		$respuesta = ControladorReservaciones::crtCancelarReservacion($id_reservacion, $id_motivo);

		$ok = $respuesta["ok"] ? "true" : "false";
		$mensaje = isset($respuesta["mensaje"]) ? $this->jsonEscape($respuesta["mensaje"]) : "";
		$folio = isset($respuesta["folio"]) ? (int) $respuesta["folio"] : 0;

		echo '{"ok": '.$ok.', "mensaje": "'.$mensaje.'", "folio": '.$folio.'}';
	}

	public function motivosCancelacion(){

		$Motivos = ControladorReservaciones::crtObtenerMotivosCancelacion();

		$datosJason = '{"data":[';

		for ($i = 0; $i < count($Motivos); $i++){
			$datosJason .= '{
				"id": '.(int) $Motivos[$i]["Id_Motivo"].',
				"nombre": "'.$this->jsonEscape($Motivos[$i]["Nombre"]).'"
			},';
		}

		if (count($Motivos) > 0) {
			$datosJason = substr($datosJason, 0, -1);
		}

		$datosJason .= ']}';

		echo $datosJason;
	}

	public function checkin(){

		$id_reservacion = isset($_POST["id_reservacion"]) ? $_POST["id_reservacion"] : "";
		$respuesta = ControladorReservaciones::crtConfirmarCheckIn($id_reservacion);

		$ok = $respuesta["ok"] ? "true" : "false";
		$mensaje = isset($respuesta["mensaje"]) ? $this->jsonEscape($respuesta["mensaje"]) : "";
		$horasAnticipada = isset($respuesta["horasAnticipada"]) ? (int) $respuesta["horasAnticipada"] : 0;
		$montoAnticipada = isset($respuesta["montoAnticipada"]) ? (float) $respuesta["montoAnticipada"] : 0;

		echo '{"ok": '.$ok.', "mensaje": "'.$mensaje.'", "horasAnticipada": '.$horasAnticipada.', "montoAnticipada": '.$montoAnticipada.'}';
	}

	// Vista previa (sin escribir nada) de si el check-in de esta reservación va a requerir
	// cobro de Horas Anticipadas, para decidir qué popup mostrar antes de confirmar.
	public function validarLlegadaAnticipada(){

		$id_reservacion = isset($_GET["id_reservacion"]) ? $_GET["id_reservacion"] : "";
		$respuesta = ControladorReservaciones::crtValidarLlegadaAnticipada($id_reservacion);

		echo json_encode($respuesta);
	}

	public function disponibilidad(){

		$fechaInicio = isset($_GET["fecha_inicio"]) ? $_GET["fecha_inicio"] : "";
		$fechaFin = isset($_GET["fecha_fin"]) ? $_GET["fecha_fin"] : "";
		$respuesta = ControladorReservaciones::crtObtenerHabitacionesDisponibles($fechaInicio, $fechaFin);

		$ok = $respuesta["ok"] ? "true" : "false";
		$mensaje = isset($respuesta["mensaje"]) ? $this->jsonEscape($respuesta["mensaje"]) : "";
		$habitaciones = $respuesta["habitaciones"] ?? [];

		$datosJason = '{"ok": '.$ok.', "mensaje": "'.$mensaje.'", "habitaciones": [';

		for ($i = 0; $i < count($habitaciones); $i++){
			$datosJason .= '{
				"id": '.(int) $habitaciones[$i]["Id_Habitacion"].',
				"numero": "'.$this->jsonEscape($habitaciones[$i]["NumeroHabitacion"]).'",
				"tipo": "'.$this->jsonEscape($habitaciones[$i]["TipoHabitacion"]).'"
			},';
		}

		if (count($habitaciones) > 0) {
			$datosJason = substr($datosJason, 0, -1);
		}

		$datosJason .= ']}';

		echo $datosJason;
	}
}

$Accion = isset($_REQUEST["accion"]) ? $_REQUEST["accion"] : "";
$Ajax = new ReservacionesAjax();

if ($Accion === "buscarClientes") {
	$Ajax->buscarClientes();
} elseif ($Accion === "historial") {
	$Ajax->historial();
} elseif ($Accion === "cancelar") {
	$Ajax->cancelar();
} elseif ($Accion === "checkin") {
	$Ajax->checkin();
} elseif ($Accion === "validarLlegadaAnticipada") {
	$Ajax->validarLlegadaAnticipada();
} elseif ($Accion === "disponibilidad") {
	$Ajax->disponibilidad();
} elseif ($Accion === "motivosCancelacion") {
	$Ajax->motivosCancelacion();
} else {
	$Ajax->crearReservacion();
}
