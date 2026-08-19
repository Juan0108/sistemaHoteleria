<?php

session_start();

require_once "../controllers/habitaciones.controlador.php";
require_once "../models/habitaciones.modelo.php";

class TablaHabitaciones
{

	// Escapa comillas, backslashes y saltos de línea para no romper el JSON armado a mano
	private function jsonEscape($valor){
		return str_replace(
			array('\\', '"', "\r\n", "\n", "\r"),
			array('\\\\', '\"', '\n', '\n', '\n'),
			(string) $valor
		);
	}

	public function mostrarTablaHabitaciones(){

	   $Habitaciones = ControladorHabitaciones::crtObtenerHabitaciones();

		$datosJason = '{
			  "data": [';

			  	for ($i = 0; $i < count($Habitaciones); $i++){

			  		$idEstatus = isset($Habitaciones[$i]["Id_Estatus"]) ? (int) $Habitaciones[$i]["Id_Estatus"] : 0;
			  		$estatusHabitacion = ($idEstatus === 2) ? "<a class='hab-badge hab-badge-inhabilitada'>Inhabilitada</a>" : "<a class='hab-badge hab-badge-activa'>Activa</a>";

			  		$Boton = "<div class='btn-group'><button class='btn btnEditarHabitacion' idHabitacion='".$Habitaciones[$i]["Id_Habitacion"]."' data-toggle='modal' data-target='#modalEditarHabitacion'><i class='fa fa-pencil'></i></button> <button class='btn btnSuspenderHabitacion' idHabitacion='".$Habitaciones[$i]["Id_Habitacion"]."'><i class='fa fa-times'></i></button></div>";

			  		$datosJason .= '[
					      "'.$Habitaciones[$i]["Id_Habitacion"].'",
					      "'.$this->jsonEscape($Habitaciones[$i]["NumeroHabitacion"]).'",
					      "'.$this->jsonEscape($Habitaciones[$i]["TipoHabitacion"]).'",
					      "'.$Habitaciones[$i]["Capacidad"].'",
					      "$'.number_format($Habitaciones[$i]["PrecioNoche"], 2).'",
					      "'.$estatusHabitacion.'",
					      "'.$Boton.'"
					    ],';
			  	}

			  	if (count($Habitaciones) > 0) {
			  		$datosJason = substr($datosJason, 0, -1);
			  	}

			  	$datosJason .= ']

			  }';

			  echo $datosJason;

	}
}


$Mostrar = new TablaHabitaciones();
$Mostrar -> mostrarTablaHabitaciones();
