<?php


require_once "../controllers/recargas.controlador.php";
require_once "../models/recargas.modelo.php";

class TablaRecargas
{
	public $id;
	public function mostrarTablaRecargas(){

	   $idusuario = $this->id;
	   $ganancias = ControladorRecargas::crtObtenerRecargas($idusuario);	   

		$datosJason = '{
			  "data": [';

			  	for ($i = 0; $i < count($ganancias); $i++){
		  		
			  		$datosJason .= '[
					      "'.$ganancias[$i]["NTicket"].'",
					      "'.$ganancias[$i]["Id_Codigo"].'",
					      "'.$ganancias[$i]["Numero"].'",
					      "'.$ganancias[$i]["Folio"].'",
					      "'.$ganancias[$i]["PrecioVenta"].'",
					      "'.$ganancias[$i]["Ganancia"].'",
                          "'.$ganancias[$i]["Fecha_Compra"].'"
					    ],';
			  	}

			  	$datosJason = substr($datosJason, 0,-1);

			  	$datosJason .= ']

			  }';

			  echo $datosJason;

	}
}


$Mostar = new TablaRecargas();
$Mostar -> id = $_POST['id'];
$Mostar -> mostrarTablaRecargas();