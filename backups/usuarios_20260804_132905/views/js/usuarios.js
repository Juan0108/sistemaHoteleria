/*=============================================
 Obtener Tipo de Pago  y Giro Combobox Agregar
 =============================================*/
$(document).ready(function(){

	CargarCbNegocios("cbnegocio");

})


$(".nuevaFoto").change(function(){

	var imagen = this.files[0];
	
	/*=============================================
  	VALIDAMOS EL FORMATO DE LA IMAGEN SEA JPG O PNG
  	=============================================*/

  	if(imagen["type"] != "image/jpeg" && imagen["type"] != "image/png"){

  		$(".nuevaFoto").val("");

  		 Swal.fire({
			  title : "Sistema PosDit",
		      text: "¡Error al subir la imagen, La imagen debe estar en formato JPG o PNG!",
		      icon: "error",
		      confirmButtonText: "¡Cerrar!"
		    });

  	}else if(imagen["size"] > 3145728){

  		$(".nuevaFoto").val("");

  		 Swal.fire({
			  title : "Sistema PosDit",
		      text: "¡Error al subir la imagen, La imagen no debe pesar más de 3MB!",
		      icon: "error",
		      confirmButtonText: "¡Cerrar!"
		    });

  	}
})

/*=============================================
 Edita Usuario
 =============================================*/
$(document).on("click", ".btnEditarUsuario", function(){

 	var _idUsuario = $(this).attr("idUsuario");

 	var datos = new FormData();
 	datos.append("idUsuario",_idUsuario);

 	$.ajax({

 		url:"ajax/usuarios.ajax.php",
 		method: "POST",
 		data: datos,
 		cache: false,
 		contentType: false,
 		processData: false,
 		dataType: "json",
 		success: function(respuesta){

 			$("#editarNombre").val(respuesta["Nombre"]);
 			$("#editarApaterno").val(respuesta["Apaterno"]);
 			$("#editarAmaterno").val(respuesta["Amaterno"]);
 			$("#editarPerfil").val(respuesta["IdPerfil"]);
 			CargarCbEstados("editarCbestado", true, respuesta["IdEstado"]);
 			CargarCbMunicipio(respuesta["IdEstado"], "editarCbmunicipio", true, respuesta["IdMunicipio"])
 			CargarCbColonia(respuesta["IdMunicipio"], "editarCbcolonia", true, respuesta["IdColonia"])
 			CargarCbNegocios("editarCbnegocio", true, respuesta["id_negocio"], respuesta["Razon_Social"])
 			$("#editarCalle").val(respuesta["Calle"]);
 			$("#editarCodigoPostal").val(respuesta["Codigo_Postal"]);
 			$("#editarUsuario").val(respuesta["usuario"]);

 		} 

 	})

  });
/*Llenado de combox aislado en una funcion para su reutilizacion
en modales de actualizacion*/
function CargarCbNegocios(select, seleccionar = false, valor = null, nombreActual = null){

	$.ajax({
		// Solo trae el negocio del usuario en sesión (el backend lo determina por
		// $_SESSION, no acepta un id por parámetro), así el combo nunca puede
		// mostrar ni dejar elegir otro negocio/hotel.
		url: "ajax/combobox-negocio-sesion.ajax.php",
		dataType: "json", // Cambiado a JSON para que jQuery valide la respuesta automáticamente
		success: function(jsonData){

			// Limpiamos el combo antes de llenarlo para no acumular opciones duplicadas
			// cada vez que se abre el modal de edición.
			$("#"+select).empty();

			if (jsonData && jsonData.data) {
				for (var i = 0; i < jsonData.data.length; i++) {
				    var counter = jsonData.data[i];

				    // Soportar ambas propiedades (Razon_Social / Id_Hotel o Id_negocio)
				    var id = counter.Id_Hotel ?? counter.Id_negocio ?? counter.id_negocio;
				    var nombre = counter.Razon_Social ?? counter.Razon_social;

				    $("#"+select).append(new Option(nombre, id));
				}
			}

			// El negocio asignado al usuario puede estar dado de baja, en cuyo caso
			// no viene en la lista de negocios activos y el combo quedaría en blanco.
			// Lo agregamos igual para que se muestre seleccionado.
			if(seleccionar && valor !== null && valor !== undefined && $("#"+select+" option[value='"+valor+"']").length === 0){
				$("#"+select).append(new Option(nombreActual ? nombreActual+" (inactivo)" : "Negocio inactivo", valor));
			}

			if(seleccionar){
				$("#"+select).val(valor);
			}
		},
		error: function(xhr, status, error) {
			console.error("Error cargando combobox negocios/hoteles:", xhr.responseText);
		}
	})

}

 function GetvalueNegocios(){

	$("#NuevoNegocio").val($('#cbnegocio').val());

}


//Nueva Funcion
$("#btnGuardar").click(function(event) {
    const form = $("#resetForm");
    const password = $("#password");
    const confirmPassword = $("#confirmPassword");
    const errorPassword = $("#errorPassword");
    const errorConfirmPassword = $("#errorConfirmPassword");

    let isValid = true;

    // Validar que la contraseña tenga más de 8 caracteres
    if (password.val().trim().length < 8) {
        errorPassword.text("La contraseña debe tener más de 8 caracteres.");
        isValid = false;
    } else {
        errorPassword.text("");
    }

    // Validar que las contraseñas coincidan
    if (password.val() !== confirmPassword.val()) {
        errorConfirmPassword.text("Las contraseñas no coinciden.");
        isValid = false;
    } else {
        errorConfirmPassword.text("");
    }

    // Detener el envío del formulario si hay errores
    if (!isValid) {
        event.preventDefault(); // Evita que el formulario se envíe
    }
});
/*=============================================
 Resetear Contraseña
 =============================================*/
