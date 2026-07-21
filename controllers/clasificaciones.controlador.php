<?php

/**
 * 
 */
class ControladorClasificaciones{
	
static public function ctrInsertarClasificacion()
{

		if(isset($_POST["nuevaclasificacion"])){

			if(preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["nuevaclasificacion"])){


				//Validar Check de Estatus
				if(isset($_POST["nuevaEstatus"])){
					$Estatus = 	$_POST["nuevaEstatus"];
				}else{
					$Estatus = 2;
				}	

			   	$clasificacion = new clasificacion(0,
			   						$_POST["nuevaclasificacion"],
			   						$_POST["nuevaDescripcion"],
			   						$_POST["NuevaidCategoria"],
			   						$Estatus);

				$respuesta = ModeloClasificaciones::MdlInsertarClasificacion($clasificacion);

				if (is_array($respuesta) && isset($respuesta['validar']) && $respuesta['validar'] === 0){

					echo'<script>

					Swal.fire({
						  icon: "success",
						  title : "Sistema PosDit",
						  text: "La clasificación ha sido guardada correctamente",
						  showConfirmButton: true,
						  confirmButtonText: "Cerrar"
						  }).then(function(result){
									if (result.value) {

									window.location = "clasificaciones";

									}
								})

					</script>';

				}else{
				echo'<script>

					Swal.fire({
						  icon: "error",
						  title : "Sistema PosDit",
						  text: "¡La clasificación ya existe, favor de validar!",
						  showConfirmButton: true,
						  confirmButtonText: "Cerrar"
						  }).then(function(result){
									if (result.value) {

									window.location = "clasificaciones";

									}
								})

					</script>';
				}


			}else{

				echo'<script>

					Swal.fire({
						  icon: "error",
						  title : "Sistema PosDit",
						  text: "¡La clasificación no puede ir vacía o llevar caracteres especiales!",
						  showConfirmButton: true,
						  confirmButtonText: "Cerrar"
						  }).then(function(result){
							if (result.value) {

							window.location = "clasificaciones";

							}
						})

			  	</script>';

			}
		}
}


static public function crtObtenerClasificaciones(){

	$respuesta = ModeloClasificaciones::MdlObtenerClasificaciones();
	return $respuesta;

 }

 static public function crtActualizarClasificacion()
 {

	if(isset($_POST["editarClasificacion"])){

	if(preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["editarClasificacion"])){


				//Validar Check de Estatus
				if(isset($_POST["editarEstatus"])){
					$Estatus = 	1;
				}else{
					$Estatus = 2;
				}


			   	$clasificacion = new clasificacion($_POST["idClasificacion"],
			   						$_POST["editarClasificacion"],
			   						$_POST["editarDescripcion"],
			   						$_POST["editaridCategoria"],
			   						$Estatus);

				$respuesta =  ModeloClasificaciones::MdlActualizarClasificacion($clasificacion);

				if($respuesta == "ok"){

					echo'<script>

					Swal.fire({
						  icon: "success",
						  title : "Sistema PosDit",
						  text: "La clasificacion ha sido actualizada correctamente",
						  showConfirmButton: true,
						  confirmButtonText: "Cerrar"
						  }).then(function(result){
									if (result.value) {

									window.location = "clasificaciones";

									}
								})

					</script>';

				}


			}else{

				echo'<script>

					Swal.fire({
						  icon: "error",
						  title : "Sistema PosDit",
						  text: "¡La clasificacion no puede ir vacía o llevar caracteres especiales!",
						  showConfirmButton: true,
						  confirmButtonText: "Cerrar"
						  }).then(function(result){
							if (result.value) {

							window.location = "clasificaciones";

							}
						})

			  	</script>';

		}
	}


 }

}