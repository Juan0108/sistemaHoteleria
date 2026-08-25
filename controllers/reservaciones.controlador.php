<?php

class ControladorReservaciones{

	static public function crtBuscarClientes($termino){
		$id_hotel = ControladorHabitaciones::crtObtenerIdHotelSesion();

		if($id_hotel === null || trim((string) $termino) === ""){
			return [];
		}

		return ModeloReservaciones::MdlBuscarClientes($id_hotel, $termino);
	}

	// Todas las reservaciones (cualquier estatus/fecha) de una habitación, para el historial
	// que se abre desde la tarjeta de Recepción.
	static public function crtObtenerReservacionesHabitacion($id_habitacion){
		$id_hotel = ControladorHabitaciones::crtObtenerIdHotelSesion();
		$id_habitacion = (int) $id_habitacion;

		if($id_hotel === null || $id_habitacion <= 0){
			return [];
		}

		return ModeloReservaciones::MdlObtenerReservacionesHabitacion($id_habitacion, $id_hotel);
	}

	// Crea la reservación. $datos trae id_habitacion, fecha_entrada, fecha_salida, precio,
	// y o bien id_cliente (cliente existente) o nombre/apaterno/amaterno/telefono (cliente nuevo).
	static public function crtCrearReservacion($datos){
		$id_hotel = ControladorHabitaciones::crtObtenerIdHotelSesion();

		if($id_hotel === null){
			return ["ok" => false, "mensaje" => "Tu negocio no tiene un hotel registrado, contacta a soporte técnico."];
		}

		$id_habitacion = isset($datos["id_habitacion"]) ? (int) $datos["id_habitacion"] : 0;
		$fecha_entrada = isset($datos["fecha_entrada"]) ? trim($datos["fecha_entrada"]) : "";
		$fecha_salida = isset($datos["fecha_salida"]) ? trim($datos["fecha_salida"]) : "";
		$precio = isset($datos["precio"]) ? (float) $datos["precio"] : 0;

		if($id_habitacion <= 0 || $fecha_entrada === "" || $fecha_salida === "" || $precio <= 0){
			return ["ok" => false, "mensaje" => "Faltan datos de la reservación."];
		}

		if(strtotime($fecha_salida) === false || strtotime($fecha_entrada) === false || strtotime($fecha_salida) <= strtotime($fecha_entrada)){
			return ["ok" => false, "mensaje" => "La fecha de salida debe ser posterior a la de entrada."];
		}

		// Se compara por día (no por minuto exacto) para no rechazar un check-in de "ahora mismo"
		// solo porque pasaron unos segundos entre elegir la hora y guardar el formulario.
		if(strtotime(date("Y-m-d", strtotime($fecha_entrada))) < strtotime(date("Y-m-d"))){
			return ["ok" => false, "mensaje" => "La fecha de entrada no puede ser en el pasado."];
		}

		// Normaliza el datetime-local del formulario ("2026-08-15T14:30") al formato de MySQL.
		$fecha_entrada = date("Y-m-d H:i:s", strtotime($fecha_entrada));
		$fecha_salida = date("Y-m-d H:i:s", strtotime($fecha_salida));

		// No permitir traslape con otra reservación activa de la misma habitación. El traslape
		// se calcula sobre la salida real (FechaSalida + HorasExtras), para que un cliente con
		// horas extras ese mismo día no deje la habitación "libre" en el sistema antes de tiempo.
		$conflictos = ModeloReservaciones::MdlVerificarDisponibilidadHabitacion($id_habitacion, $fecha_entrada, $fecha_salida);

		if(count($conflictos) > 0){
			// Si hay varios traslapes, se informa el que libera la habitación más tarde
			// (el que realmente determina hasta cuándo hay que esperar).
			$salidaRealMaxima = null;

			foreach($conflictos as $conflicto){
				$salidaReal = strtotime($conflicto["FechaSalida"]) + ((int) $conflicto["HorasExtras"] * 3600);

				if($salidaRealMaxima === null || $salidaReal > $salidaRealMaxima){
					$salidaRealMaxima = $salidaReal;
				}
			}

			return [
				"ok" => false,
				"mensaje" => "Esta habitación ya tiene una reservación activa hasta el " . date("d/m/Y g:i a", $salidaRealMaxima) . ", elige otras fechas.",
			];
		}

		$id_cliente = isset($datos["id_cliente"]) ? (int) $datos["id_cliente"] : 0;

		if($id_cliente <= 0){
			$nombre = isset($datos["nombre"]) ? trim($datos["nombre"]) : "";
			$apaterno = isset($datos["apaterno"]) ? trim($datos["apaterno"]) : "";
			$amaterno = isset($datos["amaterno"]) ? trim($datos["amaterno"]) : "";
			$telefono = isset($datos["telefono"]) ? trim($datos["telefono"]) : "";

			if($nombre === "" || $apaterno === "" || $telefono === ""){
				return ["ok" => false, "mensaje" => "Selecciona un cliente existente o captura nombre, apellido paterno y teléfono del cliente nuevo."];
			}

			// No se permite dar de alta un cliente duplicado: mismo nombre completo o mismo
			// teléfono que uno ya registrado en este hotel. Se revalida aquí (además del aviso
			// en el formulario) porque el frontend se puede saltar. Se ignoran acentos para no
			// dejar pasar duplicados solo porque alguien escribió "Martinez" en vez de "Martínez".
			$normalizar = function($valor){
				$valor = mb_strtolower(trim((string) $valor), "UTF-8");
				return str_replace(
					["á", "é", "í", "ó", "ú", "ü", "ñ"],
					["a", "e", "i", "o", "u", "u", "n"],
					$valor
				);
			};

			foreach(ModeloReservaciones::MdlBuscarClientes($id_hotel, $nombre) as $candidato){
				if($normalizar($candidato["Nombre"]) === $normalizar($nombre)
					&& $normalizar($candidato["APaterno"]) === $normalizar($apaterno)
					&& $normalizar($candidato["AMaterno"]) === $normalizar($amaterno)){
					return ["ok" => false, "mensaje" => "Ya existe un cliente registrado con ese nombre. Selecciónalo desde la búsqueda en vez de crear uno nuevo."];
				}
			}

			foreach(ModeloReservaciones::MdlBuscarClientes($id_hotel, $telefono) as $candidato){
				if((string) $candidato["Telefono"] === $telefono){
					return ["ok" => false, "mensaje" => "Ya existe un cliente registrado con este teléfono. Selecciónalo desde la búsqueda en vez de crear uno nuevo."];
				}
			}

			$clienteNuevo = ModeloReservaciones::MdlInsertarCliente($nombre, $apaterno, $amaterno, $telefono, $id_hotel);

			if(!$clienteNuevo){
				return ["ok" => false, "mensaje" => "No se pudo registrar al cliente."];
			}

			$id_cliente = (int) $clienteNuevo["id_Cliente"];
		}

		// Toda reserva nueva entra como Reservado; solo el check-in la pasa a Ocupado.
		$id_estatus = 9;

		$reservacion = ModeloReservaciones::MdlInsertarReservacion(
			$id_habitacion, $id_cliente, $precio, $fecha_entrada, $fecha_salida, $id_estatus
		);

		if(!$reservacion){
			return ["ok" => false, "mensaje" => "No se pudo guardar la reservación."];
		}

		return ["ok" => true, "folio" => $reservacion["Id_Reservacion"]];
	}

