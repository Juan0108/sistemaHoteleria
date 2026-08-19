<?php

require_once "conexion.php";

class ModeloReservaciones{

	// Clientes del negocio del hotel en sesión cuyo nombre o teléfono coincide con $termino.
	static public function MdlBuscarClientes($id_hotel, $termino){
		$stmt = Conexion::conectar()->prepare("CALL BuscarClientes(:id_hotel, :termino)");
		$stmt->bindParam(":id_hotel", $id_hotel, PDO::PARAM_INT);
		$stmt->bindParam(":termino", $termino);
		$stmt->execute();
		return $stmt->fetchAll();
	}

	// Alta rápida de cliente nuevo, asignado automáticamente al negocio del hotel en sesión.
	static public function MdlInsertarCliente($nombre, $apaterno, $amaterno, $telefono, $id_hotel){
		$stmt = Conexion::conectar()->prepare("CALL InsertarCliente(:nombre, :apaterno, :amaterno, :telefono, :id_hotel)");
		$stmt->bindParam(":nombre", $nombre);
		$stmt->bindParam(":apaterno", $apaterno);
		$stmt->bindParam(":amaterno", $amaterno);
		$stmt->bindParam(":telefono", $telefono);
		$stmt->bindParam(":id_hotel", $id_hotel, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetch();
	}

	// Reservaciones activas de la habitación cuyo rango real (incluyendo HorasExtras) se traslapa
	// con el rango propuesto. Si regresa filas, la habitación NO está disponible en esas fechas.
	static public function MdlVerificarDisponibilidadHabitacion($id_habitacion, $fecha_entrada, $fecha_salida){
		$stmt = Conexion::conectar()->prepare(
			"CALL VerificarDisponibilidadHabitacion(:id_habitacion, :fecha_entrada, :fecha_salida)"
		);
		$stmt->bindParam(":id_habitacion", $id_habitacion, PDO::PARAM_INT);
		$stmt->bindParam(":fecha_entrada", $fecha_entrada);
		$stmt->bindParam(":fecha_salida", $fecha_salida);
		$stmt->execute();
		return $stmt->fetchAll();
	}

	static public function MdlObtenerProximaReservacionHabitacion($id_habitacion, $fecha_salida_actual){
		$stmt = Conexion::conectar()->prepare(
			"CALL ObtenerProximaReservacionHabitacion(:id_habitacion, :fecha_salida_actual)"
		);
		$stmt->bindParam(":id_habitacion", $id_habitacion, PDO::PARAM_INT);
		$stmt->bindParam(":fecha_salida_actual", $fecha_salida_actual);
		$stmt->execute();
		return $stmt->fetch();
	}

	// Todas las reservaciones de una habitación (cualquier estatus, cualquier fecha), más
	// recientes primero. Limitado al hotel de la sesión.
	static public function MdlObtenerReservacionesHabitacion($id_habitacion, $id_hotel){
		$stmt = Conexion::conectar()->prepare("CALL ObtenerReservacionesHabitacion(:id_habitacion, :id_hotel)");
		$stmt->bindParam(":id_habitacion", $id_habitacion, PDO::PARAM_INT);
		$stmt->bindParam(":id_hotel", $id_hotel, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll();
	}

	// Crea la reservación con folio autogenerado (formato AñoMesDiaHoraMinutoSegundo).
	static public function MdlInsertarReservacion($id_habitacion, $id_cliente, $precio, $fecha_entrada, $fecha_salida, $id_estatus){
		$stmt = Conexion::conectar()->prepare(
			"CALL InsertarReservacion(:id_habitacion, :id_cliente, :precio, :fecha_entrada, :fecha_salida, :id_estatus)"
		);
		$stmt->bindParam(":id_habitacion", $id_habitacion, PDO::PARAM_INT);
		$stmt->bindParam(":id_cliente", $id_cliente, PDO::PARAM_INT);
		$stmt->bindParam(":precio", $precio);
		$stmt->bindParam(":fecha_entrada", $fecha_entrada);
		$stmt->bindParam(":fecha_salida", $fecha_salida);
		$stmt->bindParam(":id_estatus", $id_estatus, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetch();
	}

	// Pasa una reservación Reservado/Ocupado al estatus de cancelación que le corresponda
	// (12 CancelacionOcupacion u 13 CancelacionReserva, decidido dentro del SP) y deja el
	// folio/motivo/fecha en Tb_Cancelaciones. No hace nada si ya no está activa o si no
	// pertenece al hotel de la sesión (aislamiento por negocio).
	static public function MdlCancelarReservacion($id_reservacion, $id_hotel, $id_motivo){
		$stmt = Conexion::conectar()->prepare("CALL CancelarReservacion(:id_reservacion, :id_hotel, :id_motivo)");
		$stmt->bindParam(":id_reservacion", $id_reservacion);
		$stmt->bindParam(":id_hotel", $id_hotel, PDO::PARAM_INT);
		$stmt->bindParam(":id_motivo", $id_motivo, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetch();
	}

	// Catálogo de motivos de cancelación (cat_Motivos), para el combo del formulario.
	static public function MdlObtenerMotivosCancelacion(){
		$stmt = Conexion::conectar()->prepare("CALL ObtenerMotivosCancelacion()");
		$stmt->execute();
		return $stmt->fetchAll();
	}

	// Confirma la llegada de una reservación (Reservado -> Ocupado). No hace nada si la
	// reservación ya no está en Reservado o si no pertenece al hotel de la sesión.
	static public function MdlConfirmarCheckIn($id_reservacion, $id_hotel){
		$stmt = Conexion::conectar()->prepare("CALL ConfirmarCheckIn(:id_reservacion, :id_hotel)");
		$stmt->bindParam(":id_reservacion", $id_reservacion);
		$stmt->bindParam(":id_hotel", $id_hotel, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetch();
	}

	// Habitaciones activas del hotel sin ninguna reservación vigente (Ocupado/Reservado)
	// que se traslape con el rango [fecha_entrada, fecha_salida). Requiere el SP
	// ObtenerHabitacionesDisponiblesPorFecha (ver sql/ObtenerHabitacionesDisponiblesPorFecha.sql).
	static public function MdlObtenerHabitacionesDisponibles($id_hotel, $fecha_entrada, $fecha_salida){
		$stmt = Conexion::conectar()->prepare(
			"CALL ObtenerHabitacionesDisponiblesPorFecha(:id_hotel, :fecha_entrada, :fecha_salida)"
		);
		$stmt->bindParam(":id_hotel", $id_hotel, PDO::PARAM_INT);
		$stmt->bindParam(":fecha_entrada", $fecha_entrada);
		$stmt->bindParam(":fecha_salida", $fecha_salida);
		$stmt->execute();
		return $stmt->fetchAll();
	}

	// Conteo, por habitación, de reservaciones Reservadas (Id_Estatus=9) cuya entrada aún no
	// llega. Requiere el SP ObtenerConteoReservasProximasPorHotel (ver
	// sql/ObtenerConteoReservasProximasPorHotel.sql). Regresa filas {Id_Habitacion, Total}.
	static public function MdlObtenerConteoReservasProximas($id_hotel){
		$stmt = Conexion::conectar()->prepare("CALL ObtenerConteoReservasProximasPorHotel(:id_hotel)");
		$stmt->bindParam(":id_hotel", $id_hotel, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll();
	}
}
