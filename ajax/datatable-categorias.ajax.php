<?php


require_once "../controllers/categorias.controlador.php";
require_once "../models/categorias.modelo.php";

class TablaCategorias
{

	public function mostrarTablaCategorias(){

	   $Categorias = ControladorCategorias::crtObtenerCategorias();	   

		$datosJason = '{
			  "data": [';

			  	for ($i = 0; $i < count($Categorias); $i++){

			  		$Boton =  "<div class='checkbox'><button class='btn btn btn-info btnEditarCategoria' id_categoria='".$Categorias[$i]["Id_Categoria"]."'data-toggle='modal' data-target='#modalEditarCategoria'><i class='fa fa-pencil'></i> </button></div>";

			  		if($Categorias[$i]["Estatus"] == "checked"){

			  			$imagen = "<a class='buttonActivo'>Activo</a>";

			  		}else{

			  			$imagen = " <a class='button' > In-Activo</a>";
			  		}	
			  		
			  		$datosJason .= '[
					      "'.$Categorias[$i]["Id_Categoria"].'",
					      "'.$Categorias[$i]["Nombre"].'",
					      "'.$Categorias[$i]["Descripcion"].'",
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


$Mostar = new TablaCategorias();
$Mostar -> mostrarTablaCategorias();