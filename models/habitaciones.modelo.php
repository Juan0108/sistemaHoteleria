<?php

require_once "conexion.php";

class ModeloHabitaciones{

	// Obtener el hotel asociado al negocio de la sesión actual
	static public function MdlObtenerHotelPorNegocio($id_negocio){
		$stmt = Conexion::conectar()->prepare("CALL ObtenerHotelPorNegocio(:id_negocio)");
		$stmt->bindParam(":id_negocio", $id_negocio, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetch();
	}

	// Obtener una habitación específica (limitado al hotel de la sesión)
	static public function MdlObtenerHabitacion($valor, $id_hotel){
		$stmt = Conexion::conectar()->prepare("CALL ObtenerHabitacion(:valor, :id_hotel)");
		$stmt->bindParam(":valor", $valor);
		$stmt->bindParam(":id_hotel", $id_hotel, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetch();
	}

	// Obtener todas las habitaciones del hotel de la sesión
	static public function MdlObtenerHabitaciones($id_hotel){
		$stmt = Conexion::conectar()->prepare("CALL ObtenerHabitaciones(:id_hotel)");
		$stmt->bindParam(":id_hotel", $id_hotel, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll();
	}

	// Catálogo de tipos de habitación del hotel (para saber cuáles series/colores
	// pintar en la gráfica de consumo, aunque alguno no haya tenido ventas en el año).
	static public function MdlObtenerTiposHabitacion($id_hotel){
		$stmt = Conexion::conectar()->prepare("CALL ObtenerTiposHabitacion(:id_hotel)");
		$stmt->bindParam(":id_hotel", $id_hotel, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll();
	}

	// Ventas (reservaciones no canceladas) agrupadas por mes y tipo de habitación de un
	// año dado, para la gráfica de consumo de habitaciones del Tablero de Control.
	static public function MdlObtenerVentasPorTipoHabitacionMensual($id_hotel, $anio){
		$stmt = Conexion::conectar()->prepare("CALL ObtenerVentasPorTipoHabitacionMensual(:id_hotel, :anio)");
		$stmt->bindParam(":id_hotel", $id_hotel, PDO::PARAM_INT);
		$stmt->bindParam(":anio", $anio, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll();
	}

	// Ventas (reservaciones no canceladas) por día y tipo de habitación, dentro de un rango
	// de fechas arbitrario, para la gráfica de consumo de habitaciones del Reporte de Ventas.
	static public function MdlObtenerVentasPorTipoHabitacionRango($id_hotel, $fecha_inicio, $fecha_fin){
		$stmt = Conexion::conectar()->prepare("CALL ObtenerVentasPorTipoHabitacionRango(:id_hotel, :fecha_inicio, :fecha_fin)");
		$stmt->bindParam(":id_hotel", $id_hotel, PDO::PARAM_INT);
		$stmt->bindParam(":fecha_inicio", $fecha_inicio);
		$stmt->bindParam(":fecha_fin", $fecha_fin);
		$stmt->execute();
		return $stmt->fetchAll();
	}

	// Obtener catálogo de hoteles (para el select del formulario)
	static public function MdlObtenerHoteles(){
		$stmt = Conexion::conectar()->prepare("CALL ObtenerHoteles()");
		$stmt->execute();
		return $stmt;
	}

	// Insertar habitación
	static public function MdlInsertarHabitacion($Habitacion){
		$stmt = Conexion::conectar()->prepare("CALL InsertarHabitacion(
			'$Habitacion->id_hotel',
			'$Habitacion->numeroHabitacion',
			'$Habitacion->descripcion',
			'$Habitacion->tipoHabitacion',
			'$Habitacion->capacidad',
			'$Habitacion->precioNoche',
			'$Habitacion->foto',
			'$Habitacion->id_estatus')");

		$stmt->execute();
		return $stmt->fetch();
	}

	// Actualizar habitación
	static public function MdlActualizarHabitacion($Habitacion){
		$stmt = Conexion::conectar()->prepare("CALL ActualizarHabitacion(
			'$Habitacion->id_habitacion',
			'$Habitacion->id_hotel',
			'$Habitacion->numeroHabitacion',
			'$Habitacion->descripcion',
			'$Habitacion->tipoHabitacion',
			'$Habitacion->capacidad',
			'$Habitacion->precioNoche',
			'$Habitacion->foto',
			'$Habitacion->id_estatus')");

		return $stmt->execute();
	}

	// Habitaciones con reservación vigente en una fecha (limitado al hotel de la sesión).
	static public function MdlObtenerReservacionesPorFecha($id_hotel, $fecha){
		$stmt = Conexion::conectar()->prepare("CALL ObtenerReservacionesPorFecha(:id_hotel, :fecha)");
		$stmt->bindParam(":id_hotel", $id_hotel, PDO::PARAM_INT);
		$stmt->bindParam(":fecha", $fecha);
		$stmt->execute();
		return $stmt->fetchAll();
	}

	// Reservaciones vigentes (Ocupado/Reservado) cuyo folio o cliente coincide con el término
	// buscado, sin importar la fecha (limitado al hotel de la sesión).
	static public function MdlBuscarReservacionPorFolio($id_hotel, $termino){
		$stmt = Conexion::conectar()->prepare("CALL BuscarReservacionPorFolio(:id_hotel, :termino)");
		$stmt->bindParam(":id_hotel", $id_hotel, PDO::PARAM_INT);
		$stmt->bindParam(":termino", $termino);
		$stmt->execute();
		return $stmt->fetchAll();
	}

	// Reservaciones vigentes (Ocupado/Reservado) que tocan algún día del mes indicado
	
	static public function MdlObtenerReservacionesPorMes($id_hotel, $anio, $mes){
		$stmt = Conexion::conectar()->prepare("CALL ObtenerReservacionesPorMes(:id_hotel, :anio, :mes)");
		$stmt->bindParam(":id_hotel", $id_hotel, PDO::PARAM_INT);
		$stmt->bindParam(":anio", $anio, PDO::PARAM_INT);
		$stmt->bindParam(":mes", $mes, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll();
	}

	// Suspender / dar de baja una habitación (limitado al hotel de la sesión)
	static public function MdlSuspenderHabitacion($id_habitacion, $id_hotel){
		$stmt = Conexion::conectar()->prepare("UPDATE cat_habitaciones SET Id_Estatus = 2 WHERE Id_Habitacion = :id_habitacion AND Id_Hotel = :id_hotel");
		$stmt->bindParam(":id_habitacion", $id_habitacion, PDO::PARAM_INT);
		$stmt->bindParam(":id_hotel", $id_hotel, PDO::PARAM_INT);

		if ($stmt->execute()) {
			return $stmt->rowCount() > 0;
		} else {
			return false;
		}
	}
}