<?php

class ControladorHabitaciones{

	
	const VENTANA_MAXIMA_CHECKIN_ANTICIPADO_HORAS = 4;

	static public function crtObtenerHoteles(){
		$respuesta = ModeloHabitaciones::MdlObtenerHoteles();
		return $respuesta;
	}

	// Resuelve el hotel del usuario en sesión a partir de su negocio.
	// El usuario nunca elige ni ve el hotel: se deriva automáticamente.
	static public function crtObtenerIdHotelSesion(){
		if(!isset($_SESSION["IdNegocio"])){
			return null;
		}

		$hotel = ModeloHabitaciones::MdlObtenerHotelPorNegocio($_SESSION["IdNegocio"]);

		return $hotel ? $hotel["Id_Hotel"] : null;
	}

	static public function crtObtenerHabitaciones(){
		$id_hotel = self::crtObtenerIdHotelSesion();

		if($id_hotel === null){
			return [];
		}

		$respuesta = ModeloHabitaciones::MdlObtenerHabitaciones($id_hotel);
		return $respuesta;
	}

	// (Disponible/Ocupado/Reservado), calculado a partir de Tb_Reservaciones.
	static public function crtObtenerHabitacionesRecepcion($fecha = null){
		$id_hotel = self::crtObtenerIdHotelSesion();

		if($id_hotel === null){
			return [];
		}

		if($fecha === null){
			$fecha = date("Y-m-d");
		}

		$habitaciones = ModeloHabitaciones::MdlObtenerHabitaciones($id_hotel);
		$reservaciones = ModeloHabitaciones::MdlObtenerReservacionesPorFecha($id_hotel, $fecha);

		$resultado = self::crtFusionarHabitacionesConReservaciones($habitaciones, $reservaciones, false);

		// Para las habitaciones que quedaron Disponibles, se busca la próxima reservación (si
		// existe) para poder mostrar "Disponible hasta..." en la tarjeta.
		foreach($resultado as &$hab){
			$hab["ProximaReservacion"] = ($hab["EstadoClase"] === "disponible")
				? self::crtObtenerProximaReservacionParaTarjeta((int) $hab["Id_Habitacion"], $fecha)
				: null;
		}
		unset($hab);

		self::crtAplicarConteoReservasProximas($resultado, $id_hotel);

		return $resultado;
	}

	// Agrega "ReservasProximas" a cada habitación: cuántas reservaciones Reservadas (no la
	// estadía actual) tiene agendadas a futuro, para el badge de la tarjeta de Recepción.
	static private function crtAplicarConteoReservasProximas(&$resultado, $id_hotel){
		$conteos = ModeloReservaciones::MdlObtenerConteoReservasProximas($id_hotel);

		$conteoPorHabitacion = [];
		foreach($conteos as $fila){
			$conteoPorHabitacion[(int) $fila["Id_Habitacion"]] = (int) $fila["Total"];
		}

		foreach($resultado as &$hab){
			$hab["ReservasProximas"] = $conteoPorHabitacion[(int) $hab["Id_Habitacion"]] ?? 0;
		}
		unset($hab);
	}

	// Calcula, con la misma fórmula que usa Punto de Venta (ObtenerSiguienteReservacion), cuántas
	// horas de margen hay antes de que llegue la próxima reservación de una habitación Disponible.
	static private function crtObtenerProximaReservacionParaTarjeta($id_habitacion, $fecha){
		$proxima = ModeloReservaciones::MdlObtenerProximaReservacionHabitacion($id_habitacion, $fecha . " 00:00:00");

		if(!$proxima){
			return null;
		}

		return [
			"fechaEntrada"     => $proxima["FechaEntrada"],
			"horasDisponibles" => (int) $proxima["HorasDisponibles"],
		];
	}

