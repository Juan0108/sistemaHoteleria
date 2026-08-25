<?php

require_once "conexion.php";

class ModeloServicio{

	static public function MdlObtenerServicioActivoHabitacion($id_habitacion, $id_hotel, $id_estatus_activo){
		$stmt = Conexion::conectar()->prepare("CALL ObtenerServicioActivoHabitacion(:id_habitacion, :id_hotel, :id_estatus_activo)");
		$stmt->bindParam(":id_habitacion", $id_habitacion, PDO::PARAM_INT);
		$stmt->bindParam(":id_hotel", $id_hotel, PDO::PARAM_INT);
		$stmt->bindParam(":id_estatus_activo", $id_estatus_activo, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetch();
	}

	static public function MdlIniciarServicio($id_habitacion, $id_hotel, $id_usuario, $id_estatus_activo){
		$stmt = Conexion::conectar()->prepare("CALL IniciarServicio(:id_habitacion, :id_hotel, :id_usuario, :id_estatus_activo)");
		$stmt->bindParam(":id_habitacion", $id_habitacion, PDO::PARAM_INT);
		$stmt->bindParam(":id_hotel", $id_hotel, PDO::PARAM_INT);
		$stmt->bindParam(":id_usuario", $id_usuario, PDO::PARAM_INT);
		$stmt->bindParam(":id_estatus_activo", $id_estatus_activo, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetch();
	}

	static public function MdlObtenerServicioTareas($id_servicio){
		$stmt = Conexion::conectar()->prepare("CALL ObtenerServicioTareas(:id_servicio)");
		$stmt->bindParam(":id_servicio", $id_servicio, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll();
	}

	static public function MdlCambiarEstatusServicioTarea($id_servicio_tarea, $realizada, $id_hotel){
		$stmt = Conexion::conectar()->prepare("CALL CambiarEstatusServicioTarea(:id_servicio_tarea, :realizada, :id_hotel)");
		$stmt->bindParam(":id_servicio_tarea", $id_servicio_tarea, PDO::PARAM_INT);
		$stmt->bindParam(":realizada", $realizada, PDO::PARAM_INT);
		$stmt->bindParam(":id_hotel", $id_hotel, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetch();
	}

	static public function MdlFinalizarServicio($id_servicio, $id_hotel, $foto_evidencia, $id_estatus_completado){
		$stmt = Conexion::conectar()->prepare("CALL FinalizarServicio(:id_servicio, :id_hotel, :foto_evidencia, :id_estatus_completado)");
		$stmt->bindParam(":id_servicio", $id_servicio, PDO::PARAM_INT);
		$stmt->bindParam(":id_hotel", $id_hotel, PDO::PARAM_INT);
		$stmt->bindParam(":foto_evidencia", $foto_evidencia);
		$stmt->bindParam(":id_estatus_completado", $id_estatus_completado, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetch();
	}

	static public function MdlObtenerHabitacionesEnServicio($id_hotel, $id_estatus_activo){
		$stmt = Conexion::conectar()->prepare("CALL ObtenerHabitacionesEnServicio(:id_hotel, :id_estatus_activo)");
		$stmt->bindParam(":id_hotel", $id_hotel, PDO::PARAM_INT);
		$stmt->bindParam(":id_estatus_activo", $id_estatus_activo, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll();
	}

	static public function MdlObtenerServiciosPorMes($id_hotel, $anio, $mes){
		$stmt = Conexion::conectar()->prepare("CALL ObtenerServiciosPorMes(:id_hotel, :anio, :mes)");
		$stmt->bindParam(":id_hotel", $id_hotel, PDO::PARAM_INT);
		$stmt->bindParam(":anio", $anio, PDO::PARAM_INT);
		$stmt->bindParam(":mes", $mes, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll();
	}

}
