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

	// Pasa una reservación Reservado/Ocupado a estatus "Movida" (19), sin borrarla, para
	// que ese rango quede libre de inmediato (el calendario de Reservas no reconoce este
	// estatus, así que no se pinta: la habitación se ve disponible/normal en esas fechas,
	// sin dejar ninguna huella). No hace nada si ya no está activa o si no pertenece al
	// hotel de la sesión. Regresa Id_Cliente y Precio de la reservación original para
	// poder crear la reservación con las fechas nuevas.
	static public function MdlMarcarReservacionMovida($id_reservacion, $id_hotel){
		$stmt = Conexion::conectar()->prepare("CALL MarcarReservacionMovida(:id_reservacion, :id_hotel)");
		$stmt->bindParam(":id_reservacion", $id_reservacion);
		$stmt->bindParam(":id_hotel", $id_hotel, PDO::PARAM_INT);
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

	// Completa la estadía (Ocupado -> Completada, 20), liberando la habitación. No hace nada
	// si la reservación ya no está en Ocupado o si no pertenece al hotel de la sesión.
	static public function MdlCompletarCheckout($id_reservacion, $id_hotel){
		$stmt = Conexion::conectar()->prepare("CALL CompletarCheckout(:id_reservacion, :id_hotel)");
		$stmt->bindParam(":id_reservacion", $id_reservacion);
		$stmt->bindParam(":id_hotel", $id_hotel, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetch();
	}

	// Consumo (ventas ligadas vía Tb_Consumo) de una estadía, desglosado por producto.
	// Precio pactado de la habitación (PrecioReservacion) y conteo de horas extra/anticipada,
	// para el resumen de cobro del modal de Check Out. Limitado al hotel de la sesión.
	static public function MdlObtenerReservacionParaCheckout($id_reservacion, $id_hotel){
		$stmt = Conexion::conectar()->prepare("CALL ObtenerReservacionParaCheckout(:id_reservacion, :id_hotel)");
		$stmt->bindParam(":id_reservacion", $id_reservacion);
		$stmt->bindParam(":id_hotel", $id_hotel, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetch();
	}

	static public function MdlObtenerConsumoReservacion($id_reservacion){
		$stmt = Conexion::conectar()->prepare("CALL ObtenerConsumoReservacion(:id_reservacion)");
		$stmt->bindParam(":id_reservacion", $id_reservacion);
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

	// Tolerancia (en minutos) para redondear Horas Anticipadas al hacer check-in.
	// Se guarda como texto (ej. "30m") en cat_estatus, Id_Estatus=14.
	static public function MdlObtenerToleranciaAnticipada(){
		$stmt = Conexion::conectar()->prepare("CALL ObtenerToleranciaAnticipada()");
		$stmt->execute();
		$fila = $stmt->fetch();
		$stmt->closeCursor();

		if(!$fila || !isset($fila["Nombre"])){
			return 30;
		}

		$minutos = (int) preg_replace('/\D/', '', $fila["Nombre"]);
		return $minutos > 0 ? $minutos : 30;
	}

	// Datos de una reservación (por folio) necesarios para validar el check-in
	// anticipado, escopeados al hotel de la sesión.
	static public function MdlObtenerReservacionParaCheckin($id_reservacion, $id_hotel){
		$stmt = Conexion::conectar()->prepare("CALL ObtenerReservacionParaCheckin(:id_reservacion, :id_hotel)");
		$stmt->bindParam(":id_reservacion", $id_reservacion);
		$stmt->bindParam(":id_hotel", $id_hotel, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetch();
	}

	// Suma horas anticipadas a la reservación (alimenta el badge "Anticipada Nh" en Recepción).
	static public function MdlActualizarHoraAnticipada($id_reservacion, $horas){
		$stmt = Conexion::conectar()->prepare("CALL ActualizarHoraAnticipada(:id_reservacion, :horas)");
		$stmt->bindParam(":id_reservacion", $id_reservacion);
		$stmt->bindParam(":horas", $horas, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetch();
	}
}
