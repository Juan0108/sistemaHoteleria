<?php

class ControladorPreguntas{

	static public function crtObtenerPreguntas(){
		return ModeloPreguntas::MdlObtenerPreguntas();
	}

	static public function crtInsertarPregunta($pregunta){
		$pregunta = trim((string) $pregunta);

		if($pregunta === ""){
			return ["ok" => false, "mensaje" => "Escribe el texto de la pregunta."];
		}

		if(mb_strlen($pregunta, "UTF-8") > 255){
			return ["ok" => false, "mensaje" => "La pregunta no puede tener más de 255 caracteres."];
		}

		$resultado = ModeloPreguntas::MdlInsertarPregunta($pregunta);

		if(!$resultado){
			return ["ok" => false, "mensaje" => "No se pudo guardar la pregunta."];
		}

		return ["ok" => true];
	}

	// Alterna entre Activo (1) e In-Activo (2), los mismos IDs que ya usa cat_estatus para
	// habitaciones y el resto de los catálogos de este sistema.
	static public function crtCambiarEstatusPregunta($id_pregunta, $id_estatus_actual){
		$id_pregunta = (int) $id_pregunta;
		$id_estatus_actual = (int) $id_estatus_actual;

		if($id_pregunta <= 0){
			return ["ok" => false, "mensaje" => "No se pudo identificar la pregunta."];
		}

		$id_estatus_nuevo = ($id_estatus_actual === 1) ? 2 : 1;

		$resultado = ModeloPreguntas::MdlCambiarEstatusPregunta($id_pregunta, $id_estatus_nuevo);
		$afectados = $resultado ? (int) $resultado["Afectados"] : 0;

		if($afectados === 0){
			return ["ok" => false, "mensaje" => "No se pudo actualizar el estatus de la pregunta."];
		}

		return ["ok" => true, "idEstatus" => $id_estatus_nuevo];
	}

	// Solo el texto de las preguntas activas, sin exponer el Id: el banco de preguntas se
	// comparte entre todos los hoteles, así que el checkout nunca debe depender del id.
	static public function crtObtenerPreguntasActivasTexto(){
		$preguntas = ModeloPreguntas::MdlObtenerPreguntas();

		$textos = [];

		foreach($preguntas as $p){
			if((int) $p["Id_Estatus"] === 1){
				$textos[] = $p["Pregunta"];
			}
		}

		return $textos;
	}

	static public function crtEditarPregunta($id_pregunta, $pregunta, $id_estatus){
		$id_pregunta = (int) $id_pregunta;
		$pregunta = trim((string) $pregunta);
		$id_estatus = (int) $id_estatus;

		if($id_pregunta <= 0){
			return ["ok" => false, "mensaje" => "No se pudo identificar la pregunta."];
		}

		if($pregunta === ""){
			return ["ok" => false, "mensaje" => "Escribe el texto de la pregunta."];
		}

		if(mb_strlen($pregunta, "UTF-8") > 255){
			return ["ok" => false, "mensaje" => "La pregunta no puede tener más de 255 caracteres."];
		}

		if($id_estatus !== 1 && $id_estatus !== 2){
			return ["ok" => false, "mensaje" => "Estatus inválido."];
		}

		$resultado = ModeloPreguntas::MdlEditarPregunta($id_pregunta, $pregunta, $id_estatus);
		$afectados = $resultado ? (int) $resultado["Afectados"] : 0;

		if($afectados === 0){
			return ["ok" => false, "mensaje" => "No se pudo guardar la edición."];
		}

		return ["ok" => true];
	}

}
