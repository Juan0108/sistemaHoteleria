<?php

require_once "conexion.php";

/**
 * 
 */
class ModeloHoteles{


/**
Obtener Colonias JSON
 */
static public function MdlInsertarHotel($hotel){

    if (is_object($hotel)) {
        $Razon_Social = $hotel->Razon_social ?? $hotel->Razon_Social ?? '';
        $Responsable = $hotel->Responsable ?? $hotel->responsable ?? '';
        $Telefono = $hotel->Telefono ?? $hotel->telefono ?? '';
        $Correo = $hotel->Correo ?? $hotel->correo ?? '';
        $Id_Giro = $hotel->Id_giro ?? $hotel->Id_Giro ?? null;
        $Calle = $hotel->Calle ?? $hotel->calle ?? '';
        $Id_Estado = $hotel->Id_estado ?? $hotel->Id_Estado ?? null;
        $Id_Municipio = $hotel->Id_municipio ?? $hotel->Id_Municipio ?? null;
        $Id_Colonia = $hotel->id_colonia ?? $hotel->Id_Colonia ?? null;
        $Id_TipoPago = $hotel->id_tipopago ?? $hotel->Id_TipoPago ?? null;
        $Id_Estatus = $hotel->id_estatus ?? $hotel->Id_Estatus ?? null;
    } else {
        $Razon_Social = $hotel['Razon_Social'] ?? $hotel['Razon_social'] ?? '';
        $Responsable = $hotel['Responsable'] ?? $hotel['responsable'] ?? '';
        $Telefono = $hotel['Telefono'] ?? $hotel['telefono'] ?? '';
        $Correo = $hotel['Correo'] ?? $hotel['correo'] ?? '';
        $Id_Giro = $hotel['Id_Giro'] ?? $hotel['Id_giro'] ?? null;
        $Calle = $hotel['Calle'] ?? $hotel['calle'] ?? '';
        $Id_Estado = $hotel['Id_Estado'] ?? $hotel['Id_estado'] ?? null;
        $Id_Municipio = $hotel['Id_Municipio'] ?? $hotel['Id_municipio'] ?? null;
        $Id_Colonia = $hotel['Id_Colonia'] ?? $hotel['Id_colonia'] ?? null;
        $Id_TipoPago = $hotel['Id_TipoPago'] ?? $hotel['Id_tipopago'] ?? null;
        $Id_Estatus = $hotel['Id_Estatus'] ?? $hotel['Id_estatus'] ?? null;
    }

    if (trim($Razon_Social) === '') {
        return false;
    }

    $stmt = Conexion::conectar()->prepare(
        "CALL InsertarHotel(?,?,?,?,?,?,?,?,?,?,?)"
    );

    $stmt->bindParam(1, $Razon_Social, PDO::PARAM_STR);
    $stmt->bindParam(2, $Responsable, PDO::PARAM_STR);
    $stmt->bindParam(3, $Telefono, PDO::PARAM_STR);
    $stmt->bindParam(4, $Correo, PDO::PARAM_STR);
    $stmt->bindParam(5, $Id_Giro, PDO::PARAM_INT);
    $stmt->bindParam(6, $Calle, PDO::PARAM_STR);
    $stmt->bindParam(7, $Id_Estado, PDO::PARAM_INT);
    $stmt->bindParam(8, $Id_Municipio, PDO::PARAM_INT);
    $stmt->bindParam(9, $Id_Colonia, PDO::PARAM_INT);
    $stmt->bindParam(10, $Id_TipoPago, PDO::PARAM_INT);
    $stmt->bindParam(11, $Id_Estatus, PDO::PARAM_INT);

    try {
        $executed = $stmt->execute();
    } catch (PDOException $e) {
        error_log("InsertarHotel PDO Exception: " . $e->getMessage());
        return [
            "error" => true,
            "type" => "pdo_exception",
            "message" => $e->getMessage(),
        ];
    }

    if (!$executed) {
        $err = $stmt->errorInfo();
        error_log("InsertarHotel PDO Error: " . json_encode($err));
        return [
            "error" => true,
            "type" => "pdo_error",
            "message" => $err[2] ?? "Unknown database error",
            "info" => $err,
        ];
    }

    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt->closeCursor();

    if ($resultado === false) {
        error_log("InsertarHotel: no se obtuvo resultado del stored procedure");
        return [
            "error" => true,
            "type" => "no_result",
            "message" => "No se obtuvo resultado del stored procedure.",
        ];
    }

    return $resultado;
}

/**
Actualizar Hoteles
 */
static public function MdlActualizarHotel($hotel){

	$stmt = Conexion::conectar()->prepare(
		"CALL ActualizarNegocio('$hotel->id_hotel',
		'$hotel->Razon_social',
		'$hotel->Responsable',
		'$hotel->Telefono',
		'$hotel->Correo',
		'$hotel->Id_giro',
		'$hotel->Calle',
		'$hotel->Id_estado',
		'$hotel->Id_municipio',
		'$hotel->id_colonia',
		'$hotel->id_tipopago',
		'$hotel->id_estatus',
		'$hotel->SAire',
		'$hotel->SServicios')");

	return $stmt -> execute();

}

/**
Actualizar Logo del Hotel
 */
static public function MdlActualizarLogoHotel($idHotel, $logo){

	$stmt = Conexion::conectar()->prepare("CALL ActualizarLogoHotel(?, ?)");
	$stmt->bindParam(1, $idHotel, PDO::PARAM_INT);
	$stmt->bindParam(2, $logo, PDO::PARAM_STR);

	return $stmt -> execute();

}

/**
Obtener Hotel
 */
static public function MdlObtenerHotel($valor){

    $stmt = Conexion::conectar()->prepare("CALL ObtenerHotel(?)");
    $stmt->bindParam(1, $valor, PDO::PARAM_INT);
    $stmt->execute();
    $respuesta = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt->closeCursor(); // Liberamos el búfer para Stored Procedures
    return $respuesta;

}


/**
Resolver el Id_Hotel a partir del Id_Negocio de sesión
 */
static public function MdlObtenerHotelPorNegocio($idNegocio){

    $stmt = Conexion::conectar()->prepare("CALL ObtenerHotelPorNegocio(?)");
    $stmt->bindParam(1, $idNegocio, PDO::PARAM_INT);
    $stmt->execute();
    $respuesta = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt->closeCursor(); // Liberamos el búfer para Stored Procedures
    return $respuesta;

}

/**
Obtener las habitaciones con una reservación en estatus "Ocupado"
 */
static public function MdlObtenerHabitacionesOcupadas($idHotel){

    $stmt = Conexion::conectar()->prepare("CALL ObtenerHabitacionesOcupadas(?)");
    $stmt->bindParam(1, $idHotel, PDO::PARAM_INT);
    $stmt->execute();
    $respuesta = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor(); // Liberamos el búfer para Stored Procedures
    return $respuesta;

}

/**
Obtener la siguiente reservación "Reservado" de una habitación a partir de una fecha de salida dada
 */
static public function MdlObtenerSiguienteReservacion($idHabitacion, $fechaSalidaActual){

    $stmt = Conexion::conectar()->prepare("CALL ObtenerSiguienteReservacion(?,?)");
    $stmt->bindParam(1, $idHabitacion, PDO::PARAM_INT);
    $stmt->bindParam(2, $fechaSalidaActual, PDO::PARAM_STR);
    $stmt->execute();
    $respuesta = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt->closeCursor(); // Liberamos el búfer para Stored Procedures
    return $respuesta;

}

/**
Obtener la reservación anterior (Ocupado/Reservado) de una habitación antes de una fecha de entrada dada,
considerando la salida efectiva (FechaSalida + HorasExtras ya compradas) de esa reservación anterior
 */
static public function MdlObtenerReservacionAnterior($idHabitacion, $fechaEntradaActual, $idReservacion){

    $stmt = Conexion::conectar()->prepare("CALL ObtenerReservacionAnterior(?,?,?)");
    $stmt->bindParam(1, $idHabitacion, PDO::PARAM_INT);
    $stmt->bindParam(2, $fechaEntradaActual, PDO::PARAM_STR);
    $stmt->bindParam(3, $idReservacion, PDO::PARAM_STR);
    $stmt->execute();
    $respuesta = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt->closeCursor(); // Liberamos el búfer para Stored Procedures
    return $respuesta;

}

/**
Obtener Hoteles
 */
static public function MdlObtenerHoteles(){

    $stmt = Conexion::conectar()->prepare("CALL ObtenerHoteles()");
    $stmt->execute();
    $respuesta = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor(); // Liberamos el búfer para Stored Procedures
    return $respuesta;

}

/**
Dar de baja un Hotel (borrado lógico: Id_Estatus = 4 "Baja")
 */
static public function MdlEliminarHotel($idHotel){

    $stmt = Conexion::conectar()->prepare(
        "UPDATE cat_negocios A
         INNER JOIN cat_hoteles H ON H.Id_Negocio = A.Id_Negocio
         SET A.Id_Estatus = 4, A.Fecha_Baja = CURDATE()
         WHERE H.Id_Hotel = :idHotel"
    );
    $stmt->bindParam(":idHotel", $idHotel, PDO::PARAM_INT);

    try {
        $stmt->execute();
    } catch (PDOException $e) {
        error_log("EliminarHotel PDO Exception: " . $e->getMessage());
        return "error";
    }

    return $stmt->rowCount() > 0 ? "ok" : "error";

}

/**
Obtener TipoPago JSON
 */
static public function MdlJsonObtenerTipoPago(){

	$stmt = Conexion::conectar()->prepare("CALL ObtenerTipoPago()");
	$stmt -> execute();

	$TipoPago = array();
 
 	while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
  
  		$TipoPago['data'][] = $row;
 	}
	 $stmt->closeCursor();
	 echo json_encode($TipoPago);

}

