<?php


require_once "../controllers/ventas.controlador.php";
require_once "../models/ventas.modelo.php";

class TablaVentas
{
	public $id;
	public function mostrarTablaVentas(){

	   $idusuario = $this->id;
	   $ventas = ControladorVentas::crtObtenerVentas($idusuario);	   

		$datosJason = '{
			  "data": [';

			  	for ($i = 0; $i < count($ventas); $i++){	
			  		
			  		$datosJason .= '[
					      "'.$ventas[$i]["Id_Venta"].'",
					      "'.$ventas[$i]["NTicket"].'",
					      "'.$ventas[$i]["Id_Producto"].'",
					      "'.$ventas[$i]["Categoria"].'",
					      "'.$ventas[$i]["Marca"].'",
					      "'.$ventas[$i]["SubMarca"].'",
					      "'.$ventas[$i]["Producto"].'",
					      "'.$ventas[$i]["Clasificacion"].'",
					      "'.$ventas[$i]["Gramaje"].'",
					      "'.$ventas[$i]["Cantidad"].'",
					      "'.$ventas[$i]["PrecioCompra"].'",
					      "'.$ventas[$i]["PrecioVenta"].'",
					      "'.$ventas[$i]["Ganancia"].'",
					      "'.$ventas[$i]["Venta Total"].'",
					      "'.$ventas[$i]["Fecha_Compra"].'",
					      "'.$ventas[$i]["Vendedor"].'"
					    ],';
			  	}

			  	$datosJason = substr($datosJason, 0,-1);

			  	$datosJason .= ']

			  }';

			  echo $datosJason;

	}
}


$Mostar = new TablaVentas();
$Mostar -> id = $_POST['id'];
$Mostar -> mostrarTablaVentas();