	// Reservaciones vigentes (Ocupado/Reservado) cuyo folio o cliente coincide con $termino,
	// sin importar la fecha. Solo regresa las habitaciones que sí coinciden.
	static public function crtBuscarHabitacionesRecepcion($termino){
		$id_hotel = self::crtObtenerIdHotelSesion();

		if($id_hotel === null){
			return [];
		}

		$habitaciones = ModeloHabitaciones::MdlObtenerHabitaciones($id_hotel);
		$reservaciones = ModeloHabitaciones::MdlBuscarReservacionPorFolio($id_hotel, $termino);

		$resultado = self::crtFusionarHabitacionesConReservaciones($habitaciones, $reservaciones, true);

		self::crtAplicarConteoReservasProximas($resultado, $id_hotel);

		return $resultado;
	}

	
	static public function crtObtenerHabitacionesReserva($anio, $mes){
		$id_hotel = self::crtObtenerIdHotelSesion();

		if($id_hotel === null){
			return [];
		}

		$habitaciones = ModeloHabitaciones::MdlObtenerHabitaciones($id_hotel);
		$reservaciones = ModeloHabitaciones::MdlObtenerReservacionesPorMes($id_hotel, $anio, $mes);

		$totalDias = (int) date("t", mktime(0, 0, 0, $mes, 1, $anio));
		$tsMesInicio = mktime(0, 0, 0, $mes, 1, $anio);

		// IDs de cat_estatus usados en reservaciones: 8=Ocupado, 9=Reservado,
		// 12=CancelacionOcupacion, 13=CancelacionReserva. Cada una se pinta con su propio
		// color/patrón en el calendario. 19=Movida (reservación reagendada a otra
		// habitación/fechas) queda fuera a propósito: al no estar en este mapa, esas fechas
		// simplemente no se pintan, para que se vean disponibles/normales sin ninguna huella.
		$claseParaEstatus = [8 => "ocupada", 9 => "reservada", 12 => "cancelada-estadia", 13 => "cancelada-reserva"];
		$segmentosPorHabitacion = [];

		foreach($reservaciones as $res){
			$idEstatusRes = (int) $res["Id_Estatus"];

			if(!isset($claseParaEstatus[$idEstatusRes])){
				continue;
			}

			$idHab = (int) $res["Id_Habitacion"];
			$clase = $claseParaEstatus[$idEstatusRes];

			$tsEntrada = strtotime(date("Y-m-d", strtotime($res["FechaEntrada"])));
			$tsSalida = strtotime(date("Y-m-d", strtotime($res["FechaSalida"])));

			// La barra cubre el rango completo, incluyendo el día de salida (checkout).
			$diaInicio = max(1, (int) (($tsEntrada - $tsMesInicio) / 86400) + 1);
			$diaFin = min($totalDias, (int) (($tsSalida - $tsMesInicio) / 86400) + 1);

			if($diaFin < $diaInicio){
				// La reservación no toca ningún día visible de este mes.
				continue;
			}

			$nombreCliente = trim(($res["Nombre"] ?? "") . " " . ($res["APaterno"] ?? "") . " " . ($res["AMaterno"] ?? ""));

			// Mismo ajuste que ya usa Recepción: la entrada real se adelanta por las horas
			// anticipadas cobradas, y la salida real se corre por las horas extra, para que el
			// tooltip refleje la hora real de la estadía, no solo la programada originalmente.
			$horasExtras = (int) ($res["HorasExtras"] ?? 0);
			$horaAnticipada = (int) ($res["HoraAnticipada"] ?? 0);
			$tsEntradaReal = strtotime($res["FechaEntrada"]) - ($horaAnticipada * 3600);
			$tsSalidaReal = strtotime($res["FechaSalida"]) + ($horasExtras * 3600);

			// Nativo del navegador (atributo title): no se puede pintar con colores ni
			// tipografía propia, pero sí se acomoda en líneas para que se lea más ordenado.
			// La hora de entrada/salida ya viene sumada/restada por anticipada/extra; la nota
			// de cuántas horas fueron va aparte, ya no se marca con un ícono en la barra.
			$tituloBarra = ($nombreCliente !== "" ? $nombreCliente . "\n" : "")
						 . "Entrada: " . date("d/m/Y g:i a", $tsEntradaReal) . "\n"
						 . "Salida: " . date("d/m/Y g:i a", $tsSalidaReal);

			if($horaAnticipada > 0){
				$tituloBarra .= "\nIncluye " . $horaAnticipada . "h anticipada";
			}
			if($horasExtras > 0){
				$tituloBarra .= "\nIncluye " . $horasExtras . "h extra";
			}

			if(!isset($segmentosPorHabitacion[$idHab])){
				$segmentosPorHabitacion[$idHab] = [];
			}

			$segmentosPorHabitacion[$idHab][] = [
				"inicio"        => $diaInicio,
				"fin"           => $diaFin,
				"estado"        => $clase,
				"titulo"        => $tituloBarra,
				"nombreCliente" => $nombreCliente,
				"horasExtras"   => $horasExtras,
				"horaAnticipada" => $horaAnticipada,
				// Solo se usan para "mover reservación" (ocupada/reservada); en cancelada/
				// movida no hace falta moverlas de nuevo, pero no cuesta nada llevarlos.
				"idReservacion" => $res["Id_Reservacion"] ?? "",
				"idHabitacion"  => $idHab,
				"fechaEntrada"  => $res["FechaEntrada"] ?? "",
				"fechaSalida"   => $res["FechaSalida"] ?? "",
			];
		}

		$resultado = [];

		foreach($habitaciones as $hab){
			$idEstatus = isset($hab["Id_Estatus"]) ? (int) $hab["Id_Estatus"] : 1;

			if($idEstatus === 2){
				// Inhabilitada en Habitaciones: no se muestra en Recepción/Reserva.
				continue;
			}

			$segmentos = $segmentosPorHabitacion[(int) $hab["Id_Habitacion"]] ?? [];
			$hab["Carriles"] = self::crtAsignarCarriles($segmentos);
			$resultado[] = $hab;
		}

		return $resultado;
	}

