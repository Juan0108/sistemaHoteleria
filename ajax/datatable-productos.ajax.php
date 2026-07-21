<?php


require_once "../controllers/productos.controlador.php";
require_once "../models/productos.modelo.php";

class TablaProductos
{

	public function mostrarTablaProductos(){

	   $Productos = ControladorProductos::crtObtenerProductos();	   

		$datosJason = '{
			  "data": [';

			  	for ($i = 0; $i < count($Productos); $i++){

			  		$Boton =  "<div class='checkbox'><button class='btn btn-info btnEditarProducto' id_producto='".$Productos[$i]["Id_Producto"]."'data-toggle='modal' data-target='#modalEditarProducto'><i class='fa fa-pencil'></i> </button></div>";

			  		if($Productos[$i]["Estatus"] == "checked"){

			  			$imagen = "<a class='buttonActivo'>Activo</a>";

			  		}else{

			  			$imagen = " <a class='button' > In-Activo</a>";
			  		}	
			  		
			  		$datosJason .= '[
					      "'.$Productos[$i]["Id_Producto"].'",
					      "'.$Productos[$i]["Categoria"].'",
					      "'.$Productos[$i]["Marca"].'",
					      "'.$Productos[$i]["SubMarca"].'",
					      "'.$Productos[$i]["Producto"].'",
					      "'.$Productos[$i]["Clasificacion"].'",
					      "'.$Productos[$i]["Gramaje"].'",
					      "'.$imagen.'",
					      "'.$Boton.'"
					    ],';
			  	}

			  	$datosJason = substr($datosJason, 0,-1);

			  	$datosJason .= ']

			  }';

			  echo $datosJason;

	}
}


$Mostar = new TablaProductos();
$Mostar -> mostrarTablaProductos();