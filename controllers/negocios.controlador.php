<?php

/**
 * 
 */
class ControladorNegocios{
	

static public function ctrInsertarNegocio()
{

		if(isset($_POST["nuevoRazonsocial"])){

			if(preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["nuevoRazonsocial"])){

				$CartaResponsiva = $_FILES["nuevaCartaResponsiva"]["tmp_name"];

				if($CartaResponsiva != ""){

				$NombreImagen = $_FILES["nuevaCartaResponsiva"]["name"];
			   	$directorio = "views/img/CartaResponsiva_Negocios/".$NombreImagen;

			   	$negocio = new negocio(0,
			   							$_POST["nuevoRazonsocial"],
			   							$_POST["nuevoResponsable"],
			   							$_POST["telefono"],
			   							$_POST["NuevoCorreo"],
			   							$_POST["NuevoGiro"],
			   							$_POST["Calle"],
			   							$_POST["NuevoEstado"],
			   							$_POST["NuevoMunicipio"],
			   							$_POST["NuevaColonia"],
			   							$_POST["NuevoTipoPago"],
			   							0,
			   							0,
			   							$_POST["NuevoEstatus"],
			   							0,
			   							0
			   							);

			   		$respuesta = ModeloNegocios::MdlInsertarNegocio($negocio);

				   	    if($respuesta[0][0] == "1"){

				   	    move_uploaded_file($CartaResponsiva, $directorio);

						echo'<script>

						Swal.fire({
							  icon: "success",
							  title : "Sistema PosDit",
							  text: "El Negocio ha sido guardado correctamente",
							  showConfirmButton: true,
							  confirmButtonText: "Cerrar"
							  }).then(function(result){
										if (result.value) {

										window.location = "negocios";

										}
									})

						</script>';

					}else{
					echo'<script>

						Swal.fire({
							  icon: "error",
							  title : "Sistema PosDit",
							  text: "¡El Negocio ya existe, favor de validar!",
							  showConfirmButton: true,
							  confirmButtonText: "Cerrar"
							  }).then(function(result){
										if (result.value) {

										window.location = "negocios";

										}
									})

						</script>';
					}

				}else{

			   	echo '<script>
					 Swal.fire({
						title : "Sistema PosDit",
		      			text: "¡Favor de cargar la carta responsiva!",
		      			icon: "error",
		      			confirmButtonText: "¡Cerrar!"
		    		});
				
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

							window.location = "negocios";

							}
						})

			  	</script>';

			}
		}
}

static public function ctrActualizarNegocio()
{

		if(isset($_POST["editarRazonsocial"])){

			if(preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["editarRazonsocial"])){

			   	$negocio = new negocio($_POST["editarIdNegocio"],
			   							$_POST["editarRazonsocial"],
			   							$_POST["editarResponsable"],
			   							$_POST["editarTelefono"],
			   							$_POST["editarCorreo"],
			   							$_POST["editarGiro"],
			   							$_POST["editarCalle"],
			   							$_POST["editarEstado"],
			   							$_POST["editarMunicipio"],
			   							$_POST["editarColonia"],
			   							$_POST["editarTipoPago"],
			   							0,
			   							0,
			   							$_POST["editarEstatus"],
			   							$_POST["editarSAire"],
			   							$_POST["editarSServicios"],
			   							);

			   	$respuesta = ModeloNegocios::MdlActualizarNegocio($negocio);

				if($respuesta == "1"){

					echo'<script>

					Swal.fire({
						icon: "success",
						title : "Sistema PosDit",
						text: "El Negocio ha sido modificado correctamente",
					  }).then((result) => {
						window.location = "negocios";
					  })

						</script>';

				}else{
					echo'<script>

						Swal.fire({
							  icon: "error",
							  title : "Sistema PosDit",
							  text: "¡Error desconocido, intentelo más tarde!",
							  showConfirmButton: true,
							  confirmButtonText: "Cerrar"
							  }).then(function(result){
										if (result.value) {

										window.location = "negocios";

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

							window.location = "negocios";

							}
						})

			  	</script>';

			}
		}
}


static public function crtObtenerNegocios(){

	$respuesta = ModeloNegocios::MdlObtenerNegocios();
	return $respuesta;

 }

static public function crtJsonObtenerTipoPago(){

	$respuesta = ModeloNegocios::MdlJsonObtenerTipoPago();
	return $respuesta;

 }


static public function crtJsonObtenerGiro(){

	$respuesta = ModeloNegocios::MdlJsonObtenerGiro();
	return $respuesta;

 }


 static public function crtJsonObtenerNegocios(){

	$respuesta = ModeloNegocios::MdlJsonObtenerNegocios();
	return $respuesta;

 }

 static public function crtJsonObtenerEstatus(){

	$respuesta = ModeloNegocios::MdlJsonObtenerEstatus();
	return $respuesta;

 }

 static public function crtJsonObtenerEstados(){

	$respuesta = ModeloNegocios::MdlJsonObtenerEstados();
	return $respuesta;

 }

static public function crtObtenerNegocioUsuario($valor){

	$respuesta = ModeloNegocios::MdlObtenerNegocioUsuario($valor);
	return $respuesta;

 }
 
 static public function crtObtenerNegocioUsuarioReporte($valor){

	$respuesta = ModeloNegocios::MdlObtenerNegocioUsuarioReporte($valor);
	return $respuesta;

 }

}