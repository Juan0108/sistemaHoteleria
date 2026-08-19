/*=============================================
 Obtener Tipo de Pago  y Giro Combobox Agregar
 =============================================*/
$(document).ready(function(){

	CargarCbNegocios("cbnegocio");

})

/*=============================================
 Buscador y filtros de la tabla de Gestión de Usuarios
 (reutiliza el DataTable ya inicializado en plantilla.js)
 =============================================*/
$(document).ready(function(){

	var $tabla = $("#tablaUsuariosHotel");
	if ($tabla.length === 0) return;

	var tabla = $tabla.DataTable();
	var COL_ROL = 4;
	var COL_ESTADO = 5;

	$("#buscarUsuario").on("keyup", function(){
		tabla.search(this.value).draw();
	});

	$("#filtroRol").on("change", function(){
		var valor = $.fn.dataTable.util.escapeRegex(this.value);
		tabla.column(COL_ROL).search(valor ? "^" + valor + "$" : "", true, false).draw();
	});

	$("#filtroEstado").on("change", function(){
		var valor = $.fn.dataTable.util.escapeRegex(this.value);
		tabla.column(COL_ESTADO).search(valor ? "^" + valor + "$" : "", true, false).draw();
	});

	// Reemplaza el texto largo de DataTables ("Mostrando registros...")
	// por un conteo simple, solo en esta tabla.
	function actualizarInfoUsuarios(){
		var total = tabla.page.info().recordsDisplay;
		$tabla.closest(".dataTables_wrapper").find(".dataTables_info")
			.text(total + (total == 1 ? " usuario" : " usuarios"));
	}
	tabla.on("draw", actualizarInfoUsuarios);
	actualizarInfoUsuarios();

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
 Cambiar/agregar la foto del usuario desde el modal de edición
 (el recuadro es un <label for="editarFoto">, así que el navegador
 abre el explorador de archivos de forma nativa al hacer clic,
 sin depender de un trigger por JS)
 =============================================*/
$(document).on("change", "#editarFoto", function(){

	var imagen = this.files[0];

	if(!imagen) return;

	if(imagen["type"] != "image/jpeg" && imagen["type"] != "image/png"){

		$("#editarFoto").val("");

		Swal.fire({
			title : "Sistema PosDit",
			text: "¡Error al subir la imagen, La imagen debe estar en formato JPG o PNG!",
			icon: "error",
			confirmButtonText: "¡Cerrar!"
		});

	}else if(imagen["size"] > 3145728){

		$("#editarFoto").val("");

		Swal.fire({
			title : "Sistema PosDit",
			text: "¡Error al subir la imagen, La imagen no debe pesar más de 3MB!",
			icon: "error",
			confirmButtonText: "¡Cerrar!"
		});

	}else{

		var lector = new FileReader();
		lector.onload = function(e){
			$("#editarFotoPreview").attr("src", e.target.result).show();
			$("#editarFotoPlaceholder").hide();
		};
		lector.readAsDataURL(imagen);

	}
});

/*=============================================
 Edita Usuario
 =============================================*/
$(document).on("click", ".btnEditarUsuario", function(){

 	var _idUsuario = $(this).attr("idUsuario");

 	// Limpiamos cualquier foto seleccionada en una edición anterior antes de
 	// cargar los datos del usuario que se va a editar ahora.
 	$("#editarFoto").val("");

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
 			$("#editarCorreo").val(respuesta["Correo"]);
 			$("#editarPerfil").val(respuesta["IdPerfil"]);
 			CargarCbEstados("editarCbestado", true, respuesta["IdEstado"]);
 			CargarCbMunicipio(respuesta["IdEstado"], "editarCbmunicipio", true, respuesta["IdMunicipio"])
 			CargarCbColonia(respuesta["IdMunicipio"], "editarCbcolonia", true, respuesta["IdColonia"])
 			CargarCbNegocios("editarCbnegocio", true, respuesta["id_negocio"], respuesta["Razon_Social"])
 			$("#editarCalle").val(respuesta["Calle"]);
 			$("#editarCodigoPostal").val(respuesta["Codigo_Postal"]);
 			$("#editarUsuario").val(respuesta["usuario"]);

 			if(respuesta["foto"]){
 				$("#editarFotoPreview").attr("src", respuesta["foto"]).show();
 				$("#editarFotoPlaceholder").hide();
 			}else{
 				$("#editarFotoPreview").attr("src", "").hide();
 				$("#editarFotoPlaceholder").show();
 			}

 		}

 	})

  });

