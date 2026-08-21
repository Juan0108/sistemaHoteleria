<?php

class ControladorHabitaciones{

	// Máximo de horas antes de FechaEntrada en que Recepción puede hacer check-in anticipado
	// (con cobro de HrsAnticipadas). Más allá de esto no tiene sentido cobrarle a alguien un día
	// entero de anticipación solo porque su reserva es para esa fecha: el botón de check-in se
	// oculta (ver crtObtenerHabitacionesRecepcion) y el servidor lo rechaza aunque lo llamen
	// directo (ver ControladorReservaciones::crtEvaluarLlegadaAnticipada).
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

	// Habitaciones con sus reservaciones del mes (recortadas a los días del mes visible), para el
	// calendario del módulo Reserva. Cada habitación regresa con "Carriles": un arreglo de una o
	// más filas (cada una indexada por día de inicio, lista para dibujar con colspan). Cuando dos
	// reservaciones de la misma habitación se traslapan, se reparten en carriles distintos para
	// que ambas queden visibles en vez de que una tape a la otra.
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
		// 12=CancelacionOcupacion (se canceló una estadía que ya había iniciado),
		// 19=Movida (se reagendó a otra habitación/fechas). Ambas se pintan en el
		// calendario como rastro histórico; 13 (se canceló una reserva que nunca llegó
		// a ocuparse) no se pinta, no aporta nada ver un hueco que nunca fue real.
		$claseParaEstatus = [8 => "ocupada", 9 => "reservada", 12 => "cancelada", 19 => "movida"];
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

			// El día de salida no cuenta como ocupado (checkout), por eso el -86400.
			$diaInicio = max(1, (int) (($tsEntrada - $tsMesInicio) / 86400) + 1);
			$diaFin = min($totalDias, (int) ((($tsSalida - 86400) - $tsMesInicio) / 86400) + 1);

			if($diaFin < $diaInicio){
				// La reservación no toca ningún día visible de este mes.
				continue;
			}

			$nombreCliente = trim(($res["Nombre"] ?? "") . " " . ($res["APaterno"] ?? "") . " " . ($res["AMaterno"] ?? ""));

			$tituloBarra = "Entrada: " . date("d/m/Y g:i a", strtotime($res["FechaEntrada"]))
						 . " · Salida: " . date("d/m/Y g:i a", strtotime($res["FechaSalida"]));

			if($nombreCliente !== ""){
				$tituloBarra = $nombreCliente . " · " . $tituloBarra;
			}

			if(!isset($segmentosPorHabitacion[$idHab])){
				$segmentosPorHabitacion[$idHab] = [];
			}

			$segmentosPorHabitacion[$idHab][] = [
				"inicio"        => $diaInicio,
				"fin"           => $diaFin,
				"estado"        => $clase,
				"titulo"        => $tituloBarra,
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

			// El botón de check-in solo se ofrece dentro de la ventana de anticipación permitida
			// (o si ya llegó/pasó la hora de entrada). Fuera de esa ventana no se muestra: no
			// tiene caso ofrecer un check-in que el servidor va a rechazar de todas formas.
			$puedeCheckin = false;

			if($clase === "reservada" && !empty($reserva["fechaEntrada"])){
				$segundosParaEntrada = strtotime($reserva["fechaEntrada"]) - time();
				$ventanaSegundos = self::VENTANA_MAXIMA_CHECKIN_ANTICIPADO_HORAS * 3600;

				$puedeCheckin = $segundosParaEntrada <= $ventanaSegundos;
			}

			$hab["PuedeCheckin"] = $puedeCheckin;

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