	// Mueve una reservación activa (Ocupado/Reservado) a otra habitación/fechas: la original
	// queda marcada como "Movida" (no se borra, pero el calendario no la pinta con ningún
	// color: la habitación se ve disponible/normal en esas fechas, sin dejar huella) y se
	// crea una reservación nueva (Reservado) con el mismo cliente y precio, en las fechas nuevas.
	// $datos trae id_habitacion (destino), fecha_entrada, fecha_salida.
	static public function crtMoverReservacion($id_reservacion, $datos){
		$id_hotel = ControladorHabitaciones::crtObtenerIdHotelSesion();

		if($id_hotel === null){
			return ["ok" => false, "mensaje" => "Tu negocio no tiene un hotel registrado, contacta a soporte técnico."];
		}

		$id_reservacion = trim((string) $id_reservacion);
		$id_habitacion = isset($datos["id_habitacion"]) ? (int) $datos["id_habitacion"] : 0;
		$fecha_entrada = isset($datos["fecha_entrada"]) ? trim($datos["fecha_entrada"]) : "";
		$fecha_salida = isset($datos["fecha_salida"]) ? trim($datos["fecha_salida"]) : "";

		if($id_reservacion === "" || $id_habitacion <= 0 || $fecha_entrada === "" || $fecha_salida === ""){
			return ["ok" => false, "mensaje" => "Faltan datos para mover la reservación."];
		}

		if(strtotime($fecha_salida) === false || strtotime($fecha_entrada) === false || strtotime($fecha_salida) <= strtotime($fecha_entrada)){
			return ["ok" => false, "mensaje" => "La fecha de salida debe ser posterior a la de entrada."];
		}

		if(strtotime(date("Y-m-d", strtotime($fecha_entrada))) < strtotime(date("Y-m-d"))){
			return ["ok" => false, "mensaje" => "La fecha de entrada no puede ser en el pasado."];
		}

		$fecha_entrada = date("Y-m-d H:i:s", strtotime($fecha_entrada));
		$fecha_salida = date("Y-m-d H:i:s", strtotime($fecha_salida));

		// Misma verificación de traslape que usa crear reservación. Se hace antes de tocar
		// la reservación original: si las fechas nuevas no están disponibles, no se mueve nada.
		// La reservación que se está moviendo se excluye de sus propios conflictos: si alguien
		// solo quiere agregar/quitar días en la misma habitación, el rango nuevo se traslapa
		// con el suyo propio (que sigue activo hasta este punto) y no debe contar como choque.
		$conflictos = array_values(array_filter(
			ModeloReservaciones::MdlVerificarDisponibilidadHabitacion($id_habitacion, $fecha_entrada, $fecha_salida),
			function($conflicto) use ($id_reservacion){
				return $conflicto["Id_Reservacion"] !== $id_reservacion;
			}
		));

		if(count($conflictos) > 0){
			$salidaRealMaxima = null;

			foreach($conflictos as $conflicto){
				$salidaReal = strtotime($conflicto["FechaSalida"]) + ((int) $conflicto["HorasExtras"] * 3600);

				if($salidaRealMaxima === null || $salidaReal > $salidaRealMaxima){
					$salidaRealMaxima = $salidaReal;
				}
			}

			return [
				"ok" => false,
				"mensaje" => "La habitación destino ya tiene una reservación activa hasta el " . date("d/m/Y g:i a", $salidaRealMaxima) . ", elige otras fechas.",
			];
		}

		// El precio se recalcula con la habitación y fechas nuevas (noches × precio por
		// noche), nunca se confía en lo que mande el navegador: si cambian los días o la
		// habitación tiene otra tarifa, el total de la reservación original ya no aplica.
		$precioNoche = 0;
		foreach(ModeloHabitaciones::MdlObtenerHabitaciones($id_hotel) as $habitacionDestino){
			if((int) $habitacionDestino["Id_Habitacion"] === $id_habitacion){
				$precioNoche = (float) $habitacionDestino["PrecioNoche"];
				break;
			}
		}

		$noches = (int) ceil((strtotime($fecha_salida) - strtotime($fecha_entrada)) / 86400);
		if($noches < 1){
			$noches = 1;
		}

		$precio = $noches * $precioNoche;

		$original = ModeloReservaciones::MdlMarcarReservacionMovida($id_reservacion, $id_hotel);
		$afectados = $original ? (int) $original["Afectados"] : 0;

		if($afectados === 0){
			return ["ok" => false, "mensaje" => "Esa reservación ya no está activa o no pertenece a este hotel."];
		}

		$id_cliente = (int) $original["Id_Cliente"];

		// Toda reserva nueva entra como Reservado; solo el check-in la pasa a Ocupado.
		$reservacionNueva = ModeloReservaciones::MdlInsertarReservacion(
			$id_habitacion, $id_cliente, $precio, $fecha_entrada, $fecha_salida, 9
		);

		if(!$reservacionNueva){
			return ["ok" => false, "mensaje" => "No se pudo crear la reservación en las fechas nuevas."];
		}

		return ["ok" => true, "folio" => $reservacionNueva["Id_Reservacion"]];
	}

