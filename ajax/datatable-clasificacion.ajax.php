<?php


require_once "../controllers/clasificaciones.controlador.php";
require_once "../models/clasificaciones.modelo.php";

class TablaClasificaciones
{

	public function mostrarTablaClasificaciones(){

	   $clasificaciones = ControladorClasificaciones::crtObtenerClasificaciones();	   

		$datosJason = '{
			  "data": [';

			  	for ($i = 0; $i < count($clasificaciones); $i++){

			  		$Boton =  "<div class='checkbox'><button class='btn btn btn-info btnEditarclasificacion' id_clasificacion='".$clasificaciones[$i]["id_clasificacion"]."'data-toggle='modal' data-target='#modalEditarClasificacion'><i class='fa fa-pencil'></i> </button></div>";

			  		if($clasificaciones[$i]["Estatus"] == "checked"){

			  			$imagen = "<a class='buttonActivo'>Activo</a>";

			  		}else{

			  			$imagen = " <a class='button' > In-Activo</a>";
			  		}	
			  		
			  		$datosJason .= '[
					      "'.$clasificaciones[$i]["id_clasificacion"].'",
					      "'.$clasificaciones[$i]["Categoria"].'",
					      "'.$clasificaciones[$i]["Nombre"].'",
					      "'.$clasificaciones[$i]["Descripcion"].'",
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


$Mostar = new TablaClasificaciones();
$Mostar -> mostrarTablaClasificaciones();