/**
Obtener Giro JSON
 */
static public function MdlJsonObtenerGiro(){

	$stmt = Conexion::conectar()->prepare("CALL ObtenerGiroHotel()");
	$stmt -> execute();

	$Giro = array();
 
 	while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
  
  		$Giro['data'][] = $row;
 	}
	 $stmt->closeCursor();
	 echo json_encode($Giro);

}

/**
Obtener Giro JSON
 */
static public function MdlJsonObtenerHoteles(){

	$stmt = Conexion::conectar()->prepare("CALL ObtenerComboHoteles()");
	$stmt -> execute();

	$Hoteles = array();

 	while($row=$stmt->fetch(PDO::FETCH_ASSOC)){

  		$Hoteles['data'][] = $row;
 	}
	 $stmt->closeCursor();
	 echo json_encode($Hoteles);

}

/**
Obtener unicamente el negocio del usuario en sesion (Razon Social propia),
para que el combo de negocio en Usuarios no permita ver ni elegir otros negocios/hoteles
 */
static public function MdlJsonObtenerNegocioSesion($idNegocio){

	$stmt = Conexion::conectar()->prepare("CALL ObtenerNegocioSesion(:idNegocio)");
	$stmt -> bindParam(":idNegocio", $idNegocio, PDO::PARAM_INT);
	$stmt -> execute();

	$Negocio = array();

	while($row=$stmt->fetch(PDO::FETCH_ASSOC)){

		$Negocio['data'][] = $row;
	}
	 $stmt->closeCursor();
	 echo json_encode($Negocio);

}

