<?php

require_once "conexion.php";

class ModeloTareas{

	// Tareas del hotel de la sesión, sin importar estatus (activas e inhabilitadas se
	// muestran juntas en la tabla, diferenciadas por el badge).
	static public function MdlObtenerTareas($id_hotel){
		$stmt = Conexion::conectar()->prepare("CALL ObtenerTareas(:id_hotel)");
		$stmt->bindParam(":id_hotel", $id_hotel, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll();
	}

	static public function MdlInsertarTarea($tarea, $id_hotel){
		$stmt = Conexion::conectar()->prepare("CALL InsertarTarea(:tarea, :id_hotel)");
		$stmt->bindParam(":tarea", $tarea);
		$stmt->bindParam(":id_hotel", $id_hotel, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetch();
	}

	static public function MdlCambiarEstatusTarea($id_tarea, $id_estatus, $id_hotel){
		$stmt = Conexion::conectar()->prepare("CALL CambiarEstatusTarea(:id_tarea, :id_estatus, :id_hotel)");
		$stmt->bindParam(":id_tarea", $id_tarea, PDO::PARAM_INT);
		$stmt->bindParam(":id_estatus", $id_estatus, PDO::PARAM_INT);
		$stmt->bindParam(":id_hotel", $id_hotel, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetch();
	}

	// Edita el texto y/o el estatus de una tarea ya existente.
	static public function MdlEditarTarea($id_tarea, $tarea, $id_estatus, $id_hotel){
		$stmt = Conexion::conectar()->prepare("CALL EditarTarea(:id_tarea, :tarea, :id_estatus, :id_hotel)");
		$stmt->bindParam(":id_tarea", $id_tarea, PDO::PARAM_INT);
		$stmt->bindParam(":tarea", $tarea);
		$stmt->bindParam(":id_estatus", $id_estatus, PDO::PARAM_INT);
		$stmt->bindParam(":id_hotel", $id_hotel, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetch();
	}

}
