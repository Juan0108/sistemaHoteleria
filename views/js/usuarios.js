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

 $(".btnEditarUsuario").click(function(){

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
 			CargarCbNegocios("editarCbnegocio", true, respuesta["id_negocio"])
 			$("#editarCalle").val(respuesta["Calle"]);
 			$("#editarCodigoPostal").val(respuesta["Codigo_Postal"]);
 			$("#editarUsuario").val(respuesta["usuario"]);

 		} 

 	})

 })

/*Llenado de combox aislado en una funcion para su reutilizacion
en modales de actualizacion*/
function CargarCbNegocios(select, seleccionar = false, valor = null){

	$.ajax({

		url: "ajax/combobox-negocios.ajax.php",
		dataType: "text",
		success: function(data){
			var jsonData = JSON.parse(data);
			for (var i = 0; i < jsonData.data.length; i++) {
			    var counter = jsonData.data[i];

			    $("#"+select).append(new Option(counter.Razon_Social,counter.Id_negocio));
			}

			if(seleccionar){
				$("#"+select).val(valor);
			}

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