	// Cancela una reservación Ocupada o Reservada. El SP decide el estatus de cancelación
	// correcto (12 CancelacionOcupacion / 13 CancelacionReserva) según lo que se cancela, y
	// registra folio+motivo+fecha en Tb_Cancelaciones. La habitación queda libre de inmediato
	// porque el resto de consultas (Recepción, Reserva, disponibilidad) solo consideran
	// Id_Estatus 8/9.
	static public function crtCancelarReservacion($id_reservacion, $id_motivo){
		$id_hotel = ControladorHabitaciones::crtObtenerIdHotelSesion();

		if($id_hotel === null){
			return ["ok" => false, "mensaje" => "Tu negocio no tiene un hotel registrado, contacta a soporte técnico."];
		}

		$id_reservacion = trim((string) $id_reservacion);
		$id_motivo = (int) $id_motivo;

		if($id_reservacion === ""){
			return ["ok" => false, "mensaje" => "Falta la reservación a cancelar."];
		}

		if($id_motivo <= 0){
			return ["ok" => false, "mensaje" => "Selecciona el motivo de la cancelación."];
		}

		$resultado = ModeloReservaciones::MdlCancelarReservacion($id_reservacion, $id_hotel, $id_motivo);
		$afectados = $resultado ? (int) $resultado["Afectados"] : 0;

		if($afectados === 0){
			return ["ok" => false, "mensaje" => "Esta reservación ya no está activa o no pertenece a tu hotel."];
		}

		return ["ok" => true, "mensaje" => "Reservación cancelada correctamente.", "folio" => (int) $resultado["Folio"]];
	}

