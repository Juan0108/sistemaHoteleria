<?php

require_once "../models/negocios.modelo.php";

class AjaxValidarHorasExtra
{
	public $idHabitacion;
	public $fechaSalidaActual;
	public $cantidadHoras;

	public function ajaxValidarHorasExtra(){

		$siguiente = ModeloHoteles::MdlObtenerSiguienteReservacion($this->idHabitacion, $this->fechaSalidaActual);

		if(!$siguiente){

			echo json_encode(["permitido" => true, "maxHoras" => null, "siguienteEntrada" => null]);
			return;

		}

		$maxHoras = isset($siguiente["HorasDisponibles"]) ? (int)$siguiente["HorasDisponibles"] : 0;

		echo json_encode([
			"permitido" => ((int)$this->cantidadHoras <= $maxHoras),
			"maxHoras" => $maxHoras,
			"siguienteEntrada" => $siguiente["FechaEntrada"]
		]);

	}

}

if(isset($_POST["idHabitacion"])){

	$Validar = new AjaxValidarHorasExtra();
	$Validar -> idHabitacion = $_POST["idHabitacion"];
	$Validar -> fechaSalidaActual = $_POST["fechaSalidaActual"];
	$Validar -> cantidadHoras = $_POST["cantidadHoras"];
	$Validar -> ajaxValidarHorasExtra();

}
