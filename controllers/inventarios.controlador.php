<?php

/**
 * 
 */
class ControladorInventarios{
	

static public function crtObtenerInventarios($idusuario){

	$respuesta = ModeloInventarios::MdlObtenerInventarios($idusuario);
	return $respuesta;

}

static public function crtObtenerStocksInventarios($valor){

	$respuesta = ModeloInventarios::MdlObtenerStocksInventarios($valor);
	return $respuesta;

}

static public function crtObtenerStocksInventarioHoteleria($valor){

	$respuesta = ModeloInventarios::MdlObtenerStocksInventarioHoteleria($valor);
	return $respuesta;

}

static public function ctrInsertarUpdateInventario()
{

	if(isset($_POST["idInventario"])){

		$Insert = $_POST["idInventario"];

		if($Insert == ""){ 
	    //Inserta registro en tabla de Inventario
		

		$inventario = new inventario($_POST["IdUsuario"],
									$_POST["idInventario"],
			   						$_POST["Qbarra"],
			   						$_POST["stockNuevo"],
			   						$_POST["PrecioCompra"],
			   						$_POST["PrecioVenta"],
			   						$_POST["negocio"]);

									var_dump($inventario);

		$respuesta = ModeloInventarios::MdlInsertarInventario($inventario);

			if($respuesta == "ok"){

					echo'<script>

					Swal.fire({
						  icon: "success",
						  title : "Sistema PosDit",
						  text: "El Producto ha sido guardado correctamente",
						  showConfirmButton: true,
						  confirmButtonText: "Cerrar"
						  }).then(function(result){
									if (result.value) {

									window.location = "inventarios";

									}
								})

					</script>';

				}

		}else{

		//Actualiza registro en tabla de Inventario

		$inventario = new inventario($_POST["IdUsuario"],
									$_POST["idInventario"],
			   						$_POST["Qbarra"],
			   						$_POST["stockNuevo"],
			   						$_POST["PrecioCompra"],
			   						$_POST["PrecioVenta"],
			   						$_POST["negocio"]);

									var_dump($inventario);

		$respuesta = ModeloInventarios::MdlActualizarInventario($inventario);

		if($respuesta == "ok"){

			echo'<script>

					Swal.fire({
						  icon: "success",
						  title : "Sistema PosDit",
						  text: "El Producto ha sido actualizado correctamente",
						  showConfirmButton: true,
						  confirmButtonText: "Cerrar"
						  }).then(function(result){
								if (result.value) {

								window.location = "inventarios";

								}
							})
			</script>';

		}

		}			
	}

}
}