	// Catálogo de motivos de cancelación, para el combo del formulario.
	static public function crtObtenerMotivosCancelacion(){
		return ModeloReservaciones::MdlObtenerMotivosCancelacion();
	}

	// Completa el Check Out de una reservación Ocupada (pasa a Id_Estatus=20, Completada),
	// liberando la habitación.
	static public function crtCompletarCheckout($id_reservacion){
		$id_hotel = ControladorHabitaciones::crtObtenerIdHotelSesion();

		if($id_hotel === null){
			return ["ok" => false, "mensaje" => "Tu negocio no tiene un hotel registrado, contacta a soporte técnico."];
		}

		$id_reservacion = trim((string) $id_reservacion);

		if($id_reservacion === ""){
			return ["ok" => false, "mensaje" => "Falta la reservación."];
		}

		$resultado = ModeloReservaciones::MdlCompletarCheckout($id_reservacion, $id_hotel);
		$afectados = $resultado ? (int) $resultado["Afectados"] : 0;

		if($afectados === 0){
			return ["ok" => false, "mensaje" => "Esta reservación ya no está en estado Ocupado."];
		}

		return ["ok" => true, "mensaje" => "Check out completado correctamente."];
	}

	// Consumo desglosado (productos vendidos ligados a la estadía) para el modal de Check Out.
	static public function crtObtenerConsumoReservacion($id_reservacion){
		$id_reservacion = trim((string) $id_reservacion);

		if($id_reservacion === ""){
			return [];
		}

		return ModeloReservaciones::MdlObtenerConsumoReservacion($id_reservacion);
	}

	// Precio de hospedaje pactado + horas extra/anticipada de la estadía, para el resumen de
	// cobro del Check Out. Las horas extra ya se cobran como producto en Punto de Venta (por
	// lo que aparecen dentro del consumo); las horas anticipada no tienen tarifa configurada,
	// así que aquí solo se informa la cantidad, sin sumarles un precio.
	static public function crtObtenerHospedajeReservacion($id_reservacion){
		$id_reservacion = trim((string) $id_reservacion);

		if($id_reservacion === ""){
			return null;
		}

		$id_hotel = ControladorHabitaciones::crtObtenerIdHotelSesion();

		if($id_hotel === null){
			return null;
		}

		return ModeloReservaciones::MdlObtenerReservacionParaCheckout($id_reservacion, $id_hotel);
	}

