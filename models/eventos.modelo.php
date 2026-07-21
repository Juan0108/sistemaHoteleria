<?php

require_once "conexion.php";

/**
 * 
 */
class ModeloEventos{

/**
Obtener Eventos Notificaciones Proveedores
 */
static public function MdlObtenerEventosProveedor(){

	$stmt = Conexion::conectar()->prepare("CALL ObtenerEventosEnRango()");
	$stmt -> execute();
	return $stmt -> fetchall();

}

/**
Actualiza Estatus de Notificaciones
 */
static public function MdlUpdateStatusNotificacion($Id_Evento, $Id_Estatus){

	$stmt = Conexion::conectar()->prepare("CALL UpdateEstatusNotificacion('$Id_Evento','$Id_Estatus')");
	$stmt -> execute();
	return $stmt -> fetchall();
}


}