$(document).on("click", ".btnReset", function() {
    const idUsuario = $(this).attr("idUsuario");
    const nombreUsuario = $(this).closest("tr").find("td").eq(1).text().trim();
	const defaultPassword = "Soporte123";
	console.log("ID del usuario a resetear:", idUsuario);

    Swal.fire({
        title: "Sistema PosDit",
        text: `¿Desea resetear la contraseña del usuario ${nombreUsuario}?`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Sí, resetear",
        cancelButtonText: "Cancelar"
    })
	.then((result) => {
		if (result.isConfirmed) {
			$.ajax({
				url: "ajax/reset-contra.ajax.php",
				method: "POST",
				data: {
					idUsuario: idUsuario,
					newPassword: defaultPassword
				},
				dataType: "json",
				success: function(respuesta) {
					console.log("Respuesta del servidor:", respuesta);

					if (respuesta && respuesta.status === "success") {
						Swal.fire({
							icon: "success",
							title: "Sistema PosDit",
							text: respuesta.message || "La contraseña ha sido resetada correctamente.",
							confirmButtonText: "Aceptar"
						}).then(() => {
							location.reload();
						});
					} else {
						Swal.fire({
							icon: "error",
							title: "Error",
							text: respuesta.message || "No se pudo resetear la contraseña.",
							confirmButtonText: "Cerrar"
						});
					}
				},
				error: function(xhr) {
					console.log("Error al resetear la contraseña para usuario:", idUsuario);
					let mensaje = "Error al resetear la contraseña";

					try {
						const errorResponse = JSON.parse(xhr.responseText);
						if (errorResponse && errorResponse.message) {
							mensaje = errorResponse.message;
						}
					} catch (e) {
						console.log("No se pudo leer la respuesta de error del servidor.", e);
					}

					Swal.fire({
						icon: "error",
						title: "Error",
						text: mensaje,
						confirmButtonText: "Cerrar"
					});
				}
			});
			
		} else {
			Swal.fire({
				title: "Sistema PosDit",
				text: "¡Cancelado!",
				icon: "error",
				confirmButtonText: "¡Cerrar!"
			});
		}
	})
});
/*=============================================
 Suspender Usuario
 =============================================*/
$(document).on("click", ".btnSuspender", function() {

	const idUsuario = Number($(this).attr("idUsuario"));
	const nombreUsuario = $(this).closest("tr").find("td").eq(1).text().trim();
	const fila = $(this).closest("tr");

	if (!idUsuario) {
		Swal.fire({
			icon: "error",
			title: "Error",
			text: "No se pudo identificar al usuario.",
			confirmButtonText: "Cerrar"
		});
		return;
	}

	Swal.fire({
		title: "Sistema PosDit",
		text: `¿Desea inhabilitar al usuario ${nombreUsuario}?`,
		icon: "warning",
		showCancelButton: true,
		confirmButtonColor: "#3085d6",
		cancelButtonColor: "#d33",
		confirmButtonText: "Sí, inhabilitar",
		cancelButtonText: "Cancelar"
	}).then((result) => {
		if (result.isConfirmed) {
			$.ajax({
				url: "ajax/suspender.ajax.php",
				method: "POST",
				data: {
					idUsuario: idUsuario,
					id: idUsuario
				},
				dataType: "json",
				success: function(respuesta) {
					if (respuesta && respuesta.status === "success") {
						fila.find("td").eq(4).html('<button class="btn btn-warning btn-xs"><i class="fa fa-frown-o"></i></button> <strong>Inhabilitado</strong>');
						fila.find(".btnEditarUsuario, .btnReset, .btnSuspender").prop("disabled", true);
						Swal.fire({
							icon: "success",
							title: "Sistema PosDit",
							text: respuesta.message || "Usuario inhabilitado correctamente.",
							confirmButtonText: "Aceptar"
						}).then(() => {
							location.reload();
						});
					} else {
						Swal.fire({
							icon: "error",
							title: "Error",
							text: respuesta.message || "No se pudo inhabilitar al usuario.",
							confirmButtonText: "Cerrar"
						});
					}
				},
				error: function(xhr) {
					let mensaje = "Error al inhabilitar al usuario";

					try {
						const errorResponse = JSON.parse(xhr.responseText);
						if (errorResponse && errorResponse.message) {
							mensaje = errorResponse.message;
						}
					} catch (e) {
						console.log("No se pudo leer la respuesta de error del servidor.", e);
					}

					Swal.fire({
						icon: "error",
						title: "Error",
						text: mensaje,
						confirmButtonText: "Cerrar"
					});
				}
			});
		} else {
			Swal.fire({
				title: "Sistema PosDit",
				text: "¡Cancelado!",
				icon: "error",
				confirmButtonText: "¡Cerrar!"
			});
		}
	});
});
