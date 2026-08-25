
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

	// Habitaciones con una incidencia de mantenimiento activa (Pendiente o En proceso),
	// para el badge de mantenimiento en las tarjetas de Recepción.
	static public function MdlObtenerHabitacionesEnMantenimiento($id_hotel){
		$stmt = Conexion::conectar()->prepare("CALL ObtenerHabitacionesEnMantenimiento(:id_hotel)");
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

	// Restaura una incidencia eliminada por error, de vuelta a su último estatus antes de
	// borrarse (Pendiente/En proceso/Resuelto).
	static public function MdlRestaurarMantenimiento($id_mantenimiento, $id_hotel){
		$stmt = Conexion::conectar()->prepare("CALL RestaurarMantenimiento(:id_mantenimiento, :id_hotel)");
		$stmt->bindParam(":id_mantenimiento", $id_mantenimiento, PDO::PARAM_INT);
		$stmt->bindParam(":id_hotel", $id_hotel, PDO::PARAM_INT);
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

	// Historial de TRANSICIONES (un renglón por cada cambio de estatus, sin sobreescribir
	// nada) de todas las incidencias de una habitación. Para la pestaña "Bitácora".
	static public function MdlObtenerHistorialTransicionesHabitacion($id_habitacion, $id_hotel){
		$stmt = Conexion::conectar()->prepare("CALL ObtenerHistorialTransicionesHabitacion(:id_habitacion, :id_hotel)");
		$stmt->bindParam(":id_habitacion", $id_habitacion, PDO::PARAM_INT);
		$stmt->bindParam(":id_hotel", $id_hotel, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll();
	}

	// Info completa capturada al registrar una incidencia (pieza, proveedor, descripción,
	// fechas estimadas, foto, costo), para el pop up de detalle en la pestaña Bitácora.
	static public function MdlObtenerInfoRegistroIncidencia($id_mantenimiento, $id_hotel){
		$stmt = Conexion::conectar()->prepare("CALL ObtenerInfoRegistroIncidencia(:id_mantenimiento, :id_hotel)");
		$stmt->bindParam(":id_mantenimiento", $id_mantenimiento, PDO::PARAM_INT);
		$stmt->bindParam(":id_hotel", $id_hotel, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetch();
	}

	// Historial de INCIDENCIAS (un renglón por cada ticket, sin importar su estatus, incluidas
	// las eliminadas) de una habitación. Para la pestaña "Historial".
	static public function MdlObtenerHistorialIncidenciasHabitacion($id_habitacion, $id_hotel){
		$stmt = Conexion::conectar()->prepare("CALL ObtenerHistorialIncidenciasHabitacion(:id_habitacion, :id_hotel)");
		$stmt->bindParam(":id_habitacion", $id_habitacion, PDO::PARAM_INT);
		$stmt->bindParam(":id_hotel", $id_hotel, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll();
	}

	// Igual que la anterior, pero de TODO el hotel (todas las habitaciones juntas). Para la
	// pestaña "Historial" mientras no se haya elegido una habitación en el filtro.
	static public function MdlObtenerHistorialIncidenciasHotel($id_hotel){
		$stmt = Conexion::conectar()->prepare("CALL ObtenerHistorialIncidenciasHotel(:id_hotel)");
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
