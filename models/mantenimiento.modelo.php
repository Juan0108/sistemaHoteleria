<?php

require_once "conexion.php";

class ModeloMantenimiento{

	// Catálogo de tipos de mantenimiento (combo del formulario)
	static public function MdlObtenerTiposMantenimiento(){
		$stmt = Conexion::conectar()->prepare("CALL ObtenerTiposMantenimiento()");
		$stmt->execute();
		return $stmt->fetchAll();
	}

	// Catálogo de piezas/partes afectadas (combo del formulario)
	static public function MdlObtenerPiezasMantenimiento(){
		$stmt = Conexion::conectar()->prepare("CALL ObtenerPiezasMantenimiento()");
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
	static public function MdlInsertarMantenimiento($id_habitacion, $id_tipo, $id_pieza, $descripcion, $foto, $fecha_inicio, $fecha_fin, $costo, $id_usuario){
		$stmt = Conexion::conectar()->prepare("CALL InsertarMantenimiento(:id_habitacion, :id_tipo, :id_pieza, :descripcion, :foto, :fecha_inicio, :fecha_fin, :costo, :id_usuario)");
		$stmt->bindParam(":id_habitacion", $id_habitacion, PDO::PARAM_INT);
		$stmt->bindParam(":id_tipo", $id_tipo, PDO::PARAM_INT);
		$stmt->bindParam(":id_pieza", $id_pieza, PDO::PARAM_INT);
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

	// Una sola incidencia con los mismos campos que ObtenerMantenimientos, para
	// re-renderizar su tarjeta tras un cambio de estatus. Consulta directa
	// (no SP), limitada al hotel de la sesión.
	static public function MdlObtenerMantenimientoPorId($id_mantenimiento, $id_hotel){
		$stmt = Conexion::conectar()->prepare(
			"SELECT
				m.Id_Mantenimiento, m.Id_Estatus, m.Orden, m.Descripcion,
				m.Fecha_InicioEstimado, m.Fecha_FinEstimado, m.CostoReparacion, m.Veces_Reabierta,
				m.Fecha_Registro, m.Fecha_Resuelto,
				h.NumeroHabitacion, h.TipoHabitacion,
				t.Nombre AS TipoMantenimientoNombre,
				p.Nombre AS PiezaNombre,
				e.Nombre AS EstatusNombre
			 FROM Tb_Mantenimiento m
			 INNER JOIN cat_habitaciones h ON h.Id_Habitacion = m.Id_Habitacion
			 INNER JOIN cat_TiposMantenimiento t ON t.Id_TipoMantenimiento = m.Id_TipoMantenimiento
			 INNER JOIN cat_PiezasMantenimiento p ON p.Id_Pieza = m.Id_Pieza
			 INNER JOIN cat_estatus e ON e.Id_Estatus = m.Id_Estatus
			 WHERE m.Id_Mantenimiento = :id_mantenimiento AND h.Id_Hotel = :id_hotel
			 LIMIT 1"
		);
		$stmt->bindParam(":id_mantenimiento", $id_mantenimiento, PDO::PARAM_INT);
		$stmt->bindParam(":id_hotel", $id_hotel, PDO::PARAM_INT);
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
			"SELECT m.Fecha_Registro, m.Fecha_Resuelto, m.Fecha_InicioEstimado, m.Fecha_FinEstimado, m.Veces_Reabierta, m.Foto,
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
}