	static private function crtAsignarCarriles($segmentos){
		usort($segmentos, function($a, $b){ return $a["inicio"] <=> $b["inicio"]; });

		$carriles = [];

		foreach($segmentos as $seg){
			$colocado = false;

			foreach($carriles as &$carril){
				if($seg["inicio"] > $carril["finUltimo"]){
					$carril["mapa"][$seg["inicio"]] = $seg;
					$carril["finUltimo"] = $seg["fin"];
					$colocado = true;
					break;
				}
			}
			unset($carril);

			if(!$colocado){
				$carriles[] = ["finUltimo" => $seg["fin"], "mapa" => [$seg["inicio"] => $seg]];
			}
		}

		if(count($carriles) === 0){
			return [[]];
		}

		return array_map(function($carril){ return $carril["mapa"]; }, $carriles);
	}

	
	static private function crtFusionarHabitacionesConReservaciones($habitaciones, $reservaciones, $soloConReserva){
		
		$claseParaEstatus = [8 => "ocupada", 9 => "reservada"];
		$estadoPorHabitacion = [];

		foreach($reservaciones as $res){
			$idHab = (int) $res["Id_Habitacion"];
			$clase = $claseParaEstatus[(int) $res["Id_Estatus"]] ?? "reservada";

			if(!isset($estadoPorHabitacion[$idHab]) || $clase === "ocupada"){
				$nombreCliente = trim($res["Nombre"] . " " . $res["APaterno"] . " " . $res["AMaterno"]);

				$estadoPorHabitacion[$idHab] = [
					"clase"          => $clase,
					"fechaEntrada"   => $res["FechaEntrada"],
					"fechaSalida"    => $res["FechaSalida"],
					"idReservacion"  => $res["Id_Reservacion"],
					"nombreCliente"  => $nombreCliente,
					"horasExtras"    => (int) ($res["HorasExtras"] ?? 0),
					"horaAnticipada" => (int) ($res["HoraAnticipada"] ?? 0),
				];
			}
		}

		$estados = [
			"ocupada"    => ["texto" => "Ocupada",    "icono" => "fa-user"],
			"reservada"  => ["texto" => "Reservada",  "icono" => "fa-calendar"],
			"disponible" => ["texto" => "Disponible", "icono" => "fa-check"],
		];

		$resultado = [];

		foreach($habitaciones as $hab){
			$idEstatus = isset($hab["Id_Estatus"]) ? (int) $hab["Id_Estatus"] : 1;

			if($idEstatus === 2){
				// Inhabilitada en Habitaciones: no se muestra en Recepción/Reserva.
				continue;
			}

			$reserva = $estadoPorHabitacion[(int) $hab["Id_Habitacion"]] ?? null;

			if($soloConReserva && $reserva === null){
				continue;
			}

			$clase = $reserva["clase"] ?? "disponible";
			$estado = $estados[$clase];

			$hab["EstadoClase"] = $clase;
			$hab["EstadoTexto"] = $estado["texto"];
			$hab["EstadoIcono"] = $estado["icono"];
			$hab["FechaEntrada"] = $reserva["fechaEntrada"] ?? null;
			$hab["FechaSalida"] = $reserva["fechaSalida"] ?? null;
			$hab["Id_Reservacion"] = $reserva["idReservacion"] ?? null;
			$hab["NombreCliente"] = $reserva["nombreCliente"] ?? null;
			$hab["HorasExtras"] = $reserva["horasExtras"] ?? 0;
			$hab["HoraAnticipada"] = $reserva["horaAnticipada"] ?? 0;

			$puedeCheckin = $clase === "reservada"
				&& !empty($reserva["fechaEntrada"])
				&& date("Y-m-d", strtotime($reserva["fechaEntrada"])) <= date("Y-m-d");

			$hab["PuedeCheckin"] = $puedeCheckin;

			// Solo aparece el día exacto de la salida (no antes, no después de que ya se venció).
			$puedeCheckout = $clase === "ocupada"
				&& !empty($reserva["fechaSalida"])
				&& date("Y-m-d", strtotime($reserva["fechaSalida"])) === date("Y-m-d");

			$hab["PuedeCheckout"] = $puedeCheckout;

			$resultado[] = $hab;
		}

		return $resultado;
	}

