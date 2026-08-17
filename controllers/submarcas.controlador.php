<?php

/**
 * 
 */
class ControladorSubMarcas{
	
static public function ctrInsertarSubMarca()
{

		if(isset($_POST["nuevaSubMarca"])){

			if(preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["nuevaSubMarca"])){


				//Validar Check de Estatus
				if(isset($_POST["nuevaEstatus"])){
					$Estatus = 	$_POST["nuevaEstatus"];
				}else{
					$Estatus = 2;
				}	

			   	$submarca = new submarca(0,
			   						$_POST["nuevaSubMarca"],
			   						$_POST["NuevaidMarca"],
			   						$Estatus);

				$respuesta = ModeloSubMarcas::MdlInsertarSubMarca($submarca);

				if (is_array($respuesta) && isset($respuesta['validar']) && (int)$respuesta['validar'] === 0){

					echo'<script>

					Swal.fire({
						  icon: "success",
						  title : "Sistema PosDit",
						  text: "La submarca ha sido guardada correctamente",
						  showConfirmButton: true,
						  confirmButtonText: "Cerrar"
						  }).then(function(result){
									if (result.value) {

									window.location = "submarcas";

									}
								})

					</script>';

				}else{
				echo'<script>

					Swal.fire({
						  icon: "error",
						  title : "Sistema PosDit",
						  text: "¡La submarca ya existe, favor de validar!",
						  showConfirmButton: true,
						  confirmButtonText: "Cerrar"
						  }).then(function(result){
									if (result.value) {

									window.location = "submarcas";

									}
								})

					</script>';
				}


			}else{

				echo'<script>

					Swal.fire({
						  icon: "error",
						  title : "Sistema PosDit",
						  text: "¡La submarca no puede ir vacía o llevar caracteres especiales!",
						  showConfirmButton: true,
						  confirmButtonText: "Cerrar"
						  }).then(function(result){
							if (result.value) {

							window.location = "submarcas";

							}
						})

			  	</script>';

			}
		}
}


static public function crtObtenerSubMarcas(){

	$respuesta = ModeloSubMarcas::MdlObtenerSubMarcas();
	return $respuesta;

 }

 static public function crtActualizarSubMarca()
 {

	if(isset($_POST["editarsubMarca"])){

	if(preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["editarsubMarca"])){


				//Validar Check de Estatus
				if(isset($_POST["editarEstatus"])){
					$Estatus = 	1;
				}else{
					$Estatus = 2;
				}


			   	$submarca = new submarca($_POST["idsubmarca"],
			   						$_POST["editarsubMarca"],
			   						$_POST["editaridMarca"],
			   						$Estatus);

				$respuesta =  ModeloSubMarcas::MdlActualizarSubMarca($submarca);

				if($respuesta == "ok"){

					echo'<script>

					Swal.fire({
						  icon: "success",
						  title : "Sistema PosDit",
						  text: "La submarca ha sido actualizada correctamente",
						  showConfirmButton: true,
						  confirmButtonText: "Cerrar"
						  }).then(function(result){
									if (result.value) {

									window.location = "submarcas";

									}
								})

					</script>';

				}


			}else{

				echo'<script>

					Swal.fire({
						  icon: "error",
						  title : "Sistema PosDit",
						  text: "¡La submarca no puede ir vacía o llevar caracteres especiales!",
						  showConfirmButton: true,
						  confirmButtonText: "Cerrar"
						  }).then(function(result){
							if (result.value) {

							window.location = "submarcas";

							}
						})

			  	</script>';

		}
	}


 }

}