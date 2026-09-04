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

static public function crtGuardarCorteDiario($id_hotel, $id_usuario, $venta_reportada, $venta_sistema, $monto_caja, $cargos_mantenimiento, $diferencia, $archivo_excel){

	return ModeloVentas::MdlGuardarCorteDiario($id_hotel, $id_usuario, $venta_reportada, $venta_sistema, $monto_caja, $cargos_mantenimiento, $diferencia, $archivo_excel);

}

static public function crtObtenerCortesDiarios($id_hotel, $fecha_inicio, $fecha_fin){

	return ModeloVentas::MdlObtenerCortesDiarios($id_hotel, $fecha_inicio, $fecha_fin);

}

}