/*=============================================
 Guardar edición de Usuario por AJAX (evita el parpadeo de la página
 completa sin estilos que se veía al enviar el formulario normal)
 =============================================*/
$(document).on("submit", "#formEditarUsuario", function(e){

	e.preventDefault();

	var datos = new FormData(this);

	$.ajax({

		url: "ajax/actualizar-usuario.ajax.php",
		method: "POST",
		data: datos,
		cache: false,
		contentType: false,
		processData: false,
		dataType: "json",
		success: function(respuesta){

			if (respuesta && respuesta.status === "success") {
				Swal.fire({
					icon: "success",
					title: "Sistema PosDit",
					text: respuesta.message || "¡El personal ha sido modificado correctamente!",
					confirmButtonText: "Cerrar"
				}).then(function(){
					location.reload();
				});
			} else {
				Swal.fire({
					icon: "error",
					title: "Sistema PosDit",
					text: (respuesta && respuesta.message) || "No se pudo modificar el personal.",
					confirmButtonText: "Cerrar"
				});
			}

		},
		error: function(xhr){

			let mensaje = "Error al modificar el personal";

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

});

/*Llenado de combox aislado en una funcion para su reutilizacion
en modales de actualizacion*/
function CargarCbNegocios(select, seleccionar = false, valor = null, nombreActual = null){

	$.ajax({
		// Solo trae el negocio del usuario en sesión (el backend lo determina por
		// $_SESSION, no acepta un id por parámetro), así el combo nunca puede
		// mostrar ni dejar elegir otro negocio/hotel.
		url: "ajax/combobox-hotel-sesion.ajax.php",
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
        text: `¿Desea resetear la contraseña del personal ${nombreUsuario}?`,
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
			text: "No se pudo identificar al personal.",
			confirmButtonText: "Cerrar"
		});
		return;
	}

	Swal.fire({
		title: "Sistema PosDit",
		text: `¿Desea inhabilitar al personal ${nombreUsuario}?`,
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
						fila.find("td").eq(5).html('<span class="gu-badge-estado gu-badge-inactivo">Inactivo</span>');
						fila.find(".btnEditarUsuario, .btnReset, .btnSuspender").prop("disabled", true);
						Swal.fire({
							icon: "success",
							title: "Sistema PosDit",
							text: respuesta.message || "Personal inhabilitado correctamente.",
							confirmButtonText: "Aceptar"
						}).then(() => {
							location.reload();
						});
					} else {
						Swal.fire({
							icon: "error",
							title: "Error",
							text: respuesta.message || "No se pudo inhabilitar al personal.",
							confirmButtonText: "Cerrar"
						});
					}
				},
				error: function(xhr) {
					let mensaje = "Error al inhabilitar al personal";

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
/*=============================================
 Activar Usuario
 =============================================*/
$(document).on("click", ".btnActivar", function() {

	const idUsuario = Number($(this).attr("idUsuario"));
	const nombreUsuario = $(this).closest("tr").find("td").eq(1).text().trim();
	const fila = $(this).closest("tr");

	if (!idUsuario) {
		Swal.fire({
			icon: "error",
			title: "Error",
			text: "No se pudo identificar al personal.",
			confirmButtonText: "Cerrar"
		});
		return;
	}

	Swal.fire({
		title: "Sistema PosDit",
		text: `¿Desea activar al personal ${nombreUsuario}?`,
		icon: "warning",
		showCancelButton: true,
		confirmButtonColor: "#3085d6",
		cancelButtonColor: "#d33",
		confirmButtonText: "Sí, activar",
		cancelButtonText: "Cancelar"
	}).then((result) => {
		if (result.isConfirmed) {
			$.ajax({
				url: "ajax/activar.ajax.php",
				method: "POST",
				data: {
					idUsuario: idUsuario,
					id: idUsuario
				},
				dataType: "json",
				success: function(respuesta) {
					if (respuesta && respuesta.status === "success") {
						fila.find("td").eq(5).html('<span class="gu-badge-estado gu-badge-activo">Activo</span>');
						Swal.fire({
							icon: "success",
							title: "Sistema PosDit",
							text: respuesta.message || "Personal activado correctamente.",
							confirmButtonText: "Aceptar"
						}).then(() => {
							location.reload();
						});
					} else {
						Swal.fire({
							icon: "error",
							title: "Error",
							text: respuesta.message || "No se pudo activar al personal.",
							confirmButtonText: "Cerrar"
						});
					}
				},
				error: function(xhr) {
					let mensaje = "Error al activar al personal";

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