	// Confirma el check-in de una reservación Reservada (pasa a Id_Estatus=8, Ocupado).
	// Si el huésped llega antes de FechaEntrada (más allá de la tolerancia configurada),
	// cobra automáticamente las Horas Anticipadas correspondientes.
	static public function crtConfirmarCheckIn($id_reservacion){
		$id_hotel = ControladorHabitaciones::crtObtenerIdHotelSesion();

		if($id_hotel === null){
			return ["ok" => false, "mensaje" => "Tu negocio no tiene un hotel registrado, contacta a soporte técnico."];
		}

		$id_reservacion = trim((string) $id_reservacion);

		if($id_reservacion === ""){
			return ["ok" => false, "mensaje" => "Falta la reservación."];
		}

		// Se vuelve a calcular aquí (no se confía en nada que haya mandado el cliente sobre
		// horas/precio) para que el cobro quede protegido aunque alguien salte el popup de
		// vista previa y llame este endpoint directamente.
		$anticipada = self::crtEvaluarLlegadaAnticipada($id_reservacion, $id_hotel);

		if($anticipada["requiereCargo"] && $anticipada["permitido"] === false){
			return ["ok" => false, "mensaje" => $anticipada["mensaje"] ?? "La habitación anterior aún no está disponible para recibir al huésped tan pronto."];
		}

		$resultado = ModeloReservaciones::MdlConfirmarCheckIn($id_reservacion, $id_hotel);
		$afectados = $resultado ? (int) $resultado["Afectados"] : 0;

		if($afectados === 0){
			return ["ok" => false, "mensaje" => "Esta reservación ya no está en estado Reservada."];
		}

		$horasAnticipada = 0;
		$montoAnticipada = 0;

		if($anticipada["requiereCargo"] && $anticipada["permitido"] === true && $anticipada["horas"] > 0){
			$idUsuario = $_SESSION["IdUsuario"] ?? null;
			$ticketResp = ModeloVentas::MdlObtenerNuevoTicket($idUsuario);
			$ticket = $ticketResp ? (int) $ticketResp["NuevoTicket"] : 0;

			if($ticket > 0 && $anticipada["idInventarioAnticipada"] > 0){
				ModeloVentas::MdlInsertarVenta(
					$anticipada["idInventarioAnticipada"],
					$anticipada["horas"],
					$ticket,
					$idUsuario,
					$anticipada["precioTotal"],
					$anticipada["idCliente"],
					$id_reservacion
				);

				ModeloReservaciones::MdlActualizarHoraAnticipada($id_reservacion, $anticipada["horas"]);

				$horasAnticipada = $anticipada["horas"];
				$montoAnticipada = $anticipada["precioTotal"];
			}
		}

		$mensaje = $horasAnticipada > 0
			? "Check-in confirmado. Se cobraron {$horasAnticipada} hora(s) anticipada(s)."
			: "Check-in confirmado.";

		return [
			"ok" => true,
			"mensaje" => $mensaje,
			"horasAnticipada" => $horasAnticipada,
			"montoAnticipada" => $montoAnticipada,
		];
	}

	// Vista previa (sin efectos secundarios) de si una reservación va a requerir cobro de
	// Horas Anticipadas al hacer check-in, para mostrar el popup adecuado antes de confirmar.
	static public function crtValidarLlegadaAnticipada($id_reservacion){
		$id_hotel = ControladorHabitaciones::crtObtenerIdHotelSesion();

		if($id_hotel === null){
			return ["ok" => false, "mensaje" => "Tu negocio no tiene un hotel registrado, contacta a soporte técnico."];
		}

		$id_reservacion = trim((string) $id_reservacion);

		if($id_reservacion === ""){
			return ["ok" => false, "mensaje" => "Falta la reservación."];
		}

		return self::crtEvaluarLlegadaAnticipada($id_reservacion, $id_hotel);
	}

