<?php

require_once "conexion.php";

/**
 * 
 */
class ModeloNegocios{


/**
Obtener Colonias JSON
 */
static public function MdlInsertarNegocio($negocio){

	$stmt = Conexion::conectar()->prepare(
		"CALL InsertarNegocio('$negocio->Razon_social',
		'$negocio->Responsable',
		'$negocio->Telefono',
		'$negocio->Correo',
		'$negocio->Id_giro',
		'$negocio->Calle',
		'$negocio->Id_estado',
		'$negocio->Id_municipio',
		'$negocio->id_colonia',
		'$negocio->id_tipopago',
		'$negocio->id_estatus')");

	$stmt -> execute();

    return $stmt -> fetch();

}

/**
Actualizar Negocios
 */
static public function MdlActualizarNegocio($negocio){

	$stmt = Conexion::conectar()->prepare(
		"CALL ActualizarNegocio('$negocio->id_negocio',
		'$negocio->Razon_social',
		'$negocio->Responsable',
		'$negocio->Telefono',
		'$negocio->Correo',
		'$negocio->Id_giro',
		'$negocio->Calle',
		'$negocio->Id_estado',
		'$negocio->Id_municipio',
		'$negocio->id_colonia',
		'$negocio->id_tipopago',
		'$negocio->id_estatus',
		'$negocio->SAire',
		'$negocio->SServicios')");

	return $stmt -> execute();

}

/**
Obtener Negocio
 */
static public function MdlObtenerNegocio($valor){

		$stmt = Conexion::conectar()->prepare("CALL ObtenerNegocio('$valor')");
		$stmt -> execute();
		return $stmt -> fetch();

}


/**
Obtener Negocios
 */
static public function MdlObtenerNegocios(){

	$stmt = Conexion::conectar()->prepare("CALL ObtenerNegocios()");
	$stmt -> execute();
	return $stmt -> fetchAll();

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

	 echo json_encode($TipoPago);

}

/**
Obtener Giro JSON
 */
static public function MdlJsonObtenerGiro(){

	$stmt = Conexion::conectar()->prepare("CALL ObtenerGiro()");
	$stmt -> execute();

	$Giro = array();
 
 	while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
  
  		$Giro['data'][] = $row;
 	}

	 echo json_encode($Giro);

}

/**
Obtener Giro JSON
 */
static public function MdlJsonObtenerNegocios(){

	$stmt = Conexion::conectar()->prepare("CALL ObtenerComboNegocios()");
	$stmt -> execute();

	$Negocios = array();
 
 	while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
  
  		$Negocios['data'][] = $row;
 	}

	 echo json_encode($Negocios);

}

/**
Obtener Giro JSON
 */
static public function MdlJsonObtenerEstatus(){

	$stmt = Conexion::conectar()->prepare("CALL ObtenerEstatus()");
	$stmt -> execute();

	$Estatus = array();
 
 	while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
  
  		$Estatus['data'][] = $row;
 	}

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

	 echo json_encode($Estados);

}

/**
Obtener Municipios JSON
 */
static public function MdlJsonObtenerMunicipios($valor){

	$stmt = Conexion::conectar()->prepare("CALL ObtenerMunicipios('$valor')");
	$stmt -> execute();

	$municipios = array();
 
 	while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
  
  		$municipios['Municipios'][] = $row;
 	}

	 echo json_encode($municipios);

}

/**
Obtener Colonias JSON
 */
static public function MdlJsonObtenerColonias($valor){

	$stmt = Conexion::conectar()->prepare("CALL ObtenerColonias('$valor')");
	$stmt -> execute();

	$colonias = array();
 
 	while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
  
  		$colonias['Colonias'][] = $row;
 	}

	 echo json_encode($colonias);

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
Obtener Negocio por Usuario
 */
static public function MdlObtenerNegocioUsuario($valor){

	$stmt = Conexion::conectar()->prepare("CALL ObtenerNegocioUsuario('$valor')");
	$stmt -> execute();
	return $stmt -> fetchAll();

}

/**
Obtener Negocio para Reporte
 */
static public function MdlObtenerNegocioUsuarioReporte($valor){

	$stmt = Conexion::conectar()->prepare("CALL ObtenerNegocioUsuarioReporte('$valor')");
	$stmt -> execute();
	return $stmt -> fetchAll();

}

}

