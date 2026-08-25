<?php

require_once "conexion.php";

class ModeloPreguntas{

	// Preguntas de checkout del hotel de la sesión, sin importar estatus (activas e
	// inhabilitadas se muestran juntas en la tabla, diferenciadas por el badge).
	static public function MdlObtenerPreguntas($id_hotel){
		$stmt = Conexion::conectar()->prepare("CALL ObtenerPreguntas(:id_hotel)");
		$stmt->bindParam(":id_hotel", $id_hotel, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll();
	}

	static public function MdlInsertarPregunta($pregunta, $id_hotel){
		$stmt = Conexion::conectar()->prepare("CALL InsertarPregunta(:pregunta, :id_hotel)");
		$stmt->bindParam(":pregunta", $pregunta);
		$stmt->bindParam(":id_hotel", $id_hotel, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetch();
	}

	static public function MdlCambiarEstatusPregunta($id_pregunta, $id_estatus, $id_hotel){
		$stmt = Conexion::conectar()->prepare("CALL CambiarEstatusPregunta(:id_pregunta, :id_estatus, :id_hotel)");
		$stmt->bindParam(":id_pregunta", $id_pregunta, PDO::PARAM_INT);
		$stmt->bindParam(":id_estatus", $id_estatus, PDO::PARAM_INT);
		$stmt->bindParam(":id_hotel", $id_hotel, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetch();
	}

	// Edita el texto y/o el estatus de una pregunta ya existente.
	static public function MdlEditarPregunta($id_pregunta, $pregunta, $id_estatus, $id_hotel){
		$stmt = Conexion::conectar()->prepare("CALL EditarPregunta(:id_pregunta, :pregunta, :id_estatus, :id_hotel)");
		$stmt->bindParam(":id_pregunta", $id_pregunta, PDO::PARAM_INT);
		$stmt->bindParam(":pregunta", $pregunta);
		$stmt->bindParam(":id_estatus", $id_estatus, PDO::PARAM_INT);
		$stmt->bindParam(":id_hotel", $id_hotel, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetch();
	}

}
