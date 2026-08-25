<?php

session_start();

require_once "../controllers/habitaciones.controlador.php";
require_once "../models/habitaciones.modelo.php";
require_once "../models/reservaciones.modelo.php";
require_once "../models/mantenimiento.modelo.php";
require_once "../controllers/servicio.controlador.php";
require_once "../models/servicio.modelo.php";

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
		$tipo = isset($_GET["tipo"]) ? trim($_GET["tipo"]) : "";

		if ($busqueda !== "") {
			$Habitaciones = ControladorHabitaciones::crtBuscarHabitacionesRecepcion($busqueda);
		} else {
			$fecha = isset($_GET["fecha"]) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET["fecha"]) ? $_GET["fecha"] : date("Y-m-d");
			$Habitaciones = ControladorHabitaciones::crtObtenerHabitacionesRecepcion($fecha);
		}

		if ($tipo !== "") {
			$Habitaciones = array_values(array_filter($Habitaciones, function($hab) use ($tipo){
				return $hab["TipoHabitacion"] === $tipo;
			}));
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

			// El SP de ReservasProximas cuenta cualquier Reservada con FechaEntrada futura,
			// incluida la que ya se ve en esta misma tarjeta si aún no llega. "entradaYaLlego"
			// le dice al frontend si esa reservación actual YA está incluida en ese conteo
			// (entrada futura -> sí) o si hay que sumarla aparte (entrada ya pasada -> no).
			$entradaYaLlego = !empty($Habitaciones[$i]["FechaEntrada"])
				&& strtotime($Habitaciones[$i]["FechaEntrada"]) <= time();

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
				"enMantenimiento": '.(!empty($Habitaciones[$i]["EnMantenimiento"]) ? "true" : "false").',
				"mantenimientoDescripciones": '.json_encode($Habitaciones[$i]["MantenimientoDescripciones"] ?? [], JSON_UNESCAPED_UNICODE).',
				"enServicio": '.(!empty($Habitaciones[$i]["EnServicio"]) ? "true" : "false").',
				"servicioInicio": "'.$this->jsonEscape($Habitaciones[$i]["ServicioInicio"] ?? "").'",
				"puedeCheckin": '.($Habitaciones[$i]["PuedeCheckin"] ? "true" : "false").',
				"puedeCheckout": '.($Habitaciones[$i]["PuedeCheckout"] ? "true" : "false").',
				"reservasProximas": '.(int) $Habitaciones[$i]["ReservasProximas"].',
				"entradaYaLlego": '.($entradaYaLlego ? "true" : "false").',
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
