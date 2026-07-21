<?php
class controladorWebservice{
    
    static public function ctrConsultarSaldos(){

        $cuenta = "5514240972";
		$usuario = "juan.aguilar";
		$password = "2427TU58";

		$respuesta = ModeloWebservice::mdlConsultarSaldos($cuenta, $usuario, $password);
        
		$data = $respuesta->getElementsByTagName('data')->item(0)->nodeValue;
		$datos = json_decode($data);

		return $datos;

	}
    static public function ctrConsultarTransaccion($requestid){

		$cuenta = "5514240972";
		$usuario = "juan.aguilar";
		$password = "2427TU58";

		$respuesta = ModeloWebservice::mdlConsultarTransaccion($cuenta, $usuario, $password,$requestid);        
		$data = $respuesta->getElementsByTagName('data')->item(0)->nodeValue;
		$datos = json_decode($data);
		return $datos;

        

	}
    static public function ctrObtenerPines(){
        
        $cuenta = "5514240972";
		$usuario = "juan.aguilar";
		$password = "2427TU58";

		$respuesta = ModeloWebservice::mdlObtenerPines($cuenta, $usuario, $password);
        
		$data = $respuesta->getElementsByTagName('data')->item(0)->nodeValue;
		$datos = json_decode($data);

		return $datos;

	}
    static public function ctrObtenerProductos(){

        $cuenta = "5514240972";
		$usuario = "juan.aguilar";
		$password = "2427TU58";


		$respuesta = ModeloWebservice::mdlObtenerProductos($cuenta, $usuario, $password);
        
		$data = $respuesta->getElementsByTagName('data')->item(0)->nodeValue;
		$datos = json_decode($data);

		return $datos;

	}
    static public function ctrObtenerServicios(){

        $cuenta = "5514240972";
		$usuario = "juan.aguilar";
		$password = "2427TU58";

        $respuesta = ModeloWebservice::mdlObtenerServicios($cuenta, $usuario, $password);
        
		$data = $respuesta->getElementsByTagName('data')->item(0)->nodeValue;
		$datos = json_decode($data);

		return $datos;

	}
    static public function ctrProcesarTransaccion($requestid){
        // Funcion Procesa Recarga
        // Credenciales de desarrollo (Pruebas)
        //$cuenta = "4421001010";
        //$usuario = "juandavid.test";
        //$password = "ws123456";

        // Credenciales de producción
        $cuenta = "5514240972";
        $usuario = "juan.aguilar";
        $password = "2427TU58";

		$respuesta = ModeloWebservice::mdlProcesarTransaccion($cuenta, $usuario, $password,$requestid);
        
		$data = $respuesta->getElementsByTagName('data')->item(0)->nodeValue;
		$datos = json_decode($data);
		return $datos;

	}
    static public function ctrReservarTransaccion($numero,$producto){
        // Funcion Reserva Recarga
        // Credenciales de desarrollo (Pruebas)
        //$cuenta = "4421001010";
        //$usuario = "juandavid.test";
        //$password = "ws123456";

        // Credenciales de producción
        $cuenta = "5514240972";
        $usuario = "juan.aguilar";
        $password = "2427TU58";

		$respuesta = ModeloWebservice::mdlReservarTransaccion($cuenta, $usuario, $password,$numero,$producto);
        
		$data = $respuesta->getElementsByTagName('data')->item(0)->nodeValue;
		$datos = json_decode($data);
		return $datos;

	}
    static public function ctrReservarTransaccionServ($referencia,$producto,$monto){

        $cuenta = "5514240972";
		$usuario = "juan.aguilar";
		$password = "2427TU58";

		$respuesta = ModeloWebservice::mdlReservarTransaccionServ($cuenta, $usuario, $password,$referencia,$producto,$monto);
        
		$data = $respuesta->getElementsByTagName('data')->item(0)->nodeValue;
		$datos = json_decode($data);
		return $datos;

	}
}