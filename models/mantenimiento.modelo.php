
<?php

require_once "conexion.php";

class ModeloMantenimiento{

	// Catálogo de tipos de mantenimiento (combo del formulario)
	static public function MdlObtenerTiposMantenimiento(){
		$stmt = Conexion::conectar()->prepare("CALL ObtenerTiposMantenimiento()");
		$stmt->execute();
		return $stmt->fetchAll();
	}

	// Catálogo de motivos de eliminación (mismo patrón que cat_Motivos en Cancelaciones)
	static public function MdlObtenerMotivosMantenimiento(){
		$stmt = Conexion::conectar()->prepare("CALL ObtenerMotivosMantenimiento()");
		$stmt->execute();
		return $stmt->fetchAll();
	}

	// Incidencias del hotel en sesión, ya ordenadas por columna de estatus y Orden
	static public function MdlObtenerMantenimientos($id_hotel){
		$stmt = Conexion::conectar()->prepare("CALL ObtenerMantenimientos(:id_hotel)");
		$stmt->bindParam(":id_hotel", $id_hotel, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll();
	}

	// Insertar incidencia (siempre entra en Pendiente)
	static public function MdlInsertarMantenimiento($id_habitacion, $id_tipo, $pieza, $proveedor, $descripcion, $foto, $fecha_inicio, $fecha_fin, $costo, $id_usuario){
		$stmt = Conexion::conectar()->prepare("CALL InsertarMantenimiento(:id_habitacion, :id_tipo, :pieza, :proveedor, :descripcion, :foto, :fecha_inicio, :fecha_fin, :costo, :id_usuario)");
		$stmt->bindParam(":id_habitacion", $id_habitacion, PDO::PARAM_INT);
		$stmt->bindParam(":id_tipo", $id_tipo, PDO::PARAM_INT);
		$stmt->bindParam(":pieza", $pieza);
		$stmt->bindParam(":proveedor", $proveedor);
		$stmt->bindParam(":descripcion", $descripcion);
		$stmt->bindParam(":foto", $foto);
		$stmt->bindParam(":fecha_inicio", $fecha_inicio);
		$stmt->bindParam(":fecha_fin", $fecha_fin);
		$stmt->bindParam(":costo", $costo);
		$stmt->bindParam(":id_usuario", $id_usuario, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetch();
	}

	// Mover la tarjeta a otra columna (Pendiente / En Proceso / Resuelto)
	static public function MdlActualizarEstatusMantenimiento($id_mantenimiento, $id_hotel, $id_estatus){
		$stmt = Conexion::conectar()->prepare("CALL ActualizarEstatusMantenimiento(:id_mantenimiento, :id_hotel, :id_estatus)");
		$stmt->bindParam(":id_mantenimiento", $id_mantenimiento, PDO::PARAM_INT);
		$stmt->bindParam(":id_hotel", $id_hotel, PDO::PARAM_INT);
		$stmt->bindParam(":id_estatus", $id_estatus, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetch();
	}

	// Reordenar dentro de la misma columna ("arriba" / "abajo")
	static public function MdlMoverOrdenMantenimiento($id_mantenimiento, $id_hotel, $direccion){
		$stmt = Conexion::conectar()->prepare("CALL MoverOrdenMantenimiento(:id_mantenimiento, :id_hotel, :direccion)");
		$stmt->bindParam(":id_mantenimiento", $id_mantenimiento, PDO::PARAM_INT);
		$stmt->bindParam(":id_hotel", $id_hotel, PDO::PARAM_INT);
		$stmt->bindParam(":direccion", $direccion);
		$stmt->execute();
		return $stmt->fetch();
	}

	// Detalle de una incidencia para el popup "Detalle" de la tarjeta: quién la
	// registró (se usa como "persona encargada"), fecha de registro, el rango
	// estimado capturado al crearla y la fecha real de resuelto. Consulta
	// directa (no SP), igual que MdlSuspenderHabitacion, limitada al hotel de
	// la sesión.
	static public function MdlObtenerDetalleMantenimiento($id_mantenimiento, $id_hotel){
		$stmt = Conexion::conectar()->prepare(
			"SELECT m.Fecha_Registro, m.Fecha_Resuelto, m.Fecha_InicioEstimado, m.Fecha_FinEstimado, m.Veces_Reabierta, m.Foto, m.Foto_Resuelto, m.NotaReapertura, m.Id_Pieza AS Pieza, m.Proveedor,
			        u.Nombre, u.Apaterno, u.Amaterno
			 FROM Tb_Mantenimiento m
			 INNER JOIN cat_habitaciones h ON h.Id_Habitacion = m.Id_Habitacion
			 LEFT JOIN usuarios u ON u.Id_Usuario = m.Id_Usuario
			 WHERE m.Id_Mantenimiento = :id_mantenimiento
			   AND h.Id_Hotel = :id_hotel
			 LIMIT 1"
		);
		$stmt->bindParam(":id_mantenimiento", $id_mantenimiento, PDO::PARAM_INT);
		$stmt->bindParam(":id_hotel", $id_hotel, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetch();
	}

	// Eliminar incidencia (borrado suave: pasa a Id_Estatus = 18 "EliminadoMantenimiento"
	// con su motivo, no se hace DELETE — mismo criterio que CancelarReservacion)
	static public function MdlEliminarMantenimiento($id_mantenimiento, $id_hotel, $id_motivo){
		$stmt = Conexion::conectar()->prepare("CALL EliminarMantenimiento(:id_mantenimiento, :id_hotel, :id_motivo)");
		$stmt->bindParam(":id_mantenimiento", $id_mantenimiento, PDO::PARAM_INT);
		$stmt->bindParam(":id_hotel", $id_hotel, PDO::PARAM_INT);
		$stmt->bindParam(":id_motivo", $id_motivo, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetch();
	}

	// Resumen de abonos de una incidencia: saldo inicial (costo estimado),
	// saldo restante y número de abonos hechos
	static public function MdlObtenerResumenAbonos($id_mantenimiento, $id_hotel){
		$stmt = Conexion::conectar()->prepare("CALL ObtenerResumenAbonos(:id_mantenimiento, :id_hotel)");
		$stmt->bindParam(":id_mantenimiento", $id_mantenimiento, PDO::PARAM_INT);
		$stmt->bindParam(":id_hotel", $id_hotel, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetch();
	}

	// Detalle de cada abono de una incidencia (monto, fecha, foto del ticket, quién lo registró)
	static public function MdlObtenerListaAbonos($id_mantenimiento, $id_hotel){
		$stmt = Conexion::conectar()->prepare("CALL ObtenerListaAbonos(:id_mantenimiento, :id_hotel)");
		$stmt->bindParam(":id_mantenimiento", $id_mantenimiento, PDO::PARAM_INT);
		$stmt->bindParam(":id_hotel", $id_hotel, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll();
	}

	// Insertar un abono a una incidencia
	static public function MdlInsertarAbono($id_mantenimiento, $id_hotel, $monto, $foto, $id_usuario){
		$stmt = Conexion::conectar()->prepare("CALL InsertarAbono(:id_mantenimiento, :id_hotel, :monto, :foto, :id_usuario)");
		$stmt->bindParam(":id_mantenimiento", $id_mantenimiento, PDO::PARAM_INT);
		$stmt->bindParam(":id_hotel", $id_hotel, PDO::PARAM_INT);
		$stmt->bindParam(":monto", $monto);
		$stmt->bindParam(":foto", $foto);
		$stmt->bindParam(":id_usuario", $id_usuario, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetch();
	}

	// Bitácora de incidencias de la habitación (todas, no solo la actual)
	static public function MdlObtenerBitacoraIncidencias($id_mantenimiento, $id_hotel){
		$stmt = Conexion::conectar()->prepare("CALL ObtenerBitacoraIncidencias(:id_mantenimiento, :id_hotel)");
		$stmt->bindParam(":id_mantenimiento", $id_mantenimiento, PDO::PARAM_INT);
		$stmt->bindParam(":id_hotel", $id_hotel, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll();
	}

	// Bitácora de abonos de la habitación (todos, de cualquiera de sus incidencias)
	static public function MdlObtenerBitacoraAbonos($id_mantenimiento, $id_hotel){
		$stmt = Conexion::conectar()->prepare("CALL ObtenerBitacoraAbonos(:id_mantenimiento, :id_hotel)");
		$stmt->bindParam(":id_mantenimiento", $id_mantenimiento, PDO::PARAM_INT);
		$stmt->bindParam(":id_hotel", $id_hotel, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll();
	}

	// Bitácora de incidencias eliminadas del hotel (historial global, sin importar la incidencia)
	static public function MdlObtenerBitacoraEliminadas($id_hotel){
		$stmt = Conexion::conectar()->prepare("CALL ObtenerBitacoraEliminadas(:id_hotel)");
		$stmt->bindParam(":id_hotel", $id_hotel, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll();
	}

	// Guarda el motivo de "por qué se volvió a reabrir" (solo la nota más reciente, se sobreescribe)
	static public function MdlActualizarNotaReapertura($id_mantenimiento, $id_hotel, $nota){
		$stmt = Conexion::conectar()->prepare("CALL ActualizarNotaReapertura(:id_mantenimiento, :id_hotel, :nota)");
		$stmt->bindParam(":id_mantenimiento", $id_mantenimiento, PDO::PARAM_INT);
		$stmt->bindParam(":id_hotel", $id_hotel, PDO::PARAM_INT);
		$stmt->bindParam(":nota", $nota);
		$stmt->execute();
		return $stmt->fetch();
	}

	// Guarda la foto de cómo quedó la incidencia ya reparada, capturada al presionar "Marcar resuelto"
	static public function MdlActualizarFotoResuelta($id_mantenimiento, $id_hotel, $foto){
		$stmt = Conexion::conectar()->prepare("CALL ActualizarFotoResuelta(:id_mantenimiento, :id_hotel, :foto)");
		$stmt->bindParam(":id_mantenimiento", $id_mantenimiento, PDO::PARAM_INT);
		$stmt->bindParam(":id_hotel", $id_hotel, PDO::PARAM_INT);
		$stmt->bindParam(":foto", $foto);
		$stmt->execute();
		return $stmt->fetch();
	}
}
