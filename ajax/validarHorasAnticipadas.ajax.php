<?php

require_once "../models/negocios.modelo.php";

class AjaxValidarHorasAnticipadas
{
	public $idHabitacion;
	public $idReservacion;
	public $fechaEntradaActual;
	public $cantidadHoras;

	public function ajaxValidarHorasAnticipadas(){

		$anterior = ModeloHoteles::MdlObtenerReservacionAnterior($this->idHabitacion, $this->fechaEntradaActual, $this->idReservacion);

		if(!$anterior){

			echo json_encode(["permitido" => true, "maxHoras" => null, "salidaAnterior" => null]);
			return;

		}

		$maxHoras = isset($anterior["HorasDisponibles"]) ? (int)$anterior["HorasDisponibles"] : 0;

		echo json_encode([
			"permitido" => ((int)$this->cantidadHoras <= $maxHoras),
			"maxHoras" => $maxHoras,
			"salidaAnterior" => $anterior["FechaSalidaEfectiva"]
		]);

	}

}

if(isset($_POST["idHabitacion"])){

	$Validar = new AjaxValidarHorasAnticipadas();
	$Validar -> idHabitacion = $_POST["idHabitacion"];
	$Validar -> idReservacion = $_POST["idReservacion"];
	$Validar -> fechaEntradaActual = $_POST["fechaEntradaActual"];
	$Validar -> cantidadHoras = $_POST["cantidadHoras"];
	$Validar -> ajaxValidarHorasAnticipadas();

}
