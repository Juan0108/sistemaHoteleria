<?php

session_start();

require_once "../controllers/habitaciones.controlador.php";
require_once "../models/habitaciones.modelo.php";

class AjaxHabitaciones
{
/**
 *Editar Habitación
 */
	public $idHabitacion;

	public function ajaxEditarHabitacion(){

		$id_hotel = ControladorHabitaciones::crtObtenerIdHotelSesion();
		$valor = $this->idHabitacion;
		$respuesta = ModeloHabitaciones::MdlObtenerHabitacion($valor, $id_hotel);
		echo json_encode($respuesta);
	}

}

if(isset($_POST["idHabitacion"])){

	$Editar = new AjaxHabitaciones();
	$Editar -> idHabitacion = $_POST["idHabitacion"];
	$Editar -> ajaxEditarHabitacion();

}
