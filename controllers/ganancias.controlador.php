<?php

/**
 * 
 */
class ControladorGanancias{
	
static public function crtObtenerGanancias($IdUsuario){

	$respuesta = ModeloGanancias::MdlObtenerGanancias($IdUsuario);
	return $respuesta;

 }

}


