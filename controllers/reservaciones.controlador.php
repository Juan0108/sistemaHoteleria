<?php

class ControladorReservaciones{

	static public function crtBuscarClientes($termino){
		$id_hotel = ControladorHabitaciones::crtObtenerIdHotelSesion();

		if($id_hotel === null || trim((string) $termino) === ""){
			return [];
		}

		return ModeloReservaciones::MdlBuscarClientes($id_hotel, $termino);
	}

	// Todas las reservaciones (cualquier estatus/fecha) de una habitación, para el historial
	// que se abre desde la tarjeta de Recepción.
	static public function crtObtenerReservacionesHabitacion($id_habitacion){
		$id_hotel = ControladorHabitaciones::crtObtenerIdHotelSesion();
		$id_habitacion = (int) $id_habitacion;

		if($id_hotel === null || $id_habitacion <= 0){
			return [];
		}

		return ModeloReservaciones::MdlObtenerReservacionesHabitacion($id_habitacion, $id_hotel);
	}

	// Crea la reservación. $datos trae id_habitacion, fecha_entrada, fecha_salida, precio,
	// y o bien id_cliente (cliente existente) o nombre/apaterno/amaterno/telefono (cliente nuevo).
	static public function crtCrearReservacion($datos){
		$id_hotel = ControladorHabitaciones::crtObtenerIdHotelSesion();

		if($id_hotel === null){
			return ["ok" => false, "mensaje" => "Tu negocio no tiene un hotel registrado, contacta a soporte técnico."];
		}

		$id_habitacion = isset($datos["id_habitacion"]) ? (int) $datos["id_habitacion"] : 0;
		$fecha_entrada = isset($datos["fecha_entrada"]) ? trim($datos["fecha_entrada"]) : "";
		$fecha_salida = isset($datos["fecha_salida"]) ? trim($datos["fecha_salida"]) : "";
		$precio = isset($datos["precio"]) ? (float) $datos["precio"] : 0;

		if($id_habitacion <= 0 || $fecha_entrada === "" || $fecha_salida === "" || $precio <= 0){
			return ["ok" => false, "mensaje" => "Faltan datos de la reservación."];
		}

		if(strtotime($fecha_salida) === false || strtotime($fecha_entrada) === false || strtotime($fecha_salida) <= strtotime($fecha_entrada)){
			return ["ok" => false, "mensaje" => "La fecha de salida debe ser posterior a la de entrada."];
		}

		// Se compara por día (no por minuto exacto) para no rechazar un check-in de "ahora mismo"
		// solo porque pasaron unos segundos entre elegir la hora y guardar el formulario.
		if(strtotime(date("Y-m-d", strtotime($fecha_entrada))) < strtotime(date("Y-m-d"))){
			return ["ok" => false, "mensaje" => "La fecha de entrada no puede ser en el pasado."];
		}

		// Normaliza el datetime-local del formulario ("2026-08-15T14:30") al formato de MySQL.
		$fecha_entrada = date("Y-m-d H:i:s", strtotime($fecha_entrada));
		$fecha_salida = date("Y-m-d H:i:s", strtotime($fecha_salida));

		// No permitir traslape con otra reservación activa de la misma habitación. El traslape
		// se calcula sobre la salida real (FechaSalida + HorasExtras), para que un cliente con
		// horas extras ese mismo día no deje la habitación "libre" en el sistema antes de tiempo.
		$conflictos = ModeloReservaciones::MdlVerificarDisponibilidadHabitacion($id_habitacion, $fecha_entrada, $fecha_salida);

		if(count($conflictos) > 0){
			// Si hay varios traslapes, se informa el que libera la habitación más tarde
			// (el que realmente determina hasta cuándo hay que esperar).
			$salidaRealMaxima = null;

			foreach($conflictos as $conflicto){
				$salidaReal = strtotime($conflicto["FechaSalida"]) + ((int) $conflicto["HorasExtras"] * 3600);

				if($salidaRealMaxima === null || $salidaReal > $salidaRealMaxima){
					$salidaRealMaxima = $salidaReal;
				}
			}

			return [
				"ok" => false,
				"mensaje" => "Esta habitación ya tiene una reservación activa hasta el " . date("d/m/Y g:i a", $salidaRealMaxima) . ", elige otras fechas.",
			];
		}

		$id_cliente = isset($datos["id_cliente"]) ? (int) $datos["id_cliente"] : 0;

		if($id_cliente <= 0){
			$nombre = isset($datos["nombre"]) ? trim($datos["nombre"]) : "";
			$apaterno = isset($datos["apaterno"]) ? trim($datos["apaterno"]) : "";
			$amaterno = isset($datos["amaterno"]) ? trim($datos["amaterno"]) : "";
			$telefono = isset($datos["telefono"]) ? trim($datos["telefono"]) : "";

			if($nombre === "" || $apaterno === "" || $telefono === ""){
				return ["ok" => false, "mensaje" => "Selecciona un cliente existente o captura nombre, apellido paterno y teléfono del cliente nuevo."];
			}

			$clienteNuevo = ModeloReservaciones::MdlInsertarCliente($nombre, $apaterno, $amaterno, $telefono, $id_hotel);

			if(!$clienteNuevo){
				return ["ok" => false, "mensaje" => "No se pudo registrar al cliente."];
			}

			$id_cliente = (int) $clienteNuevo["id_Cliente"];
		}

		// Toda reserva nueva entra como Reservado; solo el check-in la pasa a Ocupado.
		$id_estatus = 9;

		$reservacion = ModeloReservaciones::MdlInsertarReservacion(
			$id_habitacion, $id_cliente, $precio, $fecha_entrada, $fecha_salida, $id_estatus
		);

		if(!$reservacion){
			return ["ok" => false, "mensaje" => "No se pudo guardar la reservación."];
		}

		return ["ok" => true, "folio" => $reservacion["Id_Reservacion"]];
	}

