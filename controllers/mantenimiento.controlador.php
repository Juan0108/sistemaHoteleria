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

	// Estatus del tablero (deben coincidir con cat_estatus 15/16/17/18)
	const ESTATUS_PENDIENTE = 15;
	const ESTATUS_PROCESO   = 16;
	const ESTATUS_RESUELTO  = 17;
	const ESTATUS_ELIMINADO = 18;

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

	static public function crtObtenerMotivos(){
		return ModeloMantenimiento::MdlObtenerMotivosMantenimiento();
	}

	// Guarda el motivo de "por qué se volvió a reabrir" (llamado desde
	// ajax/mantenimiento-cambiar-estatus.ajax.php cuando la acción es Reabrir)
	static public function crtActualizarNotaReapertura($id_mantenimiento, $nota){
		$id_hotel = self::crtObtenerIdHotelSesion();

		if($id_hotel === null){
			return false;
		}

		$nota = mb_substr(trim($nota), 0, 255);

		if($nota === ""){
			return false;
		}

		$respuesta = ModeloMantenimiento::MdlActualizarNotaReapertura($id_mantenimiento, $id_hotel, $nota);

		return $respuesta && (int) $respuesta["Afectados"] > 0;
	}

	// Guarda la foto de cómo quedó la incidencia ya reparada (llamado desde
	// ajax/mantenimiento-cambiar-estatus.ajax.php cuando la acción es Marcar resuelto)
	static public function crtGuardarFotoResuelta($id_mantenimiento, $archivoFoto){
		$id_hotel = self::crtObtenerIdHotelSesion();

		if($id_hotel === null){
			return ["status" => "error", "message" => "No se encontró el hotel de tu negocio"];
		}

		if(!is_array($archivoFoto) || empty($archivoFoto["tmp_name"])){
			return ["status" => "error", "message" => "Debes adjuntar la foto de cómo quedó resuelta la incidencia"];
		}

		if($archivoFoto["size"] > 3 * 1024 * 1024){
			return ["status" => "error", "message" => "La foto no puede pesar más de 3MB"];
		}

		// Ruta absoluta: este método se llama desde
		// ajax/mantenimiento-cambiar-estatus.ajax.php, cuyo cwd es /ajax/, así
		// que una ruta relativa como "views/img/Mantenimiento/" terminaba
		// guardando el archivo en /ajax/views/img/Mantenimiento/.
		$dirPathAbsoluto = dirname(__DIR__) . "/views/img/Mantenimiento/";
		if(!is_dir($dirPathAbsoluto)){
			mkdir($dirPathAbsoluto, 0755, true);
		}
		$nombreImagen = time() . "_" . $archivoFoto["name"];

		if(!move_uploaded_file($archivoFoto["tmp_name"], $dirPathAbsoluto . $nombreImagen)){
			return ["status" => "error", "message" => "No se pudo guardar la foto"];
		}

		$fotoDestino = "views/img/Mantenimiento/" . $nombreImagen;

		$respuesta = ModeloMantenimiento::MdlActualizarFotoResuelta($id_mantenimiento, $id_hotel, $fotoDestino);

		if(!$respuesta || (int) $respuesta["Afectados"] <= 0){
			return ["status" => "error", "message" => "No se pudo guardar la foto"];
		}

		return ["status" => "success"];
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

		if((float) ($item["SaldoRestante"] ?? 0) <= 0.009){
			$html .= '<span class="mtto-pagada-badge" title="Esta incidencia ya está completamente liquidada">';
			$html .=   '<i class="fa fa-check-circle"></i> Pagada';
			$html .= '</span>';
		}elseif($columna === "resuelto"){
			$html .= '<span class="mtto-liquidar-badge" title="Esta incidencia se marcó como resuelta pero todavía tiene saldo por cobrar">';
			$html .=   '<i class="fa fa-exclamation-circle"></i> Pendiente de liquidar';
			$html .= '</span>';
		}

		if((int) ($item["Veces_Reabierta"] ?? 0) > 0){
			$html .= '<span class="mtto-reabierta-badge" title="Esta incidencia se marcó como resuelta y se volvió a reabrir">';
			$html .=   '<i class="fa fa-undo"></i> Se volvió a reabrir';
			if((int) $item["Veces_Reabierta"] > 1){
				$html .= ' (' . (int) $item["Veces_Reabierta"] . ')';
			}
			$html .= '</span>';
		}

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
		$html .=     '<div class="mtto-bitacoras">';
		$estatusColumna = ["pendiente" => "Pendiente", "proceso" => "En Proceso", "resuelto" => "Resuelto"][$columna] ?? "Otro";
		$html .=       '<button type="button" class="mtto-btn-bitacora btnBitacoraIncidencias" idMantenimiento="' . $idMtto . '" data-estatus="' . htmlspecialchars($estatusColumna) . '" title="Bitácora de incidencias"><i class="fa fa-book"></i></button>';
		$html .=       '<button type="button" class="mtto-btn-bitacora mtto-btn-bitacora-abonos btnBitacoraAbonos" idMantenimiento="' . $idMtto . '" data-estatus="' . htmlspecialchars($estatusColumna) . '" title="Bitácora de abonos"><i class="fa fa-book"></i></button>';
		$html .=     '</div>';
		$html .=   '</div>'; // /mtto-col-der

		$html .= '</div>'; // /mtto-cabecera

		$html .= '<div class="mtto-acciones">';

		if($columna !== "resuelto"){
			$html .= '<button type="button" class="mtto-btn-orden btnMoverOrden" idMantenimiento="' . $idMtto . '" direccion="arriba" title="Subir prioridad"><i class="fa fa-arrow-up"></i></button>';
			$html .= '<button type="button" class="mtto-btn-orden btnMoverOrden" idMantenimiento="' . $idMtto . '" direccion="abajo" title="Bajar prioridad"><i class="fa fa-arrow-down"></i></button>';
		}

		$html .= '<button type="button" class="mtto-btn-abonos btnAbonosMtto" idMantenimiento="' . $idMtto . '" title="Abonos"><i class="fa fa-money"></i> Abonos</button>';

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

			// Se calcula en cualquier columna para poder pintar el badge "Pagada"
			// apenas se liquide una incidencia, sin importar si ya está Resuelta;
			// también arma el aviso de "Pendiente de liquidar" (solo aplica a Resueltas).
			$resumen = ModeloMantenimiento::MdlObtenerResumenAbonos((int) $fila["Id_Mantenimiento"], $id_hotel);
			$fila["SaldoRestante"] = $resumen ? (float) $resumen["SaldoRestante"] : (float) $fila["CostoReparacion"];

			if($columna !== null){
				$tablero[$columna][] = $fila;
			}
		}

		return $tablero;
	}

	// Incidencias Resueltas con saldo pendiente por cobrar, para el aviso
	// "Pendiente de liquidar" que se muestra al cargar el tablero.
	static public function crtObtenerPendientesLiquidar($tablero){
		$pendientes = [];

		foreach($tablero["resuelto"] as $item){
			if((float) ($item["SaldoRestante"] ?? 0) > 0.009){
				$pendientes[] = [
					"habitacion"    => $item["TipoHabitacion"] ?: $item["NumeroHabitacion"],
					"saldoRestante" => (float) $item["SaldoRestante"],
				];
			}
		}

		return $pendientes;
	}

	// Resumen + lista de abonos para el modal "Abonos" de la tarjeta.
	static public function crtObtenerAbonos($id_mantenimiento){
		$id_hotel = self::crtObtenerIdHotelSesion();

		if($id_hotel === null){
			return null;
		}

		$resumen = ModeloMantenimiento::MdlObtenerResumenAbonos($id_mantenimiento, $id_hotel);

		if(!$resumen){
			return null;
		}

		$lista = ModeloMantenimiento::MdlObtenerListaAbonos($id_mantenimiento, $id_hotel);

		$abonos = [];
		foreach($lista as $fila){
			$nombreUsuario = trim(($fila["Nombre"] ?? "") . " " . ($fila["Apaterno"] ?? "") . " " . ($fila["Amaterno"] ?? ""));
			$abonos[] = [
				"idAbono"  => (int) $fila["Id_Abono"],
				"monto"    => (float) $fila["Monto"],
				"fecha"    => date("d/m/Y g:i a", strtotime($fila["Fecha_Abono"])),
				"foto"     => $fila["Foto"] ?: null,
				"usuario"  => $nombreUsuario !== "" ? $nombreUsuario : "Sin asignar",
			];
		}

		return [
			"saldoInicial"  => (float) $resumen["SaldoInicial"],
			"saldoRestante" => (float) $resumen["SaldoRestante"],
			"numAbonos"     => (int) $resumen["NumAbonos"],
			"abonos"        => $abonos,
		];
	}

	// Nombre a mostrar del estatus de una incidencia dentro de la bitácora
	const NOMBRES_ESTATUS = [
		self::ESTATUS_PENDIENTE => "Pendiente",
		self::ESTATUS_PROCESO   => "En Proceso",
		self::ESTATUS_RESUELTO  => "Resuelto",
		self::ESTATUS_ELIMINADO => "Eliminado",
	];

	// Bitácora de incidencias de la habitación: foto + fecha de cada
	// incidencia que ha tenido, sin importar desde qué tarjeta se abrió.
	static public function crtObtenerBitacoraIncidencias($id_mantenimiento){
		$id_hotel = self::crtObtenerIdHotelSesion();

		if($id_hotel === null){
			return null;
		}

		$filas = ModeloMantenimiento::MdlObtenerBitacoraIncidencias($id_mantenimiento, $id_hotel);

		$bitacora = [];
		foreach($filas as $fila){
			$bitacora[] = [
				"idMantenimiento" => (int) $fila["Id_Mantenimiento"],
				"fecha"           => date("d/m/Y g:i a", strtotime($fila["Fecha_Registro"])),
				"foto"            => $fila["Foto"] ?: null,
				"fotoResuelto"    => $fila["Foto_Resuelto"] ?: null,
				"descripcion"     => $fila["Descripcion"],
				"proveedor"       => $fila["Proveedor"] ?: null,
				"estatus"         => self::NOMBRES_ESTATUS[(int) $fila["Id_Estatus"]] ?? "Otro",
			];
		}

		return $bitacora;
	}

	// Bitácora de abonos de la habitación: monto + fecha de cada abono hecho
	// a cualquiera de sus incidencias.
	static public function crtObtenerBitacoraAbonos($id_mantenimiento){
		$id_hotel = self::crtObtenerIdHotelSesion();

		if($id_hotel === null){
			return null;
		}

		$filas = ModeloMantenimiento::MdlObtenerBitacoraAbonos($id_mantenimiento, $id_hotel);

		$bitacora = [];
		foreach($filas as $fila){
			$nombreUsuario = trim(($fila["Nombre"] ?? "") . " " . ($fila["Apaterno"] ?? "") . " " . ($fila["Amaterno"] ?? ""));
			$bitacora[] = [
				"idMantenimiento" => (int) $fila["Id_Mantenimiento"],
				"fecha"           => date("d/m/Y g:i a", strtotime($fila["Fecha_Abono"])),
				"monto"           => (float) $fila["Monto"],
				"foto"            => $fila["Foto"] ?: null,
				"descripcion"     => $fila["Descripcion"],
				"estatus"         => self::NOMBRES_ESTATUS[(int) $fila["Id_Estatus"]] ?? "Otro",
				"usuario"         => $nombreUsuario !== "" ? $nombreUsuario : "Sin asignar",
			];
		}

		return $bitacora;
	}

	// Historial de TRANSICIONES de una habitación, para la pestaña "Bitácora": un renglón
	// por cada cambio de estatus (creada, pasó a proceso, se resolvió, se reabrió con su
	// nota, etc.) de CUALQUIERA de sus incidencias, sin sobreescribir nada.
	static public function crtObtenerHistorialTransicionesHabitacion($id_habitacion){
		$id_habitacion = (int) $id_habitacion;

		if($id_habitacion <= 0){
			return null;
		}

		$id_hotel = self::crtObtenerIdHotelSesion();

		if($id_hotel === null){
			return null;
		}

		$filas = ModeloMantenimiento::MdlObtenerHistorialTransicionesHabitacion($id_habitacion, $id_hotel);

		$historial = [];
		foreach($filas as $fila){
			$historial[] = [
				"idMantenimiento" => (int) $fila["Id_Mantenimiento"],
				"fecha"           => date("d/m/Y g:i a", strtotime($fila["Fecha"])),
				"fechaIso"        => $fila["Fecha"],
				"estatus"         => self::NOMBRES_ESTATUS[(int) $fila["Id_Estatus"]] ?? "Otro",
				"descripcion"     => $fila["Descripcion"],
				"pieza"           => $fila["Pieza"],
				"nota"            => $fila["Nota"] ?: null,
				"foto"            => $fila["Foto"] ?: null,
			];
		}

		return $historial;
	}

	// Arma un renglón del historial de incidencias a partir de la fila cruda del SP. Compartido
	// entre la vista por habitación y la de todo el hotel (esta última trae además el nombre
	// de la habitación, ya que mezcla varias).
	static private function crtArmarFilaHistorialIncidencia($fila, $mapaMotivos){
		$idEstatus = (int) $fila["Id_Estatus"];

		$item = [
			"idMantenimiento" => (int) $fila["Id_Mantenimiento"],
			"descripcion"     => $fila["Descripcion"],
			"proveedor"       => $fila["Proveedor"] ?: null,
			"foto"            => $fila["Foto"] ?: null,
			"fotoResuelto"    => $fila["Foto_Resuelto"] ?: null,
			"fechaRegistro"   => date("d/m/Y g:i a", strtotime($fila["Fecha_Registro"])),
			"fechaRegistroIso" => $fila["Fecha_Registro"],
			"fechaResuelto"   => $fila["Fecha_Resuelto"] ? date("d/m/Y g:i a", strtotime($fila["Fecha_Resuelto"])) : null,
			"fechaEliminado"  => $fila["Fecha_Eliminado"] ? date("d/m/Y g:i a", strtotime($fila["Fecha_Eliminado"])) : null,
			"estatus"         => self::NOMBRES_ESTATUS[$idEstatus] ?? "Otro",
			"vecesReabierta"  => (int) $fila["Veces_Reabierta"],
			"motivoEliminado" => $idEstatus === self::ESTATUS_ELIMINADO
				? ($mapaMotivos[(int) $fila["Id_MotivoEliminacion"]] ?? "Sin especificar")
				: null,
		];

		if(array_key_exists("TipoHabitacion", $fila) || array_key_exists("NumeroHabitacion", $fila)){
			$item["habitacion"] = $fila["TipoHabitacion"] ?: $fila["NumeroHabitacion"];
		}

		return $item;
	}

	// Info completa capturada al registrar una incidencia (pieza, proveedor, descripción,
	// fechas estimadas, foto, costo, quién la registró), para el pop up de detalle que se
	// abre desde la pestaña Bitácora.
	static public function crtObtenerInfoRegistroIncidencia($id_mantenimiento){
		$id_mantenimiento = (int) $id_mantenimiento;

		if($id_mantenimiento <= 0){
			return null;
		}

		$id_hotel = self::crtObtenerIdHotelSesion();

		if($id_hotel === null){
			return null;
		}

		$fila = ModeloMantenimiento::MdlObtenerInfoRegistroIncidencia($id_mantenimiento, $id_hotel);

		if(!$fila){
			return null;
		}

		return [
			"habitacion"          => $fila["TipoHabitacion"] ?: $fila["NumeroHabitacion"],
			"tipoMantenimiento"   => $fila["TipoMantenimientoNombre"],
			"pieza"               => $fila["Pieza"],
			"proveedor"           => $fila["Proveedor"] ?: null,
			"descripcion"         => $fila["Descripcion"],
			"foto"                => $fila["Foto"] ?: null,
			"fechaRegistro"       => date("d/m/Y g:i a", strtotime($fila["Fecha_Registro"])),
			"fechaInicioEstimado" => $fila["Fecha_InicioEstimado"] ? date("d/m/Y", strtotime($fila["Fecha_InicioEstimado"])) : null,
			"fechaFinEstimado"    => $fila["Fecha_FinEstimado"] ? date("d/m/Y", strtotime($fila["Fecha_FinEstimado"])) : null,
			"costo"               => (float) $fila["CostoReparacion"],
			"usuario"             => trim($fila["NombreUsuario"]) !== "" ? $fila["NombreUsuario"] : "Sin asignar",
			"estatus"             => self::NOMBRES_ESTATUS[(int) $fila["Id_Estatus"]] ?? "Otro",
			"fechaResuelto"       => $fila["Fecha_Resuelto"] ? date("d/m/Y g:i a", strtotime($fila["Fecha_Resuelto"])) : null,
			"vecesReabierta"      => (int) $fila["Veces_Reabierta"],
			"notaReapertura"      => $fila["NotaReapertura"] ?: null,
		];
	}

	// Historial de INCIDENCIAS (tickets) de una habitación, para la pestaña "Historial": un
	// renglón por cada incidencia que ha tenido esa habitación, sin importar su estatus
	// (pendiente, en proceso, resuelta o eliminada).
	static public function crtObtenerHistorialIncidenciasHabitacion($id_habitacion){
		$id_habitacion = (int) $id_habitacion;

		if($id_habitacion <= 0){
			return null;
		}

		$id_hotel = self::crtObtenerIdHotelSesion();

		if($id_hotel === null){
			return null;
		}

		$filas = ModeloMantenimiento::MdlObtenerHistorialIncidenciasHabitacion($id_habitacion, $id_hotel);

		$mapaMotivos = [];
		foreach(self::crtObtenerMotivos() as $motivo){
			$mapaMotivos[(int) $motivo["Id_MotivoMantenimiento"]] = $motivo["Nombre"];
		}

		$historial = [];
		foreach($filas as $fila){
			$historial[] = self::crtArmarFilaHistorialIncidencia($fila, $mapaMotivos);
		}

		return $historial;
	}

	// Igual que la anterior, pero de TODO el hotel (todas las habitaciones juntas). Se usa en
	// la pestaña "Historial" mientras no se haya elegido una habitación en el filtro.
	static public function crtObtenerHistorialIncidenciasHotel(){
		$id_hotel = self::crtObtenerIdHotelSesion();

		if($id_hotel === null){
			return null;
		}

		$filas = ModeloMantenimiento::MdlObtenerHistorialIncidenciasHotel($id_hotel);

		$mapaMotivos = [];
		foreach(self::crtObtenerMotivos() as $motivo){
			$mapaMotivos[(int) $motivo["Id_MotivoMantenimiento"]] = $motivo["Nombre"];
		}

		$historial = [];
		foreach($filas as $fila){
			$historial[] = self::crtArmarFilaHistorialIncidencia($fila, $mapaMotivos);
		}

		return $historial;
	}

	// Registrar un abono (llamado desde ajax/mantenimiento-abono-insertar.ajax.php)
	static public function crtInsertarAbono($id_mantenimiento, $monto, $archivoFoto){
		$id_hotel = self::crtObtenerIdHotelSesion();

		if($id_hotel === null){
			return ["status" => "error", "message" => "No se encontró el hotel de tu negocio"];
		}

		if($id_mantenimiento <= 0 || !preg_match('/^[0-9]+(\.[0-9]{1,2})?$/', (string) $monto) || (float) $monto <= 0){
			return ["status" => "error", "message" => "Captura un monto válido"];
		}

		$resumenPrevio = ModeloMantenimiento::MdlObtenerResumenAbonos($id_mantenimiento, $id_hotel);

		if(!$resumenPrevio){
			return ["status" => "error", "message" => "No se encontró la incidencia"];
		}

		$saldoPrevio = (float) $resumenPrevio["SaldoRestante"];

		if((float) $monto > $saldoPrevio + 0.009){
			return ["status" => "error", "message" => "El abono no puede ser mayor al saldo restante (" . number_format($saldoPrevio, 2) . ")"];
		}

		if(!is_array($archivoFoto) || empty($archivoFoto["tmp_name"])){
			return ["status" => "error", "message" => "Debes adjuntar la foto del ticket"];
		}

		if($archivoFoto["size"] > 3 * 1024 * 1024){
			return ["status" => "error", "message" => "La foto del ticket no puede pesar más de 3MB"];
		}

		$fotoDestino = null;

		// Ruta absoluta para guardar en disco (este método se llama desde
		// ajax/mantenimiento-abono-insertar.ajax.php, cuyo cwd es /ajax/, así
		// que una ruta relativa como "views/img/Abonos/" terminaba guardando
		// el archivo en /ajax/views/img/Abonos/ en vez de /views/img/Abonos/).
		$dirPathAbsoluto = dirname(__DIR__) . "/views/img/Abonos/";
		if(!is_dir($dirPathAbsoluto)){
			mkdir($dirPathAbsoluto, 0755, true);
		}
		$nombreImagen = time() . "_" . $archivoFoto["name"];

		if(move_uploaded_file($archivoFoto["tmp_name"], $dirPathAbsoluto . $nombreImagen)){
			// Ruta web (relativa a la raíz del sitio) para guardar en la BD y usarse en <img src="...">
			$fotoDestino = "views/img/Abonos/" . $nombreImagen;
		}

		$respuesta = ModeloMantenimiento::MdlInsertarAbono($id_mantenimiento, $id_hotel, $monto, $fotoDestino, $_SESSION["IdUsuario"]);

		if($respuesta && (int) $respuesta["Afectados"] > 0){
			$resumen = ModeloMantenimiento::MdlObtenerResumenAbonos($id_mantenimiento, $id_hotel);
			$saldoRestante = $resumen ? (float) $resumen["SaldoRestante"] : null;

			return [
				"status"        => "success",
				"message"       => "Abono registrado correctamente",
				"saldoRestante" => $saldoRestante,
				"pagada"        => $saldoRestante !== null && $saldoRestante <= 0.009,
			];
		}

		return ["status" => "error", "message" => "No se pudo registrar el abono"];
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

			$_POST["nuevaPiezaMtto"] = mb_substr(trim($_POST["nuevaPiezaMtto"] ?? ""), 0, 150);
			$_POST["nuevoProveedorMtto"] = mb_substr(trim($_POST["nuevoProveedorMtto"] ?? ""), 0, 150);

			if(preg_match('/^[0-9]+$/', $_POST["nuevaHabitacionMtto"] ?? "") &&
			   preg_match('/^[0-9]+$/', $_POST["nuevoTipoMtto"] ?? "") &&
			   $_POST["nuevaPiezaMtto"] !== "" &&
			   $_POST["nuevoProveedorMtto"] !== "" &&
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
					$_POST["nuevoProveedorMtto"] !== "" ? $_POST["nuevoProveedorMtto"] : null,
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
