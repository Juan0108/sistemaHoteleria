<?php

class ControladorServicio{

	const ESTATUS_ACTIVO     = 21; // Intendencia (servicio en proceso)
	const ESTATUS_COMPLETADO = 22; // IntendenciaCompletada

	static public function crtObtenerIdHotelSesion(){
		return ControladorHabitaciones::crtObtenerIdHotelSesion();
	}

	// Habitaciones del hotel en sesión, para las filas del calendario.
	static public function crtObtenerHabitaciones(){
		return ControladorHabitaciones::crtObtenerHabitaciones();
	}

	// Estatus del hoy: para el botón "Realizar tarea" solo importa si esta habitación ya
	// tiene un servicio ACTIVO ahora mismo (sin importar el día en que empezó).
	static public function crtObtenerServicioActivo($id_habitacion){
		$id_hotel = self::crtObtenerIdHotelSesion();

		if($id_hotel === null){
			return null;
		}

		$servicio = ModeloServicio::MdlObtenerServicioActivoHabitacion((int) $id_habitacion, $id_hotel, self::ESTATUS_ACTIVO);

		return $servicio ?: null;
	}

	// Habitaciones con un servicio activo ahora mismo, para el badge de Recepción.
	static public function crtObtenerHabitacionesEnServicio($id_hotel){
		$servicios = ModeloServicio::MdlObtenerHabitacionesEnServicio($id_hotel, self::ESTATUS_ACTIVO);

		$mapa = [];
		foreach($servicios as $s){
			$mapa[(int) $s["Id_Habitacion"]] = $s;
		}

		return $mapa;
	}

	// Inicia el servicio: valida que no haya ya uno activo para esa habitación, crea la
	// sesión con la hora de inicio automática, guarda la foto inicial (obligatoria) y le
	// hace una fotografía (snapshot) de las tareas activas del hotel en ese momento para
	// el checklist.
	static public function crtIniciarServicio($id_habitacion, $archivoFoto){
		$id_habitacion = (int) $id_habitacion;

		if($id_habitacion <= 0){
			return ["ok" => false, "mensaje" => "No se pudo identificar la habitación."];
		}

		$id_hotel = self::crtObtenerIdHotelSesion();

		if($id_hotel === null){
			return ["ok" => false, "mensaje" => "Tu negocio no tiene un hotel registrado, contacta a soporte técnico."];
		}

		if(self::crtObtenerServicioActivo($id_habitacion) !== null){
			return ["ok" => false, "mensaje" => "Esta habitación ya tiene un servicio en proceso."];
		}

		$id_usuario = (int) ($_SESSION["IdUsuario"] ?? 0);

		if($id_usuario <= 0){
			return ["ok" => false, "mensaje" => "No se pudo identificar al usuario en sesión."];
		}

		if(!is_array($archivoFoto) || empty($archivoFoto["tmp_name"])){
			return ["ok" => false, "mensaje" => "Debes adjuntar una foto inicial para comenzar la limpieza."];
		}

		if($archivoFoto["size"] > 3 * 1024 * 1024){
			return ["ok" => false, "mensaje" => "La foto no puede pesar más de 3MB."];
		}

		// Ruta absoluta: este método se llama desde ajax/servicio.ajax.php, cuyo cwd es
		// /ajax/, así que una ruta relativa terminaría guardando el archivo en /ajax/....
		$dirPathAbsoluto = dirname(__DIR__) . "/views/img/Servicio/";
		if(!is_dir($dirPathAbsoluto)){
			mkdir($dirPathAbsoluto, 0755, true);
		}

		$nombreImagen = time() . "_" . $archivoFoto["name"];

		if(!move_uploaded_file($archivoFoto["tmp_name"], $dirPathAbsoluto . $nombreImagen)){
			return ["ok" => false, "mensaje" => "No se pudo guardar la foto inicial."];
		}

		$fotoDestino = "views/img/Servicio/" . $nombreImagen;

		$resultado = ModeloServicio::MdlIniciarServicio($id_habitacion, $id_hotel, $id_usuario, self::ESTATUS_ACTIVO, $fotoDestino);

		if(!$resultado || empty($resultado["Id_Servicio"])){
			return ["ok" => false, "mensaje" => "No se pudo iniciar el servicio."];
		}

		return ["ok" => true, "idServicio" => (int) $resultado["Id_Servicio"]];
	}