	static public function crtInsertarHabitacion()
	{
		if(isset($_POST["nuevaHabitacion"])){

			$_POST["nuevoPrecio"] = str_replace(',', '', $_POST["nuevoPrecio"]);
			$_POST["nuevaDescripcion"] = mb_substr($_POST["nuevaDescripcion"], 0, 255);
			$_POST["nuevoTipo"] = mb_substr($_POST["nuevoTipo"], 0, 100);

			if(preg_match('/^[a-zA-Z0-9 ]+$/', $_POST["nuevoNumero"]) &&
			   preg_match('/^[0-9]+$/', $_POST["nuevaCapacidad"]) &&
			   preg_match('/^[0-9.]+$/', $_POST["nuevoPrecio"])) {

				$id_hotel = self::crtObtenerIdHotelSesion();

				if($id_hotel === null){

					echo '<script>
						Swal.fire({
							icon: "error",
							title : "Sistema PosDit",
							text: "¡Tu negocio no tiene un hotel registrado, contacta a soporte técnico!",
							confirmButtonText: "Cerrar"
						});
					</script>';
					return;
				}

				$foto = $_FILES["nuevaFoto"]["tmp_name"];

				if($foto != ""){

					$NombreImagen = $_FILES["nuevaFoto"]["name"];
					$dirPath = "views/img/Habitaciones/";
					if (!is_dir($dirPath)) {
						mkdir($dirPath, 0755, true);
					}
					$directorio = $dirPath.$NombreImagen;

					$Habitacion = new habitacion(0,
									 $id_hotel,
									 $_POST["nuevoNumero"],
									 $_POST["nuevaDescripcion"],
									 $_POST["nuevoTipo"],
									 $_POST["nuevaCapacidad"],
									 $_POST["nuevoPrecio"],
									 $directorio,
									 1); // Estatus activo por defecto

					$respuesta = ModeloHabitaciones::MdlInsertarHabitacion($Habitacion);

					if($respuesta[0][0] == "1"){

						$fotoMovida = move_uploaded_file($foto, $directorio);

						if($fotoMovida){
							echo '<script>
								Swal.fire({
								icon: "success",
								title : "Sistema PosDit",
								text: "¡La habitación ha sido guardada correctamente!",
								showConfirmButton: true,
								confirmButtonText: "Cerrar"
								});
							</script>';
						}else{
							echo '<script>
								Swal.fire({
								icon: "warning",
								title : "Sistema PosDit",
								text: "¡La habitación se guardó, pero la foto no pudo subirse, intenta editarla de nuevo!",
								showConfirmButton: true,
								confirmButtonText: "Cerrar"
								});
							</script>';
						}

					}else{

						echo '<script>
							Swal.fire({
								icon: "error",
								title : "Sistema PosDit",
								text: "¡La habitación ya existe, favor de validar!",
								showConfirmButton: true,
								confirmButtonText: "Cerrar"
							}).then(function(result){
								if(result.value){
									window.location = "habitaciones";
								}
							});
						</script>';
					}

				}else{

					echo '<script>
						Swal.fire({
							title : "Sistema PosDit",
							text: "¡Favor de cargar una foto para la habitación!",
							icon: "error",
							confirmButtonText: "¡Cerrar!"
						});
					</script>';
				}

			}else{

				echo '<script>
					Swal.fire({
						icon: "error",
						title : "Sistema PosDit",
						text: "¡Los datos no pueden ir vacíos o llevar caracteres inválidos!",
						showConfirmButton: true,
						confirmButtonText: "Cerrar"
					});
				</script>';
			}
		}
	}

