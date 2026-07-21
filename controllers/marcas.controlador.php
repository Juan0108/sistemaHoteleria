<?php

/**
 * 
 */
class ControladorMarcas{
	
static public function ctrInsertarMarca()
{

		if(isset($_POST["nuevaMarca"])){

			if(preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["nuevaMarca"])){


				//Validar Check de Estatus
				if(isset($_POST["nuevaEstatus"])){
					$Estatus = 	$_POST["nuevaEstatus"];
				}else{
					$Estatus = 2;
				}	

			   	$marca = new marca(0,
			   						$_POST["nuevaMarca"],
			   						$_POST["nuevaDescripcion"],
			   						$_POST["NuevaidCategoria"],
			   						$Estatus);

				$respuesta = ModeloMarcas::MdlInsertarMarca($marca);

				if (is_array($respuesta) && isset($respuesta['validar']) && $respuesta['validar'] === 0) {

					echo'<script>

					Swal.fire({
						  icon: "success",
						  title : "Sistema PosDit",
						  text: "La marca ha sido guardada correctamente",
						  showConfirmButton: true,
						  confirmButtonText: "Cerrar"
						  }).then(function(result){
									if (result.value) {

									window.location = "marcas";

									}
								})

					</script>';

				}else{
				echo'<script>

					Swal.fire({
						  icon: "error",
						  title : "Sistema PosDit",
						  text: "¡La marca ya existe, favor de validar!",
						  showConfirmButton: true,
						  confirmButtonText: "Cerrar"
						  }).then(function(result){
									if (result.value) {

									window.location = "marcas";

									}
								})

					</script>';
				}


			}else{

				echo'<script>

					Swal.fire({
						  icon: "error",
						  title : "Sistema PosDit",
						  text: "¡La marca no puede ir vacía o llevar caracteres especiales!",
						  showConfirmButton: true,
						  confirmButtonText: "Cerrar"
						  }).then(function(result){
							if (result.value) {

							window.location = "marcas";

							}
						})

			  	</script>';

			}
		}
}


static public function crtObtenerMarcas(){

	$respuesta = ModeloMarcas::MdlObtenerMarcas();
	return $respuesta;

 }

  static public function crtJsonObtenerMarcas(){

	$respuesta = ModeloMarcas::MdlJsonObtenerMarcas();
	return $respuesta;

 }

  static public function crtJsonObtenerMarcasCategoria(){

	$respuesta = ModeloMarcas::MdlJsonObtenerMarcasCategoria();
	return $respuesta;

 }

 static public function crtActualizarMarca()
 {

	if(isset($_POST["editarDescripcion"])){

	if(preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["editarDescripcion"])){


				//Validar Check de Estatus
				if(isset($_POST["editarEstatus"])){
					$Estatus = 	1;
				}else{
					$Estatus = 2;
				}


			   	$marca = new marca($_POST["idmarca"],
			   						$_POST["editarMarca"],
			   						$_POST["editarDescripcion"],
			   						$_POST["editaridCategoria"],
			   						$Estatus);

				$respuesta =  ModeloMarcas::MdlActualizarMarca($marca);

				if($respuesta == "ok"){

					echo'<script>

					Swal.fire({
						  icon: "success",
						  title : "Sistema PosDit",
						  text: "La marca ha sido actualizada correctamente",
						  showConfirmButton: true,
						  confirmButtonText: "Cerrar"
						  }).then(function(result){
									if (result.value) {

									window.location = "marcas";

									}
								})

					</script>';

				}


			}else{

				echo'<script>

					Swal.fire({
						  icon: "error",
						  title : "Sistema PosDit",
						  text: "¡La marca no puede ir vacía o llevar caracteres especiales!",
						  showConfirmButton: true,
						  confirmButtonText: "Cerrar"
						  }).then(function(result){
							if (result.value) {

							window.location = "marcas";

							}
						})

			  	</script>';

		}
	}


 }

}