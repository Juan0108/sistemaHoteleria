<?php

require_once dirname(__DIR__) . "/controllers/habitaciones.controlador.php";
require_once dirname(__DIR__) . "/models/habitaciones.modelo.php";
require_once dirname(__DIR__) . "/models/mantenimiento.modelo.php";

class ControladorMantenimiento{

	// El ícono de cada pieza se resuelve aquí (no vive en la BD): así se puede
	// ajustar/agregar sin tocar cat_PiezasMantenimiento. Clases de Font Awesome
	// 4.7, ya incluido en el proyecto.
	const MAPA_ICONOS_PIEZAS = [
		"Aire acondicionado" => "fa-snowflake-o",
		"Cama / Mobiliario"  => "fa-bed",
		"Baño / Plomería"    => "fa-shower",
		"Electricidad"       => "fa-bolt",
		"Cerradura"          => "fa-key",
		"Televisión"         => "fa-television",
		"Cortinas / Ventana" => "fa-window-maximize",
		"Pintura / Pared"    => "fa-paint-brush",
		"Otro"               => "fa-wrench",
	];

	const ICONO_PIEZA_DEFECTO = "fa-wrench";

	// Estatus del tablero (deben coincidir con cat_estatus 15/16/17)
	const ESTATUS_PENDIENTE = 15;
	const ESTATUS_PROCESO   = 16;
	const ESTATUS_RESUELTO  = 17;

	static public function crtObtenerIdHotelSesion(){
		return ControladorHabitaciones::crtObtenerIdHotelSesion();
	}

	static public function crtObtenerIconoPieza($nombrePieza){
		return self::MAPA_ICONOS_PIEZAS[$nombrePieza] ?? self::ICONO_PIEZA_DEFECTO;
	}

	// Habitaciones del hotel en sesión, para el combo del formulario
	static public function crtObtenerHabitaciones(){
		return ControladorHabitaciones::crtObtenerHabitaciones();
	}

	static public function crtObtenerTipos(){
		return ModeloMantenimiento::MdlObtenerTiposMantenimiento();
	}

	static public function crtObtenerPiezas(){
		return ModeloMantenimiento::MdlObtenerPiezasMantenimiento();
	}

	static public function crtObtenerMotivos(){
		return ModeloMantenimiento::MdlObtenerMotivosMantenimiento();
	}

	// Detalle para el popup "Detalle" de la tarjeta.
	static public function crtObtenerDetalle($id_mantenimiento){
		$id_hotel = self::crtObtenerIdHotelSesion();

		if($id_hotel === null){
			return null;
		}

		$fila = ModeloMantenimiento::MdlObtenerDetalleMantenimiento($id_mantenimiento, $id_hotel);

		if(!$fila){
			return null;
		}

		$nombreEncargado = trim(($fila["Nombre"] ?? "") . " " . ($fila["Apaterno"] ?? "") . " " . ($fila["Amaterno"] ?? ""));

		return [
			"encargado"           => $nombreEncargado !== "" ? $nombreEncargado : "Sin asignar",
			"fechaRegistro"       => date("d/m/Y g:i a", strtotime($fila["Fecha_Registro"])),
			"fechaInicioEstimado" => $fila["Fecha_InicioEstimado"] ? date("d/m/Y", strtotime($fila["Fecha_InicioEstimado"])) : null,
			"fechaFinEstimado"    => $fila["Fecha_FinEstimado"] ? date("d/m/Y", strtotime($fila["Fecha_FinEstimado"])) : null,
			"fechaResuelto"       => $fila["Fecha_Resuelto"] ? date("d/m/Y g:i a", strtotime($fila["Fecha_Resuelto"])) : null,
			"vecesReabierta"      => (int) $fila["Veces_Reabierta"],
			"foto"                => $fila["Foto"] ?: null,
		];
	}