	static public function crtActualizarHabitacion()
	{
		if(isset($_POST["editarHabitacion"])){

			$_POST["editarPrecio"] = str_replace(',', '', $_POST["editarPrecio"]);
			$_POST["editarDescripcion"] = mb_substr($_POST["editarDescripcion"], 0, 255);
			$_POST["editarTipo"] = mb_substr($_POST["editarTipo"], 0, 100);

			if(preg_match('/^[a-zA-Z0-9 ]+$/', $_POST["editarNumero"]) &&
			   preg_match('/^[0-9]+$/', $_POST["editarCapacidad"]) &&
			   preg_match('/^[0-9.]+$/', $_POST["editarPrecio"])) {

				$id_hotel = self::crtObtenerIdHotelSesion();

				if($id_hotel === null){

					echo '<script>
						Swal.fire({
							icon: "error",
							title : "Sistema PosDit",
							text: "¡Tu negocio no tiene un hotel registrado, contacta a soporte técnico!",
							confirmButtonText: "Cerrar"
						});
					</script>';
					return;
				}

				$fotoNueva = $_FILES["editarFoto"]["tmp_name"];

				if($fotoNueva != ""){
					$NombreImagen = $_FILES["editarFoto"]["name"];
					$dirPath = "views/img/Habitaciones/";
					if (!is_dir($dirPath)) {
						mkdir($dirPath, 0755, true);
					}
					$directorio = $dirPath.$NombreImagen;
				}else{
					// No se seleccionó una foto nueva: conservar la que ya tenía
					$directorio = $_POST["editarFotoActual"];
				}

				// El toggle es un checkbox: si no está marcado, el navegador ni siquiera lo envía
				$Estatus = isset($_POST["editarEstatus"]) ? 1 : 2;

				$Habitacion = new habitacion(
								 $_POST["editarIdHabitacion"],
								 $id_hotel,
								 $_POST["editarNumero"],
								 $_POST["editarDescripcion"],
								 $_POST["editarTipo"],
								 $_POST["editarCapacidad"],
								 $_POST["editarPrecio"],
								 $directorio,
								 $Estatus);

				$respuesta = ModeloHabitaciones::MdlActualizarHabitacion($Habitacion);

				if($respuesta == 1){

					$fotoMovida = true;
					if($fotoNueva != ""){
						$fotoMovida = move_uploaded_file($fotoNueva, $directorio);
					}

					if($fotoMovida){
						echo '<script>
							Swal.fire({
							icon: "success",
							title : "Sistema PosDit",
							text: "¡La habitación ha sido modificada correctamente!",
							showConfirmButton: true,
							confirmButtonText: "Cerrar"
							});
						</script>';
					}else{
						echo '<script>
							Swal.fire({
							icon: "warning",
							title : "Sistema PosDit",
							text: "¡La habitación se modificó, pero la foto no pudo subirse, intenta editarla de nuevo!",
							showConfirmButton: true,
							confirmButtonText: "Cerrar"
							});
						</script>';
					}

				}else{

					echo '<script>
						Swal.fire({
							icon: "error",
							title : "Sistema PosDit",
							text: "¡Error desconocido, intentelo más tarde!",
							showConfirmButton: true,
							confirmButtonText: "Cerrar"
						});
					</script>';
				}

			}else{

				echo '<script>
					Swal.fire({
						icon: "error",
						title : "Sistema PosDit",
						text: "¡Los datos no pueden ir vacíos o llevar caracteres inválidos!",
						showConfirmButton: true,
						confirmButtonText: "Cerrar"
					});
				</script>';
			}
		}
	}
}