	// Núcleo compartido por el preview y el commit: calcula si la reservación llega antes de su
	// FechaEntrada más allá de la tolerancia configurada (cat_estatus Id_Estatus=14) y, de ser
	// así, si la habitación anterior permite cubrir esas horas y cuánto costarían.
	private static function crtEvaluarLlegadaAnticipada($id_reservacion, $id_hotel){
		$base = [
			"ok" => true,
			"mensaje" => null,
			"requiereCargo" => false,
			"permitido" => null,
			"horas" => 0,
			"precioUnitario" => null,
			"precioTotal" => null,
			"salidaAnterior" => null,
			"idHabitacion" => null,
			"idCliente" => null,
			"idInventarioAnticipada" => null,
		];

		$reservacion = ModeloReservaciones::MdlObtenerReservacionParaCheckin($id_reservacion, $id_hotel);

		if(!$reservacion || (int) $reservacion["Id_Estatus"] !== 9){
			$base["ok"] = false;
			$base["mensaje"] = "Esta reservación ya no está en estado Reservada.";
			return $base;
		}

		$idHabitacion = (int) $reservacion["Id_Habitacion"];
		$fechaEntrada = $reservacion["FechaEntrada"];

		$base["idHabitacion"] = $idHabitacion;
		$base["idCliente"] = (int) $reservacion["Id_Cliente"];

		$tolerancia = ModeloReservaciones::MdlObtenerToleranciaAnticipada();
		$horas = self::crtCalcularHorasAnticipadas($fechaEntrada, $tolerancia);

		if($horas <= 0){
			return $base; // llegó a tiempo (o dentro de la tolerancia): sin cargo, popup normal.
		}

		$base["requiereCargo"] = true;
		$base["horas"] = $horas;

		// Tope de anticipación: pasada esta ventana no se ofrece check-in anticipado (el botón ya
		// ni siquiera se muestra en Recepción, ver ControladorHabitaciones::VENTANA_MAXIMA_CHECKIN_ANTICIPADO_HORAS).
		// Esto evita cobrar HrsAnticipadas por adelantar una reserva un día entero.
		if($horas > ControladorHabitaciones::VENTANA_MAXIMA_CHECKIN_ANTICIPADO_HORAS){
			$base["permitido"] = false;
			$horaMinima = strtotime($fechaEntrada) - (ControladorHabitaciones::VENTANA_MAXIMA_CHECKIN_ANTICIPADO_HORAS * 3600);
			$base["mensaje"] = "Aún es muy pronto para hacer check-in. Podrás hacerlo a partir de las " . date("g:i a", $horaMinima) . " del " . date("d/m/Y", $horaMinima) . ".";
			return $base;
		}

		// Reutiliza la misma validación que ya usa (para venta manual) ajax/validarHorasAnticipadas.ajax.php:
		// no se pueden cubrir horas anticipadas si la habitación anterior no se desocupa a tiempo.
		$anterior = ModeloHoteles::MdlObtenerReservacionAnterior($idHabitacion, $fechaEntrada, $id_reservacion);
		$maxHoras = ($anterior && isset($anterior["HorasDisponibles"])) ? (int) $anterior["HorasDisponibles"] : null;

		if($anterior && $maxHoras !== null && $horas > $maxHoras){
			$base["permitido"] = false;
			$base["salidaAnterior"] = !empty($anterior["FechaSalidaEfectiva"])
				? date("d/m/Y g:i a", strtotime($anterior["FechaSalidaEfectiva"]))
				: null;
			return $base;
		}

		$base["permitido"] = true;

		$producto = ModeloInventarios::MdlObtenerInventario($_SESSION["IdUsuario"] ?? null, "HRSANTICIPADA");

		if(!$producto || !isset($producto["Id_Inventario"])){
			// Sin el producto dado de alta en Inventario no hay forma de cobrar: se bloquea el
			// check-in anticipado en vez de dejarlo pasar sin costo.
			$base["permitido"] = false;
			$base["mensaje"] = "No se encontró el producto de Horas Anticipadas en el inventario, contacta a soporte técnico.";
			return $base;
		}

		$base["idInventarioAnticipada"] = (int) $producto["Id_Inventario"];
		$base["precioUnitario"] = (float) $producto["PrecioVenta"];
		$base["precioTotal"] = $base["precioUnitario"] * $horas;

		return $base;
	}

	// Convierte "cuántos minutos antes llega" en horas facturables, redondeando hacia arriba
	// cuando el residuo pasa de la tolerancia configurada (ej. 1h40m antes con 30min de
	// tolerancia = 1h completa + 40min de residuo > 30min -> se cobran 2 horas).
	private static function crtCalcularHorasAnticipadas($fechaEntrada, $toleranciaMinutos){
		if(!$fechaEntrada){
			return 0;
		}

		$minutosAntes = (strtotime($fechaEntrada) - time()) / 60;

		if($minutosAntes <= 0){
			return 0;
		}

		$horasCompletas = intdiv((int) $minutosAntes, 60);
		$residual = $minutosAntes - ($horasCompletas * 60);

		return ($residual > $toleranciaMinutos) ? $horasCompletas + 1 : $horasCompletas;
	}

}
