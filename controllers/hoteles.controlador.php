<?php

/**
 * 
 */
class ControladorHoteles{
	

static public function ctrInsertarHotel()
{
	if(isset($_POST["nuevoRazonsocial"])){

		if(preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["nuevoRazonsocial"])){

			$Contrato = $_FILES["nuevoContrato"]["tmp_name"];

			if($Contrato != ""){

				$NombreImagen = $_FILES["nuevoContrato"]["name"];
				$dirPath = "views/img/Contrato_Hoteles/";
				if (!is_dir($dirPath)) {
					mkdir($dirPath, 0755, true);
				}
				$directorio = $dirPath . $NombreImagen;

$IdEstatus = isset($_POST["NuevoEstatus"]) ? intval($_POST["NuevoEstatus"]) : 0;
				if ($IdEstatus < 1 || $IdEstatus > 7) {
					echo '<script>
						Swal.fire({
							icon: "error",
							title: "Sistema PosDit",
							text: "¡Seleccione un estatus válido antes de guardar!",
							confirmButtonText: "Cerrar"
						});
					</script>';
					return;
				}

				// El catálogo cat_colonias no cubre todos los municipios; cuando el combo de
				// Colonia se queda sin opciones, el formulario deja escribirla a mano
				// (NuevaColoniaTexto) y aquí se da de alta esa colonia (o se reutiliza si ya
				// existe) para seguir guardando el hotel con un Id_Colonia real.
				$idColonia = $_POST["NuevaColonia"];
				if(empty($idColonia) && !empty($_POST["NuevaColoniaTexto"])){
					$coloniaNueva = ModeloHoteles::MdlInsertarColoniaSiNoExiste(
						$_POST["NuevoMunicipio"],
						trim($_POST["NuevaColoniaTexto"]),
						$_POST["CodigoPostal"] ?? ""
					);
					$idColonia = $coloniaNueva ? $coloniaNueva["Id_Colonia"] : "";
				}

				$hotel = new hotel(0,
							$_POST["nuevoRazonsocial"],
							$_POST["nuevoResponsable"],
							$_POST["telefono"],
							$_POST["NuevoCorreo"],
							$_POST["NuevoGiro"],
							$_POST["Calle"],
							$_POST["NuevoEstado"],
							$_POST["NuevoMunicipio"],
							$idColonia,
							$_POST["NuevoTipoPago"],
							0,
							0,
							$IdEstatus,
									0,
									0
								);

				$respuesta = ModeloHoteles::MdlInsertarHotel($hotel);

				if (is_array($respuesta) && isset($respuesta['error']) && $respuesta['error'] === true) {
					$errorMessage = isset($respuesta['message']) ? $respuesta['message'] : "¡Error interno al registrar el hotel, favor de intentar de nuevo!";
					echo'<script>
						Swal.fire({
							icon: "error",
							title : "Sistema PosDit",
							text: "' . addslashes($errorMessage) . '",
							showConfirmButton: true,
							confirmButtonText: "Cerrar"
						}).then(function(result){
							if (result.value) {
								window.location = "hoteles";
							}
						});
					</script>';
				} elseif (is_array($respuesta) && isset($respuesta["validar"]) && intval($respuesta["validar"]) === 0) {
					$moved = @move_uploaded_file($Contrato, $directorio);

					if(!empty($_FILES["nuevoLogo"]["tmp_name"])){
						self::ctrGuardarLogoHotel($respuesta["Id_Hotel"], $_FILES["nuevoLogo"]);
					}

					echo'<script>
						Swal.fire({
							icon: "success",
							title : "Sistema PosDit",
							text: "El Hotel ha sido guardado correctamente",
							showConfirmButton: true,
							confirmButtonText: "Cerrar"
						}).then(function(result){
							if (result.value) {
								window.location = "hoteles";
							}
						});
					</script>';
				} elseif (is_array($respuesta) && isset($respuesta["validar"]) && intval($respuesta["validar"]) === 1) {
					echo'<script>
						Swal.fire({
							icon: "error",
							title : "Sistema PosDit",
							text: "¡El Hotel ya existe, no se puede registrar duplicado!",
							showConfirmButton: true,
							confirmButtonText: "Cerrar"
						}).then(function(result){
							if (result.value) {
								window.location = "hoteles";
							}
						});
					</script>';
				} else {
					echo'<script>
						Swal.fire({
							icon: "error",
							title : "Sistema PosDit",
							text: "¡El Hotel ya existe o hubo un problema al registrar, favor de validar!",
							showConfirmButton: true,
							confirmButtonText: "Cerrar"
						}).then(function(result){
							if (result.value) {
								window.location = "hoteles";
							}
						});
					</script>';
				}

			} else {

				echo '<script>
					Swal.fire({
						title : "Sistema PosDit",
						text: "¡Favor de cargar el Contrato !",
						icon: "error",
						confirmButtonText: "¡Cerrar!"
					});
				</script>';

			}			

		} else {

			echo'<script>
				Swal.fire({
					icon: "error",
					title : "Sistema PosDit",
					text: "¡La categoría no puede ir vacía o llevar caracteres especiales!",
					showConfirmButton: true,
					confirmButtonText: "Cerrar"
				}).then(function(result){
					if (result.value) {
						window.location = "hoteles";
					}
				});
			</script>';

		}
	}
}

