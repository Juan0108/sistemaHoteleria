<?php


require_once "../controllers/inventarios.controlador.php";
require_once "../models/inventarios.modelo.php";

class TablaInventarios
{

	public $id;
	public function mostrarTablaInventarios(){

	   $idusuario = $this->id;
	   $Inventarios = ControladorInventarios::crtObtenerInventarios($idusuario);	   

		$datosJason = '{
			  "data": [';

			  	for ($i = 0; $i < count($Inventarios); $i++){	

				if($Inventarios[$i]["Stock"] <= 10){

  					$stock = "<button class='btn btn-danger'>".$Inventarios[$i]["Stock"]."</button>";

  				}else if($Inventarios[$i]["Stock"] > 11 && $Inventarios[$i]["Stock"] <= 29){

  					$stock = "<button class='btn btn-warning'>".$Inventarios[$i]["Stock"]."</button>";

  				}else{

  					$stock = "<button class='btn btn-success'>".$Inventarios[$i]["Stock"]."</button>";

  				}
			  		
			  		$datosJason .= '[
			  			  "'.$Inventarios[$i]["Id_Inventario"].'",
					      "'.$Inventarios[$i]["Id_Producto"].'",
					      "'.$Inventarios[$i]["Categoria"].'",
					      "'.$Inventarios[$i]["Marca"].'",
					      "'.$Inventarios[$i]["SubMarca"].'",
					      "'.$Inventarios[$i]["Producto"].'",
					      "'.$Inventarios[$i]["Clasificacion"].'",
					      "'.$Inventarios[$i]["Gramaje"].'",
					      "'.$stock.'",
					      "'.$Inventarios[$i]["PrecioCompra"].'",
					      "'.$Inventarios[$i]["PrecioVenta"].'"
					    ],';
			  	}

			  	$datosJason = substr($datosJason, 0,-1);

			  	$datosJason .= ']

			  }';

			  echo $datosJason;

	}
}


$Mostar = new TablaInventarios();
$Mostar -> id = $_POST['id'];
$Mostar -> mostrarTablaInventarios();