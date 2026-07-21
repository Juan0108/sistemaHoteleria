<?php

/**
 * 
 */
class ControladorProductos{
	


static public function crtObtenerProductos(){

	$respuesta = ModeloProductos::MdlObtenerProductos();
	return $respuesta;

 }

 
static public function ctrInsertarProducto()
{

		if(isset($_POST["nuevaProducto"])){

			if(preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["nuevaProducto"])){


				//Validar Check de Estatus
				if(isset($_POST["nuevaEstatus"])){
					$Estatus = 	$_POST["nuevaEstatus"];
				}else{
					$Estatus = 2;
				}	

			   	$producto = new producto($_POST["nuevaQbarra"],
			   						$_POST["nuevaProducto"],
			   						$_POST["nuevaGramaje"],
			   						$_POST["NidCategoria"],
			   						$_POST["NuevaidMarca"],
			   						$_POST["NuevaidSubMarca"],
			   						$_POST["NuevaidClasificacion"],
			   						$Estatus);

				$respuesta = ModeloProductos::MdlInsertarProducto($producto);

				if (is_array($respuesta) && isset($respuesta['validar']) && $respuesta['validar'] === 0){

					echo'<script>

					Swal.fire({
						  icon: "success",
						  title : "Sistema PosDit",
						  text: "El Producto ha sido guardado correctamente",
						  showConfirmButton: true,
						  confirmButtonText: "Cerrar"
						  }).then(function(result){
									if (result.value) {

									window.location = "productos";

									}
								})

					</script>';

				}else{
				echo'<script>

					Swal.fire({
						  icon: "error",
						  title : "Sistema PosDit",
						  text: "¡La Producto ya existe, favor de validar!",
						  showConfirmButton: true,
						  confirmButtonText: "Cerrar"
						  }).then(function(result){
									if (result.value) {

									window.location = "productos";

									}
								})

					</script>';
				}


			}else{

				echo'<script>

					Swal.fire({
						  icon: "error",
						  title : "Sistema PosDit",
						  text: "¡El Producto no puede ir vacía o llevar caracteres especiales!",
						  showConfirmButton: true,
						  confirmButtonText: "Cerrar"
						  }).then(function(result){
							if (result.value) {

							window.location = "productos";

							}
						})

			  	</script>';

			}
		}
}


public function crtActualizarProducto(){

if(isset($_POST["editarProducto"])){

			if(preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["editarProducto"])){


				//Validar Check de Estatus
				if(isset($_POST["editarEstatus"])){
					$Estatus = 	1;
				}else{
					$Estatus = 2;
				}

				$x = $_POST["ProductoId"];

			   	$producto = new producto($x,
			   						$_POST["editarProducto"],
			   						$_POST["editarGramaje"],
			   						$_POST["editaridCategoria"],
			   						$_POST["editaridMarca"],
			   						$_POST["editaridSubMarca"],
			   						$_POST["editaridClasificacion"],
			   						$Estatus);

				$respuesta = ModeloProductos::MdlActualizarProducto($producto);

				$respuesta ="ok"; 

				if($respuesta == "ok"){

					echo'<script>

					Swal.fire({
						  icon: "success",
						  title : "Sistema PosDit",
						  text: "El producto ha sido actualizado correctamente",
						  showConfirmButton: true,
						  confirmButtonText: "Cerrar"
						  }).then(function(result){
									if (result.value) {

									window.location = "productos";

									}
								})

					</script>';

				}


			}else{

				echo'<script>

					Swal.fire({
						  icon: "error",
						  title : "Sistema PosDit",
						  text: "¡El producto no puede ir vacío o llevar caracteres especiales!",
						  showConfirmButton: true,
						  confirmButtonText: "Cerrar"
						  }).then(function(result){
							if (result.value) {

							window.location = "productos";

							}
						})

			  	</script>';

			}
		}



 }

}