<?php

class ControladorTareas{

	static public function crtObtenerTareas(){
		$id_hotel = ControladorHabitaciones::crtObtenerIdHotelSesion();

		if($id_hotel === null){
			return [];
		}

		return ModeloTareas::MdlObtenerTareas($id_hotel);
	}

	static public function crtInsertarTarea($tarea){
		$tarea = trim((string) $tarea);

		if($tarea === ""){
			return ["ok" => false, "mensaje" => "Escribe el texto de la tarea."];
		}

		if(mb_strlen($tarea, "UTF-8") > 255){
			return ["ok" => false, "mensaje" => "La tarea no puede tener más de 255 caracteres."];
		}

		$id_hotel = ControladorHabitaciones::crtObtenerIdHotelSesion();

		if($id_hotel === null){
			return ["ok" => false, "mensaje" => "Tu negocio no tiene un hotel registrado, contacta a soporte técnico."];
		}

		$resultado = ModeloTareas::MdlInsertarTarea($tarea, $id_hotel);

		if(!$resultado){
			return ["ok" => false, "mensaje" => "No se pudo guardar la tarea."];
		}

		return ["ok" => true];
	}

	// Alterna entre Activo (1) e In-Activo (2), los mismos IDs que ya usa cat_estatus para
	// habitaciones, preguntas y el resto de los catálogos de este sistema.
	static public function crtCambiarEstatusTarea($id_tarea, $id_estatus_actual){
		$id_tarea = (int) $id_tarea;
		$id_estatus_actual = (int) $id_estatus_actual;

		if($id_tarea <= 0){
			return ["ok" => false, "mensaje" => "No se pudo identificar la tarea."];
		}

		$id_hotel = ControladorHabitaciones::crtObtenerIdHotelSesion();

		if($id_hotel === null){
			return ["ok" => false, "mensaje" => "Tu negocio no tiene un hotel registrado, contacta a soporte técnico."];
		}

		$id_estatus_nuevo = ($id_estatus_actual === 1) ? 2 : 1;

		$resultado = ModeloTareas::MdlCambiarEstatusTarea($id_tarea, $id_estatus_nuevo, $id_hotel);
		$afectados = $resultado ? (int) $resultado["Afectados"] : 0;

		if($afectados === 0){
			return ["ok" => false, "mensaje" => "No se pudo actualizar el estatus de la tarea."];
		}

		return ["ok" => true, "idEstatus" => $id_estatus_nuevo];
	}

	// Solo el texto de las tareas activas del hotel de la sesión, sin exponer el Id. Pensado
	// para consumirse desde el flujo de validación de servicios, igual que las preguntas de
	// checkout.
	static public function crtObtenerTareasActivasTexto(){
		$tareas = self::crtObtenerTareas();

		$textos = [];

		foreach($tareas as $t){
			if((int) $t["Id_Estatus"] === 1){
				$textos[] = $t["Tarea"];
			}
		}

		return $textos;
	}

	static public function crtEditarTarea($id_tarea, $tarea, $id_estatus){
		$id_tarea = (int) $id_tarea;
		$tarea = trim((string) $tarea);
		$id_estatus = (int) $id_estatus;

		if($id_tarea <= 0){
			return ["ok" => false, "mensaje" => "No se pudo identificar la tarea."];
		}

		if($tarea === ""){
			return ["ok" => false, "mensaje" => "Escribe el texto de la tarea."];
		}

		if(mb_strlen($tarea, "UTF-8") > 255){
			return ["ok" => false, "mensaje" => "La tarea no puede tener más de 255 caracteres."];
		}

		if($id_estatus !== 1 && $id_estatus !== 2){
			return ["ok" => false, "mensaje" => "Estatus inválido."];
		}

		$id_hotel = ControladorHabitaciones::crtObtenerIdHotelSesion();

		if($id_hotel === null){
			return ["ok" => false, "mensaje" => "Tu negocio no tiene un hotel registrado, contacta a soporte técnico."];
		}

		$resultado = ModeloTareas::MdlEditarTarea($id_tarea, $tarea, $id_estatus, $id_hotel);
		$afectados = $resultado ? (int) $resultado["Afectados"] : 0;

		if($afectados === 0){
			return ["ok" => false, "mensaje" => "No se pudo guardar la edición."];
		}

		return ["ok" => true];
	}

}
