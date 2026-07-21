<?php


require_once "../controllers/marcas.controlador.php";
require_once "../models/marcas.modelo.php";

class TablaMarcas
{

	public function mostrarTablaMarcas(){

	   $Marcas = ControladorMarcas::crtObtenerMarcas();	   

		$datosJason = '{
			  "data": [';

			  	for ($i = 0; $i < count($Marcas); $i++){

			  		$Boton =  "<div class='checkbox'><button class='btn btn btn-info btnEditarMarca' id_marca='".$Marcas[$i]["Id_Marca"]."'data-toggle='modal' data-target='#modalEditarMarca'><i class='fa fa-pencil'></i> </button></div>";

			  		if($Marcas[$i]["Estatus"] == "checked"){

			  			$imagen = "<a class='buttonActivo'>Activo</a>";

			  		}else{

			  			$imagen = " <a class='button' > In-Activo</a>";
			  		}	
			  		
			  		$datosJason .= '[
					      "'.$Marcas[$i]["Id_Marca"].'",
					      "'.$Marcas[$i]["Categoria"].'",
					      "'.$Marcas[$i]["Nombre"].'",
					      "'.$Marcas[$i]["Descripcion"].'",
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


$Mostar = new TablaMarcas();
$Mostar -> mostrarTablaMarcas();