	// HTML de una tarjeta del tablero. Vive aquí (no en la vista) para poder
	// reutilizarse también desde ajax/mantenimiento-cambiar-estatus.ajax.php:
	// cuando una tarjeta cambia de columna, el servidor regresa su HTML ya
	// armado y el JS solo la mueve al DOM correcto, sin recargar la página ni
	// duplicar esta plantilla en JS.
	static public function crtRenderizarTarjeta($item, $columna){
		$idMtto = (int) $item["Id_Mantenimiento"];
		$icono = htmlspecialchars($item["PiezaIcono"]);

		$html  = '<div class="mtto-tarjeta" idMantenimiento="' . $idMtto . '">';
		$html .= '<div class="mtto-cabecera">';
		$html .=   '<div class="mtto-col-izq">';
		$html .=     '<span class="mtto-hab-badge"><i class="fa fa-hotel"></i> ' . htmlspecialchars($item["NumeroHabitacion"]);
		if(!empty($item["TipoHabitacion"])){
			$html .= ' · ' . htmlspecialchars($item["TipoHabitacion"]);
		}
		$html .=     '</span>';

		if((int) ($item["Veces_Reabierta"] ?? 0) > 0){
			$html .= '<span class="mtto-reabierta-badge" title="Esta incidencia se marcó como resuelta y se volvió a reabrir">';
			$html .=   '<i class="fa fa-undo"></i> Se volvió a reabrir';
			if((int) $item["Veces_Reabierta"] > 1){
				$html .= ' (x' . (int) $item["Veces_Reabierta"] . ')';
			}
			$html .= '</span>';
		}

		$html .=     '<button type="button" class="mtto-btn-detalle btnDetalleMtto" idMantenimiento="' . $idMtto . '"><i class="fa fa-list-alt"></i> Detalle</button>';

		$html .=     '<div class="mtto-pieza">';
		$html .=       '<span class="mtto-pieza-icono"><i class="fa ' . $icono . '"></i></span>';
		$html .=       '<span class="mtto-pieza-nombre">' . htmlspecialchars($item["PiezaNombre"]) . '</span>';
		$html .=     '</div>';

		if(!empty($item["Descripcion"])){
			$html .= '<div class="mtto-descripcion">' . htmlspecialchars($item["Descripcion"]) . '</div>';
		}

		$html .=   '</div>'; // /mtto-col-izq

		$html .=   '<div class="mtto-col-der">';
		$html .=     '<span class="mtto-tipo">' . htmlspecialchars($item["TipoMantenimientoNombre"]) . '</span>';
		$html .=     '<div class="mtto-datos">';
		$html .=       '<span class="mtto-dato mtto-dato-fecha"><i class="fa fa-hourglass-half"></i> Vence: ' .
		                 (!empty($item["Fecha_FinEstimado"]) ? date("d/m/Y", strtotime($item["Fecha_FinEstimado"])) : "—") . '</span>';
		$html .=       '<span class="mtto-dato"><i class="fa fa-money"></i> $' . number_format((float) $item["CostoReparacion"], 2) . '</span>';
		$html .=     '</div>';
		$html .=   '</div>'; // /mtto-col-der

		$html .= '</div>'; // /mtto-cabecera

		$html .= '<div class="mtto-acciones">';

		if($columna !== "resuelto"){
			$html .= '<button type="button" class="mtto-btn-orden btnMoverOrden" idMantenimiento="' . $idMtto . '" direccion="arriba" title="Subir prioridad"><i class="fa fa-arrow-up"></i></button>';
			$html .= '<button type="button" class="mtto-btn-orden btnMoverOrden" idMantenimiento="' . $idMtto . '" direccion="abajo" title="Bajar prioridad"><i class="fa fa-arrow-down"></i></button>';
		}

		if($columna === "pendiente"){
			$html .= '<button type="button" class="mtto-btn-avanzar btnCambiarEstatus" idMantenimiento="' . $idMtto . '" idEstatus="' . self::ESTATUS_PROCESO . '">Iniciar <i class="fa fa-arrow-right"></i></button>';
		}elseif($columna === "proceso"){
			$html .= '<button type="button" class="mtto-btn-avanzar mtto-btn-resolver btnCambiarEstatus" idMantenimiento="' . $idMtto . '" idEstatus="' . self::ESTATUS_RESUELTO . '">Marcar resuelto <i class="fa fa-check"></i></button>';
		}else{
			$html .= '<button type="button" class="mtto-btn-reabrir btnCambiarEstatus" idMantenimiento="' . $idMtto . '" idEstatus="' . self::ESTATUS_PENDIENTE . '"><i class="fa fa-undo"></i> Reabrir</button>';
		}

		$html .= '<button type="button" class="mtto-btn-eliminar btnEliminarMtto" idMantenimiento="' . $idMtto . '" title="Eliminar"><i class="fa fa-trash"></i></button>';

		$html .= '</div>'; // /mtto-acciones
		$html .= '</div>'; // /mtto-tarjeta

		return $html;
	}

	// Fila + columna de una sola incidencia, ya renderizada, para que el ajax
	// de cambiar-estatus regrese la tarjeta lista y el JS la mueva sin recargar.
	static public function crtObtenerFilaTablero($id_mantenimiento){
		$id_hotel = self::crtObtenerIdHotelSesion();

		if($id_hotel === null){
			return null;
		}

		$fila = ModeloMantenimiento::MdlObtenerMantenimientoPorId($id_mantenimiento, $id_hotel);

		if(!$fila){
			return null;
		}

		$fila["PiezaIcono"] = self::crtObtenerIconoPieza($fila["PiezaNombre"]);

		$columnaPorEstatus = [
			self::ESTATUS_PENDIENTE => "pendiente",
			self::ESTATUS_PROCESO   => "proceso",
			self::ESTATUS_RESUELTO  => "resuelto",
		];

		$columna = $columnaPorEstatus[(int) $fila["Id_Estatus"]] ?? null;

		if($columna === null){
			return null;
		}

		return [
			"columna" => $columna,
			"html"    => self::crtRenderizarTarjeta($fila, $columna),
		];
	}