	// Cancela una reservación Ocupada o Reservada. El SP decide el estatus de cancelación
	// correcto (12 CancelacionOcupacion / 13 CancelacionReserva) según lo que se cancela, y
	// registra folio+motivo+fecha en Tb_Cancelaciones. La habitación queda libre de inmediato
	// porque el resto de consultas (Recepción, Reserva, disponibilidad) solo consideran
	// Id_Estatus 8/9.
	static public function crtCancelarReservacion($id_reservacion, $id_motivo){
		$id_hotel = ControladorHabitaciones::crtObtenerIdHotelSesion();

		if($id_hotel === null){
			return ["ok" => false, "mensaje" => "Tu negocio no tiene un hotel registrado, contacta a soporte técnico."];
		}

		$id_reservacion = trim((string) $id_reservacion);
		$id_motivo = (int) $id_motivo;

		if($id_reservacion === ""){
			return ["ok" => false, "mensaje" => "Falta la reservación a cancelar."];
		}

		if($id_motivo <= 0){
			return ["ok" => false, "mensaje" => "Selecciona el motivo de la cancelación."];
		}

		$resultado = ModeloReservaciones::MdlCancelarReservacion($id_reservacion, $id_hotel, $id_motivo);
		$afectados = $resultado ? (int) $resultado["Afectados"] : 0;

		if($afectados === 0){
			return ["ok" => false, "mensaje" => "Esta reservación ya no está activa o no pertenece a tu hotel."];
		}

		return ["ok" => true, "mensaje" => "Reservación cancelada correctamente.", "folio" => (int) $resultado["Folio"]];
	}

	// Catálogo de motivos de cancelación, para el combo del formulario.
	static public function crtObtenerMotivosCancelacion(){
		return ModeloReservaciones::MdlObtenerMotivosCancelacion();
	}

	// Confirma el check-in de una reservación Reservada (pasa a Id_Estatus=8, Ocupado).
	static public function crtConfirmarCheckIn($id_reservacion){
		$id_hotel = ControladorHabitaciones::crtObtenerIdHotelSesion();

		if($id_hotel === null){
			return ["ok" => false, "mensaje" => "Tu negocio no tiene un hotel registrado, contacta a soporte técnico."];
		}

		$id_reservacion = trim((string) $id_reservacion);

		if($id_reservacion === ""){
			return ["ok" => false, "mensaje" => "Falta la reservación."];
		}

		$resultado = ModeloReservaciones::MdlConfirmarCheckIn($id_reservacion, $id_hotel);
		$afectados = $resultado ? (int) $resultado["Afectados"] : 0;

		if($afectados === 0){
			return ["ok" => false, "mensaje" => "Esta reservación ya no está en estado Reservada."];
		}

		return ["ok" => true, "mensaje" => "Check-in confirmado."];
	}

	// Habitaciones del hotel en sesión disponibles entre $fecha_inicio y $fecha_fin (formato
	// Y-m-d). Bloquea el día completo de inicio y fin, igual que las barras del calendario.
	static public function crtObtenerHabitacionesDisponibles($fecha_inicio, $fecha_fin){
		$id_hotel = ControladorHabitaciones::crtObtenerIdHotelSesion();

		if($id_hotel === null){
			return ["ok" => false, "mensaje" => "Tu negocio no tiene un hotel registrado, contacta a soporte técnico."];
		}

		$fecha_inicio = trim((string) $fecha_inicio);
		$fecha_fin = trim((string) $fecha_fin);

		if($fecha_inicio === "" || $fecha_fin === ""){
			return ["ok" => false, "mensaje" => "Selecciona fecha de inicio y de fin."];
		}

		$tsInicio = strtotime($fecha_inicio);
		$tsFin = strtotime($fecha_fin);

		if($tsInicio === false || $tsFin === false || $tsFin < $tsInicio){
			return ["ok" => false, "mensaje" => "El rango de fechas no es válido."];
		}

		$fecha_entrada = date("Y-m-d", $tsInicio) . " 00:00:00";
		$fecha_salida = date("Y-m-d", $tsFin) . " 23:59:59";

		$habitaciones = ModeloReservaciones::MdlObtenerHabitacionesDisponibles($id_hotel, $fecha_entrada, $fecha_salida);

		return ["ok" => true, "habitaciones" => $habitaciones];
	}
}