/**
Obtener Giro JSON
 */
static public function MdlJsonObtenerEstatus(){

	$stmt = Conexion::conectar()->prepare("CALL ObtenerEstatus()");
	$stmt -> execute();

	$Estatus = array();

 	while($row=$stmt->fetch(PDO::FETCH_ASSOC)){

  		// Id_Estatus 14 es una fila de configuración (tolerancia de Horas Anticipadas
  		// en Recepción), no un estatus real de hotel/habitación/reservación: se excluye
  		// para que no aparezca como opción en los combos de estatus de otras pantallas.
  		if((int) $row['Id_estatus'] === 14){
  			continue;
  		}

  		$Estatus['data'][] = $row;
 	}
	 $stmt->closeCursor();
	 echo json_encode($Estatus);

}

/**
Obtener Estados JSON
 */
static public function MdlJsonObtenerEstados(){

	$stmt = Conexion::conectar()->prepare("CALL ObtenerEstados()");
	$stmt -> execute();

	$Estados = array();
 
 	while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
  
  		$Estados['data'][] = $row;
 	}
	 $stmt->closeCursor();
	 echo json_encode($Estados);

}

/**
Obtener Municipios JSON
 */
static public function MdlJsonObtenerMunicipios($valor){

	$stmt = Conexion::conectar()->prepare("CALL ObtenerMunicipios(?)");
	$stmt -> bindParam(1, $valor, PDO::PARAM_INT);
	$stmt -> execute();

	$municipios = array("Municipios" => array()); 
 
 	while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
  		$municipios['Municipios'][] = $row;
 	}
	 $stmt->closeCursor();
	 echo json_encode($municipios);

}

