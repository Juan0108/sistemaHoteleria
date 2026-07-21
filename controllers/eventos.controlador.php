<?php

/**
 * 
 */
class ControladorEventos{
	
static public function crtObtenerEventosProveedor(){

	$respuesta = ModeloEventos::MdlObtenerEventosProveedor();
	return $respuesta;

 }

 static public function crtUpdateStatusNotificacion($Id_Evento, $Id_Estatus){

	ModeloEventos::MdlUpdateStatusNotificacion($Id_Evento, $Id_Estatus);

 }

}


