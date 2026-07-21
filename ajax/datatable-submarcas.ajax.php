<?php


require_once "../controllers/submarcas.controlador.php";
require_once "../models/submarcas.modelo.php";

class TablaSubMarcas
{

	public function mostrarTablaSubMarcas(){

	   $SubMarcas = ControladorSubMarcas::crtObtenerSubMarcas();	   

		$datosJason = '{
			  "data": [';

			  	for ($i = 0; $i < count($SubMarcas); $i++){

			  		$Boton =  "<div class='checkbox'><button class='btn btn btn-info btnEditarSubMarca' id_submarca='".$SubMarcas[$i]["Id_SubMarca"]."'data-toggle='modal' data-target='#modalEditarSubMarca'><i class='fa fa-pencil'></i> </button></div>";

			  		if($SubMarcas[$i]["Estatus"] == "checked"){

			  			$imagen = "<a class='buttonActivo'>Activo</a>";

			  		}else{

			  			$imagen = " <a class='button' > In-Activo</a>";
			  		}	
			  		
			  		$datosJason .= '[
					      "'.$SubMarcas[$i]["Id_SubMarca"].'",
					      "'.$SubMarcas[$i]["Marca"].'",
					      "'.$SubMarcas[$i]["SubMarca"].'",
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


$Mostar = new TablaSubMarcas();
$Mostar -> mostrarTablaSubMarcas();