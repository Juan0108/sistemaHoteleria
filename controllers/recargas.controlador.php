<?php

/**
 * 
 */
class ControladorRecargas{
	
static public function crtObtenerRecargas($IdUsuario){

	$respuesta = ModeloRecargas::MdlObtenerRecargas($IdUsuario);
	return $respuesta;

 }

}


