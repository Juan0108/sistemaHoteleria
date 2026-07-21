<?php


require_once "../controllers/negocios.controlador.php";
require_once "../models/negocios.modelo.php";

class TablaNegocios
{

	public function mostrarTablaNegocios(){

	   $Negocios = ControladorNegocios::crtObtenerNegocios();	   

		$datosJason = '{
			  "data": [';

			  	for ($i = 0; $i < count($Negocios); $i++){

			  		$Boton =  "<div class='checkbox'><button class='btn btn btn-info btnEditarNegocio' data-id-negocio='".$Negocios[$i]["Id_Negocio"]."'data-toggle='modal' data-target='#modalEditarNegocio'><i class='fa fa-pencil'></i> </button></div>";

			  		if($Negocios[$i]["Estatus"] == "checked"){

			  			$imagen = "<a class='buttonActivo'>Activo</a>";

			  		}elseif($Negocios[$i]["Estatus"] == "prueba") {

			  			$imagen = " <a class='buttonPeriodo' > Periodo Prueba</a>";

			  		}elseif ($Negocios[$i]["Estatus"] == "") {

			  			$imagen = " <a class='button' > In-Activo</a>";
			  		}	
			  		
			  		$datosJason .= '[
					      "'.$Negocios[$i]["Id_Negocio"].'",
					      "'.$Negocios[$i]["Razon_Social"].'",
					      "'.$Negocios[$i]["Responsable"].'",
					      "'.$Negocios[$i]["Telefono"].'",
					      "'.$Negocios[$i]["Estado"].'",
					      "'.$Negocios[$i]["Municipio"].'",
					      "'.$Negocios[$i]["Colonia"].'",
					      "'.$Negocios[$i]["Calle"].'",
					      "'.$Negocios[$i]["Correo"].'",
					      "'.$Negocios[$i]["Giro"].'",
					      "'.$Negocios[$i]["TipoPago"].'",
					      "'.$Negocios[$i]["Fecha_Alta"].'",
					      "'.$Negocios[$i]["Fecha_Baja"].'",
					      "'.$Negocios[$i]["SAire"].'",
					      "'.$Negocios[$i]["SServicios"].'",
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


$Mostar = new TablaNegocios();
$Mostar -> mostrarTablaNegocios();