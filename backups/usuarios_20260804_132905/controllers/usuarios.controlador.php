<?php
 
 class ControladorUsuarios{

 //Login Usuario	
 static	public function ctrIngresoUsuario()
 	{

 		if(isset($_POST["ingUsuario"])){

 			if(preg_match('/^[a-zA-Z0-9]+$/', $_POST["ingUsuario"]) &&
 			   preg_match('/^[a-zA-Z0-9]+$/', $_POST["ingPassword"])){

 				$valor = $_POST["ingUsuario"];

 				$respuesta = ModeloUsuarios::MdlObtenerUsuario($valor);

 				if($respuesta){

 					$EncriptarPass = crypt($_POST["ingPassword"],'$2a$07$asxx54ahjppf45sd87a5a4dDDGsystemdev$');
 					
 					if($respuesta["usuario"] == $_POST["ingUsuario"] && $respuesta["password"] == $EncriptarPass){

						// Validar estatus del usuario
						if ($respuesta["id_estatus"] == 2) {
							echo '<br><div class="alert alert-danger"><strong>Usuario inhabilitado.</strong> No puedes iniciar sesión con esta cuenta.</div>';
							return;
						}

						$_SESSION["IniciarSesion"]= "ok";
						$_SESSION["NombreUser"]= $respuesta["Nombre"] . " ". $respuesta["Apaterno"] ." ". $respuesta["Amaterno"];
						$_SESSION["Perfil"]= $respuesta["NombrePerfil"];
						$_SESSION["RutaFoto"]= $respuesta["foto"];
						$_SESSION["RazonSocial"]= $respuesta["Razon_Social"];
						$_SESSION["IdNegocio"]= $respuesta["id_negocio"];
						$_SESSION["IdUsuario"]= $respuesta["id_usuario"];
						$_SESSION["Telefono"] = $respuesta["Telefono"];
						$_SESSION["Estatus"] = $respuesta["id_estatus"];

						if ($respuesta["id_estatus"] == 5) {
							echo '<script>window.location = "reset";</script>';
						} else {
							echo '<script>window.location = "inicio";</script>';
						}
					}else{

						echo '<br><div class="alert alert-danger">Error al ingresar, vuelve a intentarlo</div>';
					}
				}else{

					echo '<br><div class="alert alert-danger">Error al ingresar, vuelve a intentarlo</div>';

				}
			}
		}
	}


 static public function crtObtenerPerfiles(){

	$respuesta = ModeloUsuarios::MdlObtenerPerfiles();
	return $respuesta;

 }

static public function crtObtenerUsuarios(){

	$respuesta = ModeloUsuarios::MdlObtenerUsuarios();
	return $respuesta;

 }

static public function crtObtenerUsuariosHotel(){

	$respuesta = ModeloUsuarios::MdlObtenerUsuariosHotel($_SESSION["IdNegocio"]);
	return $respuesta;

 }



 static public function crtInsertarUsuario()
 	{
 			
 		if(isset($_POST["nuevoUsuario"])){
			if(preg_match('/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]+$/', $_POST["nuevoNombre"]) &&
			   preg_match('/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]+$/', $_POST["Apaterno"]) &&
			   preg_match('/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]+$/', $_POST["Amaterno"]) &&
			   preg_match('/^[a-zA-Z0-9]+$/', $_POST["nuevoUsuario"]) &&
			   preg_match('/^[a-zA-Z0-9]+$/', $_POST["Password"])) {

				$foto = $_FILES["nuevaFoto"]["tmp_name"];

			   if($foto != ""){

					$NombreImagen = $_FILES["nuevaFoto"]["name"];
					$directorio = "views/img/Users/".$NombreImagen;
					$EncriptarPass = crypt($_POST["Password"],'$2a$07$asxx54ahjppf45sd87a5a4dDDGsystemdev$');

					// El negocio SIEMPRE es el de la sesión activa, nunca el que venga
					// en el formulario, para que no se pueda dar de alta un usuario
					// en un negocio/hotel distinto al propio.
					$Usuario = new usuario(0,
									 $_POST["nuevoNombre"],
									 $_POST["Apaterno"],
									 $_POST["Amaterno"],
									 $_SESSION["IdNegocio"],
									 $_POST["Calle"],
									 $_POST["NuevoEstado"],
									 $_POST["NuevoMunicipio"],
									 $_POST["NuevaColonia"],
									 $_POST["Perfil"],
									 $_POST["nuevoUsuario"],
									 $EncriptarPass,
									 $directorio,
									 5);
				  
				  $respuesta = ModeloUsuarios::MdlInsertarUsuario($Usuario);

					if($respuesta[0][0] == "1"){

						move_uploaded_file($foto, $directorio);

						echo '<script>
							Swal.fire({
							icon: "success",
							title : "Sistema PosDit",
							text: "¡El usuario ha sido guardado correctamente!",
							showConfirmButton: true,
							confirmButtonText: "Cerrar"
							}).then(function(result){
								if(result.value){
								
									window.location = "usuarios";

								}

							});
						
						</script>';

					}else{

						echo '<script>

						Swal.fire({
							icon: "error",
							title : "Sistema PosDit",
							text: "¡El usuario ya existe, favor de validar!",
							showConfirmButton: true,
							confirmButtonText: "Cerrar"
						}).then(function(result){

						if(result.value){
								
							window.location = "usuarios";

						}
						});
								
						</script>';

				}
				

			   }else{

			   echo '<script>
					 Swal.fire({
						title : "Sistema PosDit",
						text: "¡Favor de cargar una foto para el perfil!",
						icon: "error",
						confirmButtonText: "¡Cerrar!"
					});
				
				</script>';
					
			   }
						
			}else{

				echo '<script>
					Swal.fire({
						icon: "error",
						title : "Sistema PosDit",
						text: "¡El usuario no puede ir vacío o llevar caracteres especiales!",
						showConfirmButton: true,
						confirmButtonText: "Cerrar"

					}).then(function(result){

						if(result.value){
						
							window.location = "usuarios";

						}

					});
				

				</script>';

			}
		}
	}

static public function crtActualizarUsuario()
 	{

 		if(isset($_POST["editarUsuario"])){
			if(preg_match('/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]+$/', $_POST["editarNombre"]) &&
			   preg_match('/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]+$/', $_POST["editarApaterno"]) &&
			   preg_match('/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]+$/', $_POST["editarAmaterno"]) &&
			   preg_match('/^[a-zA-Z0-9]+$/', $_POST["editarUsuario"])) {

				if($_POST["editarPassword"] != ""){

					$EncriptarPass = crypt($_POST["editarPassword"],'$2a$07$asxx54ahjppf45sd87a5a4dDDGsystemdev$');

				}else{

					$EncriptarPass = null;

				}

					// El negocio SIEMPRE es el de la sesión activa, nunca el que venga
					// en el formulario, para que no se pueda reasignar un usuario
					// a un negocio/hotel distinto al propio.
					$Usuario = new usuario(0,
									 $_POST["editarNombre"],
									 $_POST["editarApaterno"],
									 $_POST["editarAmaterno"],
									 $_SESSION["IdNegocio"],
									 $_POST["editarCalle"],
									 $_POST["editarEstado"],
									 $_POST["editarMunicipio"],
									 $_POST["editarColonia"],
									 $_POST["editarPerfil"],
									 $_POST["editarUsuario"],
									 $EncriptarPass,
									 null,
									 1);
			  
			    $respuesta = ModeloUsuarios::MdlActualizarUsuario($Usuario);

					if($respuesta == 1){

						echo '<script>
							Swal.fire({
							icon: "success",
							title : "Sistema PosDit",
							text: "¡El usuario ha sido modificado correctamente!",
							showConfirmButton: true,
							confirmButtonText: "Cerrar"
							}).then(function(result){
								if(result.value){
								
									window.location = "usuarios";

								}

							});
						
							</script>';

					}else{

						echo '<script>

						Swal.fire({
							icon: "error",
							title : "Sistema PosDit",
							text: "¡Error desconocido, intentelo más tarde!",
							showConfirmButton: true,
							confirmButtonText: "Cerrar"
						}).then(function(result){

						if(result.value){
								
							window.location = "usuarios";

						}
						});
								
						</script>';

				}
				  
					 
			}else{

				echo '<script>
					Swal.fire({
						icon: "error",
						title : "Sistema PosDit",
						text: "¡El usuario no puede ir vacío o llevar caracteres especiales!",
						showConfirmButton: true,
						confirmButtonText: "Cerrar"

					}).then(function(result){

						if(result.value){
						
							window.location = "usuarios";

						}

					});
				

				</script>';

			}
		}
	 	
	}

	//Funciona
	static public function ctrActualizarContrasena() {
		if (isset($_POST["newPassword"])) {
			// Obtén los datos enviados por el formulario
			$id_usuario = $_POST['id_usuario'];
			$password = crypt($_POST["newPassword"], '$2a$07$asxx54ahjppf45sd87a5a4dDDGsystemdev$');
	
			// Llama al modelo para actualizar la contraseña
			$resultado = ModeloUsuarios::MdlActualizarContraseña($id_usuario, $password);
	
			// Verifica si la actualización fue exitosa
			if ($resultado) {
				// Mensaje de éxito con SweetAlert2
				echo '<script>
					Swal.fire({
						icon: "success",
						title: "Sistema PosDit",
						text: "La contraseña ha sido actualizada correctamente.",
						showConfirmButton: true,
						confirmButtonText: "Aceptar"
					}).then((result) => {
						if (result.isConfirmed) {
							window.location = "inicio";
						}
					});
				</script>';
			} else {
				// Mensaje de error con SweetAlert2
				echo '<script>
					Swal.fire({
						icon: "error",
						title: "Error al Actualizar",
						text: "No se pudo actualizar la contraseña. Inténtelo nuevamente.",
						showConfirmButton: true,
						confirmButtonText: "Aceptar"
					});
				</script>';
			}
		} else {
			// Solicitud no válida
			echo '<script>
				Swal.fire({
					icon: "warning",
					title : "Sistema PosDit",
					text: "Por favor, complete el formulario correctamente.",
					showConfirmButton: true,
					confirmButtonText: "Aceptar"
				});
			</script>';
		}
	}
}