	// Historial de limpiezas ya finalizadas, para la tabla debajo del botón "Iniciar
	// limpieza": una fila por sesión completada, con su habitación, usuario, fechas,
	// fotos y las tareas que sí se marcaron como realizadas.
	static public function crtObtenerHistorialServicios(){
		$id_hotel = self::crtObtenerIdHotelSesion();

		if($id_hotel === null){
			return [];
		}

		$filas = ModeloServicio::MdlObtenerHistorialServicios($id_hotel);

		$historial = [];
		foreach($filas as $fila){
			$historial[] = [
				"idServicio"       => (int) $fila["Id_Servicio"],
				"idHabitacion"     => (int) $fila["Id_Habitacion"],
				"habitacion"       => $fila["TipoHabitacion"] ?: $fila["NumeroHabitacion"],
				"usuario"          => trim($fila["NombreUsuario"]) !== "" ? $fila["NombreUsuario"] : "Sin asignar",
				"fechaInicio"      => $fila["Fecha_Inicio"] ? date("d/m/Y g:i a", strtotime($fila["Fecha_Inicio"])) : null,
				"fechaInicioRaw"   => $fila["Fecha_Inicio"] ? date("Y-m-d", strtotime($fila["Fecha_Inicio"])) : null,
				"fotoInicio"       => $fila["Foto_Inicio"] ?: null,
				"fechaFin"         => $fila["Fecha_Fin"] ? date("d/m/Y g:i a", strtotime($fila["Fecha_Fin"])) : null,
				"fotoResultado"    => $fila["Foto_Evidencia"] ?: null,
				"tareasRealizadas" => $fila["TareasRealizadas"] ?: null,
			];
		}

		return $historial;
	}

	static public function crtObtenerServicioTareas($id_servicio){
		$id_servicio = (int) $id_servicio;

		if($id_servicio <= 0){
			return [];
		}

		return ModeloServicio::MdlObtenerServicioTareas($id_servicio);
	}

	static public function crtCambiarEstatusServicioTarea($id_servicio_tarea, $realizada){
		$id_servicio_tarea = (int) $id_servicio_tarea;

		if($id_servicio_tarea <= 0){
			return ["ok" => false, "mensaje" => "No se pudo identificar la tarea."];
		}

		$id_hotel = self::crtObtenerIdHotelSesion();

		if($id_hotel === null){
			return ["ok" => false, "mensaje" => "Tu negocio no tiene un hotel registrado, contacta a soporte técnico."];
		}

		$realizada = $realizada ? 1 : 0;

		$resultado = ModeloServicio::MdlCambiarEstatusServicioTarea($id_servicio_tarea, $realizada, $id_hotel);
		$afectados = $resultado ? (int) $resultado["Afectados"] : 0;

		if($afectados === 0){
			return ["ok" => false, "mensaje" => "No se pudo actualizar la tarea."];
		}

		return ["ok" => true];
	}

	// Finaliza el servicio: la evidencia final es obligatoria, sin ella no se puede cerrar.
	static public function crtFinalizarServicio($id_servicio, $archivoFoto){
		$id_servicio = (int) $id_servicio;

		if($id_servicio <= 0){
			return ["ok" => false, "mensaje" => "No se pudo identificar el servicio."];
		}

		$id_hotel = self::crtObtenerIdHotelSesion();

		if($id_hotel === null){
			return ["ok" => false, "mensaje" => "Tu negocio no tiene un hotel registrado, contacta a soporte técnico."];
		}

		if(!is_array($archivoFoto) || empty($archivoFoto["tmp_name"])){
			return ["ok" => false, "mensaje" => "Debes adjuntar una foto de evidencia para finalizar el servicio."];
		}

		if($archivoFoto["size"] > 3 * 1024 * 1024){
			return ["ok" => false, "mensaje" => "La foto no puede pesar más de 3MB."];
		}

		// Ruta absoluta: este método se llama desde ajax/servicio.ajax.php, cuyo cwd es
		// /ajax/, así que una ruta relativa terminaría guardando el archivo en /ajax/....
		$dirPathAbsoluto = dirname(__DIR__) . "/views/img/Servicio/";
		if(!is_dir($dirPathAbsoluto)){
			mkdir($dirPathAbsoluto, 0755, true);
		}

		$nombreImagen = time() . "_" . $archivoFoto["name"];

		if(!move_uploaded_file($archivoFoto["tmp_name"], $dirPathAbsoluto . $nombreImagen)){
			return ["ok" => false, "mensaje" => "No se pudo guardar la foto de evidencia."];
		}

		$fotoDestino = "views/img/Servicio/" . $nombreImagen;

		$resultado = ModeloServicio::MdlFinalizarServicio($id_servicio, $id_hotel, $fotoDestino, self::ESTATUS_COMPLETADO);
		$afectados = $resultado ? (int) $resultado["Afectados"] : 0;

		if($afectados === 0){
			return ["ok" => false, "mensaje" => "No se pudo finalizar el servicio."];
		}

		return ["ok" => true];
	}

	// Corte diario (limpiezas que iniciaron/terminaron hoy) para el reporte de WhatsApp.
	// Solo lo puede pedir el Administrador — se revalida aquí también, no solo se oculta el
	// botón.
	static public function crtObtenerCorteDiarioLimpieza(){
		if(($_SESSION["Perfil"] ?? "") !== "Administrador"){
			return null;
		}

		$id_hotel = self::crtObtenerIdHotelSesion();

		if($id_hotel === null){
			return null;
		}

		return ModeloServicio::MdlObtenerCorteDiarioLimpieza($id_hotel);
	}

}
