<?php

/**
 * 
 */
class ControladorCategorias{
	
static public function ctrInsertarCategoria()
{

		if(isset($_POST["nuevaCategoria"])){

			if(preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["nuevaCategoria"])){


				//Validar Check de Estatus
				if(isset($_POST["nuevaEstatus"])){
					$Estatus = 	$_POST["nuevaEstatus"];
				}else{
					$Estatus = 2;
				}	

			   	$categoria = new categoria(0,
			   							$_POST["nuevaCategoria"],
			   							$_POST["nuevaDescripcion"],
			   							$Estatus);

				$respuesta = ModeloCategorias::MdlInsertarCategoria($categoria);
				

                
				if (is_array($respuesta) && isset($respuesta['validar']) && $respuesta['validar'] === 0) {
					echo'<script>

					Swal.fire({
						  icon: "success",
						  title : "Sistema PosDit",
						  text: "La categoría ha sido guardada correctamente",
						  showConfirmButton: true,
						  confirmButtonText: "Cerrar"
						  }).then(function(result){
									if (result.value) {

									//window.location = "categorias";

									}
								})

					</script>';

				}else{
				echo'<script>

					Swal.fire({
						  icon: "error",
						  title : "Sistema PosDit",
						  text: "¡La categoría ya existe, favor de validar!",
						  showConfirmButton: true,
						  confirmButtonText: "Cerrar"
						  }).then(function(result){
									if (result.value) {

									//window.location = "categorias";

									}
								})

					</script>';
				}

			}else{

				echo'<script>

					Swal.fire({
						  icon: "error",
						  title : "Sistema PosDit",
						  text: "¡La categoría no puede ir vacía o llevar caracteres especiales!",
						  showConfirmButton: true,
						  confirmButtonText: "Cerrar"
						  }).then(function(result){
							if (result.value) {

							window.location = "categorias";

							}
						})

			  	</script>';

			}
		}
}


static public function crtObtenerCategorias(){

	$respuesta = ModeloCategorias::MdlObtenerCategorias();
	return $respuesta;

 }

 static public function crtJsonObtenerCategorias(){

	$respuesta = ModeloCategorias::MdlJsonObtenerCategorias();
	return $respuesta;

 }


 static public function crtActualizarCategoria(){

			if(isset($_POST["editarCategoria"])){

			if(preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["editarCategoria"])){


				//Validar Check de Estatus
				if(isset($_POST["editarEstatus"])){
					$Estatus = 	1;
				}else{
					$Estatus = 2;
				}


			   	$categoria = new categoria($_POST["idCategoria"],
			   							$_POST["editarCategoria"],
			   							$_POST["editarDescripcion"],
			   							$Estatus);

				$respuesta = ModeloCategorias::MdlActualizarCategoria($categoria);

				if($respuesta == "ok"){

					echo'<script>

					Swal.fire({
						  icon: "success",
						  title : "Sistema PosDit",
						  text: "La categoría ha sido actualizada correctamente",
						  showConfirmButton: true,
						  confirmButtonText: "Cerrar"
						  }).then(function(result){
									if (result.value) {

									window.location = "categorias";

									}
								})

					</script>';

				}


			}else{

				echo'<script>

					Swal.fire({
						  icon: "error",
						  title : "Sistema PosDit",
						  text: "¡La categoría no puede ir vacía o llevar caracteres especiales!",
						  showConfirmButton: true,
						  confirmButtonText: "Cerrar"
						  }).then(function(result){
							if (result.value) {

							window.location = "categorias";

							}
						})

			  	</script>';

			}
		}

 }

}