<?php

/**
 * 
 */
class ControladorBitacora{
	

static public function crtObtenerBitacoraInvetarios($idusuario){

    $pantalla = "Inventarios";
	$respuesta = ModeloBitacora::MdlObtenerBitacoraInventarios($pantalla,$idusuario);
	return $respuesta;

}

}