	// Tablero agrupado en las 3 columnas, ya con el ícono de pieza resuelto
	static public function crtObtenerTablero(){
		$id_hotel = self::crtObtenerIdHotelSesion();

		$tablero = [
			"pendiente" => [],
			"proceso"   => [],
			"resuelto"  => [],
		];

		if($id_hotel === null){
			return $tablero;
		}

		$filas = ModeloMantenimiento::MdlObtenerMantenimientos($id_hotel);

		$columnaPorEstatus = [
			self::ESTATUS_PENDIENTE => "pendiente",
			self::ESTATUS_PROCESO   => "proceso",
			self::ESTATUS_RESUELTO  => "resuelto",
		];

		foreach($filas as $fila){
			$fila["PiezaIcono"] = self::crtObtenerIconoPieza($fila["PiezaNombre"]);

			$columna = $columnaPorEstatus[(int) $fila["Id_Estatus"]] ?? null;

			if($columna !== null){
				$tablero[$columna][] = $fila;
			}
		}

		return $tablero;
	}

	static public function crtInsertarMantenimiento(){
		if(isset($_POST["nuevoMantenimiento"])){

			$_POST["nuevaDescripcionMtto"] = mb_substr(trim($_POST["nuevaDescripcionMtto"] ?? ""), 0, 255);

			$palabrasDescripcion = preg_split('/\s+/', $_POST["nuevaDescripcionMtto"], -1, PREG_SPLIT_NO_EMPTY);
			$descripcionValida = count($palabrasDescripcion) >= 10;

			$fechaInicio = $_POST["nuevaFechaInicioMtto"] ?? "";
			$fechaFin = $_POST["nuevaFechaFinMtto"] ?? "";
			$hoy = strtotime(date("Y-m-d"));
			$fechasValidas = preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaInicio) &&
			                 preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaFin) &&
			                 strtotime($fechaInicio) >= $hoy &&
			                 strtotime($fechaFin) >= strtotime($fechaInicio);

			// La foto es obligatoria: debe venir un archivo y pesar máximo 3MB.
			$fotoTmp = $_FILES["nuevaFotoMtto"]["tmp_name"] ?? "";
			$fotoValida = $fotoTmp !== "" && $_FILES["nuevaFotoMtto"]["size"] <= 3 * 1024 * 1024;

			if(preg_match('/^[0-9]+$/', $_POST["nuevaHabitacionMtto"] ?? "") &&
			   preg_match('/^[0-9]+$/', $_POST["nuevoTipoMtto"] ?? "") &&
			   preg_match('/^[0-9]+$/', $_POST["nuevaPiezaMtto"] ?? "") &&
			   $descripcionValida &&
			   $fechasValidas &&
			   $fotoValida &&
			   preg_match('/^[0-9]+(\.[0-9]{1,2})?$/', $_POST["nuevoCostoMtto"] ?? "")) {

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

				$fotoDestino = null;

				if($fotoTmp !== ""){
					$dirPath = "views/img/Mantenimiento/";
					if(!is_dir($dirPath)){
						mkdir($dirPath, 0755, true);
					}
					$nombreImagen = time() . "_" . $_FILES["nuevaFotoMtto"]["name"];
					$fotoDestino = $dirPath . $nombreImagen;

					if(!move_uploaded_file($fotoTmp, $fotoDestino)){
						$fotoDestino = null;
					}
				}

				ModeloMantenimiento::MdlInsertarMantenimiento(
					$_POST["nuevaHabitacionMtto"],
					$_POST["nuevoTipoMtto"],
					$_POST["nuevaPiezaMtto"],
					$_POST["nuevaDescripcionMtto"],
					$fotoDestino,
					$fechaInicio,
					$fechaFin,
					$_POST["nuevoCostoMtto"],
					$_SESSION["IdUsuario"]
				);

				echo '<script>
					Swal.fire({
						icon: "success",
						title : "Sistema PosDit",
						text: "¡La incidencia de mantenimiento se registró correctamente!",
						showConfirmButton: true,
						confirmButtonText: "Cerrar"
					}).then(function(){
						window.location = "mantenimiento";
					});
				</script>';

			}else if(!$descripcionValida){
				echo '<script>
					Swal.fire({
						icon: "error",
						title : "Sistema PosDit",
						text: "¡La descripción es obligatoria y debe tener al menos 10 palabras!",
						showConfirmButton: true,
						confirmButtonText: "Cerrar"
					});
				</script>';
			}else if(!$fotoValida){
				echo '<script>
					Swal.fire({
						icon: "error",
						title : "Sistema PosDit",
						text: "¡La foto es obligatoria y no puede pesar más de 3MB!",
						showConfirmButton: true,
						confirmButtonText: "Cerrar"
					});
				</script>';
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
