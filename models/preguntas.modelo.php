<?php

require_once "conexion.php";

class ModeloPreguntas{

	// Todas las preguntas de checkout, sin importar estatus (activas e inhabilitadas se
	// muestran juntas en la tabla, diferenciadas por el badge).
	static public function MdlObtenerPreguntas(){
		$stmt = Conexion::conectar()->prepare("CALL ObtenerPreguntas()");
		$stmt->execute();
		return $stmt->fetchAll();
	}

	static public function MdlInsertarPregunta($pregunta){
		$stmt = Conexion::conectar()->prepare("CALL InsertarPregunta(:pregunta)");
		$stmt->bindParam(":pregunta", $pregunta);
		$stmt->execute();
		return $stmt->fetch();
	}

	static public function MdlCambiarEstatusPregunta($id_pregunta, $id_estatus){
		$stmt = Conexion::conectar()->prepare("CALL CambiarEstatusPregunta(:id_pregunta, :id_estatus)");
		$stmt->bindParam(":id_pregunta", $id_pregunta, PDO::PARAM_INT);
		$stmt->bindParam(":id_estatus", $id_estatus, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetch();
	}

	// Edita el texto y/o el estatus de una pregunta ya existente.
	static public function MdlEditarPregunta($id_pregunta, $pregunta, $id_estatus){
		$stmt = Conexion::conectar()->prepare("CALL EditarPregunta(:id_pregunta, :pregunta, :id_estatus)");
		$stmt->bindParam(":id_pregunta", $id_pregunta, PDO::PARAM_INT);
		$stmt->bindParam(":pregunta", $pregunta);
		$stmt->bindParam(":id_estatus", $id_estatus, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetch();
	}

}
