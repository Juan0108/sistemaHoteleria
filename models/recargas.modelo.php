<?php

require_once "conexion.php";

/**
 * 
 */
class ModeloRecargas{

/**
Obtener Recargas
 */
static public function MdlObtenerRecargas($IdUsuario){

	$stmt = Conexion::conectar()->prepare("CALL ObtenerVentaYServiciosPorUsuario('$IdUsuario')");
	$stmt -> execute();
	return $stmt -> fetchall();

}

}