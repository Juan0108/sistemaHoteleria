<?php

session_start();

require_once "../controllers/habitaciones.controlador.php";
require_once "../models/habitaciones.modelo.php";
require_once "../models/reservaciones.modelo.php";

class RecepcionAjax
{

	// Escapa comillas, backslashes y saltos de línea para no romper el JSON armado a mano
	private function jsonEscape($valor){
		return str_replace(
			array('\\', '"', "\r\n", "\n", "\r"),
			array('\\\\', '\"', '\n', '\n', '\n'),
			(string) $valor
		);
	}

	public function mostrarRecepcion(){

		$busqueda = isset($_GET["busqueda"]) ? trim($_GET["busqueda"]) : "";

		if ($busqueda !== "") {
			$Habitaciones = ControladorHabitaciones::crtBuscarHabitacionesRecepcion($busqueda);
		} else {
			$fecha = isset($_GET["fecha"]) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET["fecha"]) ? $_GET["fecha"] : date("Y-m-d");
			$Habitaciones = ControladorHabitaciones::crtObtenerHabitacionesRecepcion($fecha);
		}

		$datosJason = '{"data":[';

		for ($i = 0; $i < count($Habitaciones); $i++){

			$tituloPill = "";
			$entradaCorta = "";
			$salidaCorta = "";
			if ($Habitaciones[$i]["FechaEntrada"] && $Habitaciones[$i]["FechaSalida"]) {
				$tsSalidaReal = strtotime($Habitaciones[$i]["FechaSalida"]) + ((int) $Habitaciones[$i]["HorasExtras"] * 3600);
				$tsEntradaReal = strtotime($Habitaciones[$i]["FechaEntrada"]) - ((int) $Habitaciones[$i]["HoraAnticipada"] * 3600);
				$tituloPill = "Entrada: " . date("d/m/Y g:i a", $tsEntradaReal)
							. " · Salida: " . date("d/m/Y g:i a", $tsSalidaReal);
				$entradaCorta = date("d/m g:i a", $tsEntradaReal);
				$salidaCorta = date("d/m g:i a", $tsSalidaReal);
			}

			$proximaTexto = "";
			$proxima = $Habitaciones[$i]["ProximaReservacion"] ?? null;
			if ($proxima) {
				$proximaTexto = "Disponible hasta el " . date("d/m/Y g:i a", strtotime($proxima["fechaEntrada"]));
			}

			$datosJason .= '{
				"id": "'.$Habitaciones[$i]["Id_Habitacion"].'",
				"nombre": "'.$this->jsonEscape($Habitaciones[$i]["TipoHabitacion"]).'",
				"estadoClase": "'.$Habitaciones[$i]["EstadoClase"].'",
				"estadoTexto": "'.$Habitaciones[$i]["EstadoTexto"].'",
				"estadoIcono": "'.$Habitaciones[$i]["EstadoIcono"].'",
				"tituloPill": "'.$this->jsonEscape($tituloPill).'",
				"entradaCorta": "'.$this->jsonEscape($entradaCorta).'",
				"salidaCorta": "'.$this->jsonEscape($salidaCorta).'",
				"idReservacion": "'.$this->jsonEscape($Habitaciones[$i]["Id_Reservacion"]).'",
				"nombreCliente": "'.$this->jsonEscape($Habitaciones[$i]["NombreCliente"]).'",
				"horasExtras": '.(int) $Habitaciones[$i]["HorasExtras"].',
				"horaAnticipada": '.(int) $Habitaciones[$i]["HoraAnticipada"].',
				"puedeCheckin": '.($Habitaciones[$i]["PuedeCheckin"] ? "true" : "false").',
				"reservasProximas": '.(int) $Habitaciones[$i]["ReservasProximas"].',
				"proximaReservacionTexto": "'.$this->jsonEscape($proximaTexto).'",
				"descripcion": "'.$this->jsonEscape($Habitaciones[$i]["Descripcion"]).'",
				"capacidad": "'.$this->jsonEscape($Habitaciones[$i]["Capacidad"]).'",
				"precio": "'.number_format((float) $Habitaciones[$i]["PrecioNoche"], 2).'",
				"precioNoche": "'.((float) $Habitaciones[$i]["PrecioNoche"]).'",
				"foto": "'.$this->jsonEscape($Habitaciones[$i]["Foto"]).'"
			},';
		}

		if (count($Habitaciones) > 0) {
			$datosJason = substr($datosJason, 0, -1);
		}

		$datosJason .= ']}';

		echo $datosJason;
	}
}

$Mostrar = new RecepcionAjax();
$Mostrar -> mostrarRecepcion();
