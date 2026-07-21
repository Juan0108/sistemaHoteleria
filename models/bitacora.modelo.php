<?php

require_once "conexion.php";

/**
 * 
 */
class ModeloBitacora{


/**
 * Obtener Bitacora Inventarios
 */
static public function MdlObtenerBitacoraInventarios($Pantalla,$idusuario){

	$stmt = Conexion::conectar()->prepare("CALL ConsultarBitacoraPorNegocio('$idusuario', '$Pantalla')");
	$stmt -> execute();
	return $stmt -> fetchAll();

}

}

