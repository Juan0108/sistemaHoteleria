<?php

/**
 * 
 */
class ControladorVentas{
	
static public function crtObtenerVentas($IdUsuario){

	$respuesta = ModeloVentas::MdlObtenerVentas($IdUsuario);
	return $respuesta;

 }
 
 static public function crtObtenerCierreDia($IdUsuario){

	$respuesta = ModeloVentas::MdlObtenerCierreDia($IdUsuario);
	return $respuesta;

 }

 static public function crtObtenerTicket($IdTicket, $idusuario){

	$respuesta = ModeloVentas::MdlObtenerTicket($IdTicket, $idusuario);
	return $respuesta;

 }

 static public function crtObtenerVentasFecha($idusuario, $fi, $ff){

	$respuesta = ModeloVentas::MdlObtenerVentasFecha($idusuario, $fi, $ff);
	return $respuesta;

}


 static public function crtObtenerSumaVentas($valor){

	$respuesta = ModeloVentas::MdlObtenerSumaVentas($valor);
	return $respuesta;

}

 static public function crtObtenerGraficaVentas($valor){

	$respuesta = ModeloVentas::MdlObtenerGraficaVentas($valor);
	return $respuesta;

}

static public function crtObtenerGraficaBalance($valor){

	$respuesta = ModeloVentas::MdlObtenerGraficaBalance($valor);
	return $respuesta;

}

static public function crtObtenerSumaGanancias($valor){

	$respuesta = ModeloVentas::MdlObtenerSumaGanancias($valor);
	return $respuesta;

}

static public function crtObtenerTopVentas($IdUsuario){

	$respuesta = ModeloVentas::MdlObtenerTopVentas($IdUsuario);
	return $respuesta;

 }

}


