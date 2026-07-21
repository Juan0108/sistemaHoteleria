<?php


require_once "../controllers/ganancias.controlador.php";
require_once "../models/ganancias.modelo.php";

class TablaGananacias
{
	public $id;
	public function mostrarTablaGanancias(){

	   $idusuario = $this->id;
	   $ganancias = ControladorGanancias::crtObtenerGanancias($idusuario);	   

		$datosJason = '{
			  "data": [';

			  	for ($i = 0; $i < count($ganancias); $i++){

			  		$totalCompras=$ganancias[$i]["PrecioCompra"]*$ganancias[$i]["Stock"];
			  		$totalVentas=$ganancias[$i]["PrecioVenta"]*$ganancias[$i]["Stock"];
			  		$total=$totalVentas-$totalCompras;
			  		
			  		$datosJason .= '[
					      "'.$ganancias[$i]["Marca"].'",
					      "'.$ganancias[$i]["Submarca"].'",
					      "'.$ganancias[$i]["Clasificacion"].'",
					      "'.$ganancias[$i]["Stock"].'",
					      "'.$ganancias[$i]["PrecioCompra"].'",
					      "'.$totalCompras.'",
					      "'.$ganancias[$i]["PrecioVenta"].'",
					      "'.$totalVentas.'",
					      "'.$total.'"
					    ],';
			  	}

			  	$datosJason = substr($datosJason, 0,-1);

			  	$datosJason .= ']

			  }';

			  echo $datosJason;

	}
}


$Mostar = new TablaGananacias();
$Mostar -> id = $_POST['id'];
$Mostar -> mostrarTablaGanancias();