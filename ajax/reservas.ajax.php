<?php

session_start();

require_once "../controllers/habitaciones.controlador.php";
require_once "../models/habitaciones.modelo.php";
require_once "../models/reservaciones.modelo.php";

class ReservasAjax
{

	// Escapa comillas, backslashes y saltos de línea para no romper el JSON armado a mano
	private function jsonEscape($valor){
		return str_replace(
			array('\\', '"', "\r\n", "\n", "\r"),
			array('\\\\', '\"', '\n', '\n', '\n'),
			(string) $valor
		);
	}

	public function mostrarMes(){

		$mesActual = isset($_GET["mes"]) ? (int) $_GET["mes"] : (int) date("n");
		$anioActual = isset($_GET["anio"]) ? (int) $_GET["anio"] : (int) date("Y");

		if ($mesActual < 1 || $mesActual > 12) {
			$mesActual = (int) date("n");
		}

		$totalDias = (int) date("t", mktime(0, 0, 0, $mesActual, 1, $anioActual));

		$mesAnterior = $mesActual - 1;
		$anioMesAnterior = $anioActual;
		if ($mesAnterior < 1) { $mesAnterior = 12; $anioMesAnterior--; }

		$mesSiguiente = $mesActual + 1;
		$anioMesSiguiente = $anioActual;
		if ($mesSiguiente > 12) { $mesSiguiente = 1; $anioMesSiguiente++; }

		$nombresMes = [1 => "ENERO", 2 => "FEBRERO", 3 => "MARZO", 4 => "ABRIL", 5 => "MAYO", 6 => "JUNIO",
					   7 => "JULIO", 8 => "AGOSTO", 9 => "SEPTIEMBRE", 10 => "OCTUBRE", 11 => "NOVIEMBRE", 12 => "DICIEMBRE"];
		$diasSemana = ["Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sáb"];

		$hoy = date("Y-m-d");

		$Habitaciones = ControladorHabitaciones::crtObtenerHabitacionesReserva($anioActual, $mesActual);

		ob_start();
		include "../views/modules/reservas_tabla.php";
		$tabla = ob_get_clean();

		$respuesta = '{
			"tabla": "'.$this->jsonEscape($tabla).'",
			"titulo": "'.$this->jsonEscape($nombresMes[$mesActual] . " " . $anioActual).'",
			"mesActual": '.$mesActual.',
			"anioActual": '.$anioActual.',
			"mesAnterior": '.$mesAnterior.',
			"anioMesAnterior": '.$anioMesAnterior.',
			"mesSiguiente": '.$mesSiguiente.',
			"anioMesSiguiente": '.$anioMesSiguiente.'
		}';

		echo $respuesta;
	}
}

$Mostrar = new ReservasAjax();
$Mostrar -> mostrarMes();
