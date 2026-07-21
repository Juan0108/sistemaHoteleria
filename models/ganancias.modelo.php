<?php

require_once "conexion.php";

/**
 * 
 */
class ModeloGanancias{

/**
Obtener Ventas
 */
static public function MdlObtenerGanancias($IdUsuario){

	$stmt = Conexion::conectar()->prepare("CALL ObtenerGanancias('$IdUsuario')");
	$stmt -> execute();
	return $stmt -> fetchall();

}

}