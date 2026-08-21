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

		// La referencia para "ya terminada" es la fecha que se está viendo en Recepción
		// (el picker #recepcionFecha), no la fecha real del servidor: si el usuario se
		// paró en el 22 de agosto, una estadía que terminó el 21 ya debe verse como pasada
		// aunque "hoy" (la fecha real) sea otra.
		$fechaReferencia = isset($_GET["fecha"]) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET["fecha"]) ? $_GET["fecha"] : date("Y-m-d");

		// IDs de cat_estatus usados en reservaciones: 8=Ocupado, 9=Reservado,
		// 12=CancelacionOcupacion, 13=CancelacionReserva, 19=Movida.
		$claseParaEstatus = [8 => "ocupada", 9 => "reservada", 12 => "cancelada", 13 => "cancelada"];
		$textoParaEstatus = [12 => "Cancelada (estadía)", 13 => "Cancelada (reserva)"];

		$datosJason = '{"data":[';
		$totalAgregadas = 0;

		for ($i = 0; $i < count($Reservaciones); $i++){

			$idEstatus = (int) $Reservaciones[$i]["Id_Estatus"];

			// Este modal es de "próximas reservas" vigentes: las canceladas (12/13) y las
			// movidas (19, ya reemplazadas por una reservación nueva) no cuentan aquí. Ese
			// rastro histórico solo se ve en el calendario de Reservas.
			if ($idEstatus === 12 || $idEstatus === 13 || $idEstatus === 19){
				continue;
			}

			// Tampoco cuentan las estadías ya terminadas (FechaSalida antes de la fecha de
			// referencia). El estatus se queda en 8/9 para siempre (no hay un "completada"),
			// así que sin este filtro el historial completo de la habitación aparecía como
			// si fueran reservas vigentes.
			$fechaSalida = $Reservaciones[$i]["FechaSalida"] ?? "";
			if ($fechaSalida !== "" && strtotime(date("Y-m-d", strtotime($fechaSalida))) < strtotime($fechaReferencia)){
				continue;
			}

			$totalAgregadas++;
			$clase = $claseParaEstatus[$idEstatus] ?? "otro";
			$texto = $textoParaEstatus[$idEstatus] ?? $Reservaciones[$i]["EstatusNombre"];
			$nombreCliente = trim($Reservaciones[$i]["Nombre"] . " " . $Reservaciones[$i]["APaterno"] . " " . $Reservaciones[$i]["AMaterno"]);

			$datosJason .= '{
				"folio": "'.$this->jsonEscape($Reservaciones[$i]["Id_Reservacion"]).'",
				"entrada": "'.$this->jsonEscape($Reservaciones[$i]["FechaEntrada"] ? date("d/m/Y g:i a", strtotime($Reservaciones[$i]["FechaEntrada"])) : "").'",
				"salida": "'.$this->jsonEscape($Reservaciones[$i]["FechaSalida"] ? date("d/m/Y g:i a", strtotime($Reservaciones[$i]["FechaSalida"])) : "").'",
				"entradaRaw": "'.$this->jsonEscape($Reservaciones[$i]["FechaEntrada"] ?? "").'",
				"salidaRaw": "'.$this->jsonEscape($Reservaciones[$i]["FechaSalida"] ?? "").'",
				"estadoClase": "'.$clase.'",
				"estadoTexto": "'.$this->jsonEscape($texto).'",
				"cliente": "'.$this->jsonEscape($nombreCliente).'"
			},';
		}

		if ($totalAgregadas > 0) {
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

	public function mover(){

		$id_reservacion = isset($_POST["id_reservacion"]) ? $_POST["id_reservacion"] : "";
		$respuesta = ControladorReservaciones::crtMoverReservacion($id_reservacion, $_POST);

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
} elseif ($Accion === "motivosCancelacion") {
	$Ajax->motivosCancelacion();
} elseif ($Accion === "mover") {
	$Ajax->mover();
} else {
	$Ajax->crearReservacion();
}