/**
Obtener Colonias JSON
 */
static public function MdlJsonObtenerColonias($valor){

	$stmt = Conexion::conectar()->prepare("CALL ObtenerColonias(?)");
	$stmt -> bindParam(1, $valor, PDO::PARAM_INT);
	$stmt -> execute();

	// Inicializamos explícitamente el arreglo para evitar un resultado undefined si no hay registros
	$colonias = array("Colonias" => array()); 
 
 	while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
  		$colonias['Colonias'][] = $row;
 	}
	 $stmt->closeCursor();
	 echo json_encode($colonias);

}

/**
Da de alta una colonia escrita a mano (cuando el catálogo cat_colonias no tiene ninguna
para el municipio elegido) o reutiliza la que ya exista con ese mismo nombre, para no
duplicar filas cada vez que alguien la vuelve a escribir.
 */
static public function MdlInsertarColoniaSiNoExiste($id_municipio, $nombre, $codigo_postal){

	$stmt = Conexion::conectar()->prepare("CALL InsertarColoniaSiNoExiste(:id_municipio, :nombre, :codigo_postal)");
	$stmt->bindParam(":id_municipio", $id_municipio, PDO::PARAM_INT);
	$stmt->bindParam(":nombre", $nombre);
	$stmt->bindParam(":codigo_postal", $codigo_postal);
	$stmt->execute();
	return $stmt->fetch();

}

/**
Obtener Codigo Postal
 */
static public function MdlObtenerCodigoPostal($valor){

	$stmt = Conexion::conectar()->prepare("CALL ObtenerCodigoPostal('$valor')");
	$stmt -> execute();
	return $stmt -> fetch();

}

/**
Obtener Hotel por Usuario
 */
static public function MdlObtenerHotelUsuario($valor){

	$stmt = Conexion::conectar()->prepare("CALL ObtenerHotelUsuario('$valor')");
	$stmt -> execute();
	return $stmt -> fetchAll();

}

/**
Obtener Hotel para Reporte
 */
static public function MdlObtenerHotelUsuarioReporte($valor){

	$stmt = Conexion::conectar()->prepare("CALL ObtenerHotelUsuarioReporte('$valor')");
	$stmt -> execute();
	return $stmt -> fetchAll();

}

/**
Obtener datos del negocio (razón social, dirección, teléfono, correo) a
partir del usuario que hizo la venta. Usado para armar el ticket en PDF.
 */
static public function MdlObtenerNegocioUsuarioReporte($idUsuario){

	$stmt = Conexion::conectar()->prepare("CALL ObtenerNegocioUsuarioReporte(?)");
	$stmt->bindParam(1, $idUsuario, PDO::PARAM_INT);
	$stmt->execute();
	$respuesta = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$stmt->closeCursor(); // Liberamos el búfer para Stored Procedures
	return $respuesta;

}

}