static public function ctrActualizarHotel()
{

		if(isset($_POST["editarRazonsocial"])){

			if(preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["editarRazonsocial"])){

			   	$idColonia = $_POST["editarColonia"];
			   	if(empty($idColonia) && !empty($_POST["editarColoniaTexto"])){
			   		$coloniaNueva = ModeloHoteles::MdlInsertarColoniaSiNoExiste(
			   			$_POST["editarMunicipio"],
			   			trim($_POST["editarColoniaTexto"]),
			   			$_POST["editarCodigoPostal"] ?? ""
			   		);
			   		$idColonia = $coloniaNueva ? $coloniaNueva["Id_Colonia"] : "";
			   	}

			   	$hotel = new hotel($_POST["editarIdHotel"],
			   							$_POST["editarRazonsocial"],
			   							$_POST["editarResponsable"],
			   							$_POST["editarTelefono"],
			   							$_POST["editarCorreo"],
			   							$_POST["editarGiro"],
			   							$_POST["editarCalle"],
			   							$_POST["editarEstado"],
			   							$_POST["editarMunicipio"],
			   							$idColonia,
			   							$_POST["editarTipoPago"],
			   							0,
			   							0,
			   							$_POST["editarEstatus"],
			   							$_POST["editarSAire"],
			   							$_POST["editarSServicios"]
			   							);

			   	$respuesta = ModeloHoteles::MdlActualizarHotel($hotel);

				if(!empty($_FILES["editarLogo"]["tmp_name"])){
					self::ctrGuardarLogoHotel($_POST["editarIdHotel"], $_FILES["editarLogo"]);
				}

				if($respuesta == "1"){

					echo'<script>

					Swal.fire({
						icon: "success",
						title : "Sistema PosDit",
						text: "El Hotel ha sido modificado correctamente",
					  }).then((result) => {
						window.location = "hoteles";
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

										window.location = "hoteles";

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

							window.location = "hoteles";

							}
						})

			  	</script>';

			}
		}
}


static public function crtObtenerHoteles(){

	$respuesta = ModeloHoteles::MdlObtenerHoteles();
	return $respuesta;

 }

static public function ctrEliminarHotel()
{
	if(isset($_POST["idEliminarHotel"])){

		$idHotel = intval($_POST["idEliminarHotel"]);
		$respuesta = ModeloHoteles::MdlEliminarHotel($idHotel);
		return $respuesta;

	}
}

static public function crtJsonObtenerTipoPago(){

	$respuesta = ModeloHoteles::MdlJsonObtenerTipoPago();
	return $respuesta;

 }


static public function crtJsonObtenerGiro(){

	$respuesta = ModeloHoteles::MdlJsonObtenerGiro();
	return $respuesta;

 }


 static public function crtJsonObtenerHoteles(){

	$respuesta = ModeloHoteles::MdlJsonObtenerHoteles();
	return $respuesta;

 }

 static public function crtJsonObtenerNegocioSesion($idNegocio){

	$respuesta = ModeloHoteles::MdlJsonObtenerNegocioSesion($idNegocio);
	return $respuesta;

 }

 // Habitaciones en estatus "Ocupado" del hotel ligado al negocio en sesión.
 // Se usa para refrescar el combo de Crear Venta en tiempo real (sin F5).
 static public function crtJsonObtenerHabitacionesOcupadas($idNegocio){

	$hotel = ModeloHoteles::MdlObtenerHotelPorNegocio($idNegocio);
	$idHotel = $hotel ? $hotel["Id_Hotel"] : null;

	if(!$idHotel){
		return [];
	}

	$respuesta = ModeloHoteles::MdlObtenerHabitacionesOcupadas($idHotel);
	return $respuesta;

 }

 static public function crtJsonObtenerEstatus(){

	$respuesta = ModeloHoteles::MdlJsonObtenerEstatus();
	return $respuesta;

 }

 static public function crtJsonObtenerEstados(){

	$respuesta = ModeloHoteles::MdlJsonObtenerEstados();
	return $respuesta;

 }

static public function crtObtenerHotelUsuario($valor){

	$respuesta = ModeloHoteles::MdlObtenerHotelUsuario($valor);
	return $respuesta;

 }
 
 static public function crtObtenerHotelUsuarioReporte($valor){

	$respuesta = ModeloHoteles::MdlObtenerHotelUsuarioReporte($valor);
	return $respuesta;

 }

 static public function crtObtenerNegocioUsuarioReporte($idUsuario){

	$respuesta = ModeloHoteles::MdlObtenerNegocioUsuarioReporte($idUsuario);
	return $respuesta;

 }

 private static function ctrGuardarLogoHotel($idHotel, $archivo){

	$tipo = $archivo["type"];
	$tamano = $archivo["size"];

	if($tipo !== "image/jpeg" && $tipo !== "image/png"){
		return false;
	}

	if($tamano > 3145728){
		return false;
	}

	$nombreImagen = uniqid() . "_" . $archivo["name"];
	$rutaWeb = "views/img/Logos_Hoteles/" . $nombreImagen;
	$dirFisico = dirname(__DIR__) . "/views/img/Logos_Hoteles/";

	if(!is_dir($dirFisico)){
		mkdir($dirFisico, 0755, true);
	}

	if(@move_uploaded_file($archivo["tmp_name"], $dirFisico . $nombreImagen)){
		ModeloHoteles::MdlActualizarLogoHotel($idHotel, $rutaWeb);
		return true;
	}

	return false;
 }

}

