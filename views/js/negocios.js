/*=============================================
 Obtener Hoteles
 =============================================*/
$('.tablaHoteles').DataTable( {
    "ajax": "ajax/datatable-negocios.ajax.php",
    "deferRender": true,
	"retrieve": true,
	"processing": true,
	 "language": {

			"sProcessing":     "Procesando...",
			"sLengthMenu":     "Mostrar _MENU_ registros",
			"sZeroRecords":    "No se encontraron resultados",
			"sEmptyTable":     "Ningún dato disponible en esta tabla",
			"sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_",
			"sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0",
			"sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
			"sInfoPostFix":    "",
			"sSearch":         "Buscar:",
			"sUrl":            "",
			"sInfoThousands":  ",",
			"sLoadingRecords": "Cargando...",
			"oPaginate": {
			"sFirst":    "Primero",
			"sLast":     "Último",
			"sNext":     "Siguiente",
			"sPrevious": "Anterior"
			},
			"oAria": {
				"sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
				"sSortDescending": ": Activar para ordenar la columna de manera descendente"
			}

	}

} )

//Money Euro
$('[data-mask]').inputmask()

//Timepicker
$('.timepicker').timepicker({
   showInputs: false
})


$(".nuevoContrato").change(function(){

	var imagen = this.files[0];
	
	/*=============================================
  	VALIDAMOS EL FORMATO DE LA CartaResponsiva sea PDF
  	=============================================*/

  	if(imagen["type"] != "application/pdf" ){

  		$(".nuevoContrato").val("");

		  Swal.fire({
			  title: "Sistema PosDit",
		      text: "¡El Contrato debe estar en formato PDF!",
		      icon: "error",
		      confirmButtonText: "¡Cerrar!"
		    });

  	}else if(imagen["size"] > 3145728){

  		$(".nuevoContrato").val("");

  		 Swal.fire({
			  title: "Sistema PosDit",
		      text: "¡Error al subir el Contrato, No debe pesar más de 3MB!",
		      icon: "error",
		      confirmButtonText: "¡Cerrar!"
		    });

  	}
})

/*=============================================
 Obtener Tipo de Pago  y Giro Combobox Agregar
 =============================================*/
 $(document).on("click", ".btnEditarHotel", function(){

 	var _idHotel = $(this).attr("data-id-Hotel");

 	var datos = new FormData();
 	datos.append("idHotel", _idHotel);

 	$.ajax({

 		url:"ajax/negocios.ajax.php",
 		method: "POST",
 		data: datos,
 		cache: false,
 		contentType: false,
 		processData: false,
 		dataType: "json",
 		success: function(respuesta){

 			$("#editarIdHotel").val(_idHotel);
 			$("#editarRazonSocial").val(respuesta["Razon_Social"]);
 			$("#editarResponsable").val(respuesta["Responsable"]);
 			$("#editarTelefono").val(respuesta["Telefono"]);
 			CargarCbEstados("editarCbestado", true, respuesta["Id_Estado"]);
 			CargarCbMunicipio(respuesta["Id_Estado"], "editarCbmunicipio", true, respuesta["Id_Municipio"]);
 			CargarCbColonia(respuesta["Id_Municipio"], "editarCbcolonia", true, respuesta["Id_Colonia"]);
 			$("#editarCalle").val(respuesta["Calle"]);
 			$("#editarCodigoPostal").val(respuesta["Codigo_Postal"]);
 			CargarCbTipoPago("editarCbtipopago", true, respuesta["Id_TipoPago"]);
 			$("#editarCorreo").val(respuesta["Correo"]);
 			CargarCbGiro("editarCbgiro", true, respuesta["Id_Giro"]);
 			CargarCbEstatus("editarCbestatus", true, respuesta["Id_Estatus"]);
 			$("#editarSAire").val(respuesta["SAire"]);
 			$("#editarSServicios").val(respuesta["SServicios"]);

 		} 

 	});

 });

/*=============================================
 Eliminar (dar de baja) Hotel
 =============================================*/
$(document).on("click", ".btnEliminarHotel", function(){

	var _idHotel = $(this).attr("data-id-hotel");

	Swal.fire({
		title: "Sistema PosDit",
		text: "¿Está seguro de dar de baja este hotel?",
		icon: "warning",
		showCancelButton: true,
		confirmButtonText: "Sí, dar de baja",
		cancelButtonText: "Cancelar"
	}).then(function(result){

		if(result.value){

			var datos = new FormData();
			datos.append("idEliminarHotel", _idHotel);

			$.ajax({

				url: "ajax/negocios.ajax.php",
				method: "POST",
				data: datos,
				cache: false,
				contentType: false,
				processData: false,
				dataType: "text",
				success: function(respuesta){

					if(respuesta.trim() == "ok"){

						Swal.fire({
							icon: "success",
							title: "Sistema PosDit",
							text: "El Hotel ha sido dado de baja correctamente"
						}).then(function(){
							$('.tablaHoteles').DataTable().ajax.reload(null, false);
						});

					}else{

						Swal.fire({
							icon: "error",
							title: "Sistema PosDit",
							text: "¡Error al dar de baja el Hotel, favor de intentar de nuevo!"
						});

					}

				}

			});

		}

	});

});

$(document).ready(function(){

	 CargarCbTipoPago("cbtipopago");

	 CargarCbGiro("cbgiro");

	 CargarCbEstatus("cbestatus");

})

/*=============================================
 Combobox Direcciones
 =============================================*/
$(document).ready(function(){

	 CargarCbEstados("cbestado");

//Combobox Estados 
 $('#cbestado').on('change', function(){
 	
 	$("#cbmunicipio").empty().append('<option value="" selected hidden>--Seleccionar Municipio--</option>');
 	$("#cbcolonia").empty().append('<option value="" selected hidden>--Seleccionar Colonia--</option>');
 	$("#CodigoPostal").val('');

    var id = $('#cbestado').val();
    
    CargarCbMunicipio(id, "cbmunicipio");

  })

//Combobox Municipios 
 $('#cbmunicipio').on('change', function(){
 	
 	$("#cbcolonia").empty().append('<option value="" selected hidden>--Seleccionar Colonia--</option>');
 	$("#CodigoPostal").val('');

    var id = $('#cbmunicipio').val()
    
    CargarCbColonia(id, "cbcolonia");

  })

//Combobox Municipios 
 $('#cbcolonia').on('change', function(){
 
    var id = $('#cbcolonia').val()
 	var datos = new FormData();
 	datos.append("id",id);

 	CargarCodigoPostal(datos, "CodigoPostal");

  })

//DISPARADORES DE EVENTO DE COMBOBOX EN MODAL ACTUALIZAR

 //Editar Combobox Estados 
 $('#editarCbestado').on('change', function(){
 	
 	$("#editarCbmunicipio").empty().append('<option value="" selected hidden>--Seleccionar Municipio--</option>');
 	$("#editarCbcolonia").empty().append('<option value="" selected hidden>--Seleccionar Colonia--</option>');
 	$("#editarCodigoPostal").val('');

    var id = $('#editarCbestado').val();
    
    CargarCbMunicipio(id, "editarCbmunicipio");

  })

//Combobox Municipios 
 $('#editarCbmunicipio').on('change', function(){
 	
 	$("#editarCbcolonia").empty().append('<option value="" selected hidden>--Seleccionar Colonia--</option>');
 	$("#editarCodigoPostal").val('');

    var id = $('#editarCbmunicipio').val()
    
    CargarCbColonia(id, "editarCbcolonia");

  })

//Combobox Municipios 
 $('#editarCbcolonia').on('change', function(){
 
    var id = $('#editarCbcolonia').val()
 	var datos = new FormData();
 	datos.append("id",id);

 	CargarCodigoPostal(datos, "editarCodigoPostal");

  })

})

/*Llenado de combox aislado en una funcion para su reutilizacion
en modales de actualizacion*/
function CargarCbTipoPago(select, seleccionar = false, valor = null){

	$.ajax({

		url: "ajax/combobox-tipopago.ajax.php",
		dataType: "text",
		success: function(data){
			var jsonData = JSON.parse(data);
			for (var i = 0; i < jsonData.data.length; i++) {
			    var counter = jsonData.data[i];
			    var id = counter.Id_TipoPago ?? counter.Id_tipopago ?? counter.id_tipopago ?? counter.Id_TipoPago;

			    $("#"+select).append(new Option(counter.Nombre, id));
			}

			if(seleccionar){
				$("#"+select).val(valor);
			}

		}
	})

}

/*Llenado de combox aislado en una funcion para su reutilizacion
en modales de actualizacion*/
function CargarCbGiro(select, seleccionar = false, valor = null){

	$.ajax({

		url: "ajax/combobox-giro.ajax.php",
		dataType: "text",
		success: function(data){
			var jsonData = JSON.parse(data);
			for (var i = 0; i < jsonData.data.length; i++) {
			    var counter = jsonData.data[i];

			    $("#"+select).append(new Option(counter.Nombre,counter.Id_Giro));
			}

			if(seleccionar){
				$("#"+select).val(valor);
			}


		}
	})

}

function CargarCbEstatus(select, seleccionar = false, valor = null){

	$.ajax({

		url: "ajax/combobox-estatus.ajax.php",
		dataType: "text",
		success: function(data){
			var jsonData = JSON.parse(data);
			for (var i = 0; i < jsonData.data.length; i++) {
			    var counter = jsonData.data[i];
			    var id = counter.Id_Estatus ?? counter.Id_estatus ?? counter.id_estatus ?? counter.Id_Estatus;

			    $("#"+select).append(new Option(counter.Nombre, id));
			}

			if(seleccionar){
				$("#"+select).val(valor);
			}

		}
	})

}

/*Llenado de combox aislado en una funcion para su reutilizacion
en modales de actualizacion*/
function CargarCbEstados(select, seleccionar = false, valor = null){

	$.ajax({

		url: "ajax/combobox-estados.ajax.php",
		dataType: "text",
		success: function(data){
		var jsonData = JSON.parse(data);

			// Limpiamos el combo antes de llenarlo para no acumular opciones duplicadas
			// cada vez que se abre el modal de edición.
			$("#"+select).empty();

			for (var i = 0; i < jsonData.data.length; i++) {
			    var counter = jsonData.data[i];

			    $("#"+select).append(new Option(counter.Nombre,counter.Id_Estado));
			}

			if(seleccionar){
				$("#"+select).val(valor);
			}
		}
	})

}

/*Llenado de combox aislado en una funcion para su reutilizacion
en modales de actualizacion*/
function CargarCbMunicipio(id, select, seleccionar = false, valor = null){

	$.ajax({
      type: 'POST',
      url: 'ajax/combobox-filtro-municipios.ajax.php',
      data: {'id': id}
    })
    .done(function(Municipios){

		var jsonData = JSON.parse(Municipios);

		// Limpiamos el combo antes de llenarlo para no acumular opciones duplicadas
		// cada vez que se abre el modal de edición.
		$("#"+select).empty();

		for (var i = 0; i < jsonData.Municipios.length; i++) {
		    var counter = jsonData.Municipios[i];

		    $("#"+select).append(new Option(counter.Nombre,counter.Id_Municipio));
		}

		if(seleccionar){
			$("#"+select).val(valor);
		}

    })

}

/*Llenado de combox aislado en una funcion para su reutilizacion
en modales de actualizacion*/
function CargarCbColonia(id, select, seleccionar = false, valor = null){

	$.ajax({
      type: 'POST',
      url: 'ajax/combobox-filtro-colonias.ajax.php',
      data: {'id': id}
    })
    .done(function(Colonias){
      
		var jsonData = (typeof Colonias === 'string') ? JSON.parse(Colonias) : Colonias;
		
		// Verificamos si los datos vienen directamente en un array o dentro de jsonData.Colonias
		var lista = jsonData.Colonias || jsonData.data || jsonData;

		if(Array.isArray(lista) && lista.length > 0){

			// Limpiamos el combo antes de llenarlo para no acumular opciones duplicadas
			// cada vez que se abre el modal de edición.
			$("#"+select).empty();

			for (var i = 0; i < lista.length; i++) {
			    var counter = lista[i];


			    $("#"+select).append(new Option(counter.Nombre, counter.Id_Colonia));
			}

			if(seleccionar){
				$("#"+select).val(valor);
			}
		} else {
			console.warn("No se devolvieron colonias para el municipio con ID:", id);
		}
    })
    .fail(function(err){
        console.error("Error en la petición AJAX de colonias:", err);
    });

}

/*Llenado de combox aislado en una funcion para su reutilizacion
en modales de actualizacion*/
function CargarCodigoPostal(datos, campo){

	$.ajax({

 		url:"ajax/codigpostal.ajax.php",
 		method: "POST",
 		data: datos,
 		cache: false,
 		contentType: false,
 		processData: false,
 		dataType: "json",
 		success: function(respuesta){

 			$("#"+campo).val(respuesta["Codigo_Postal"]);

 		} 

 	})

}

/*=============================================
 Obtener Id´s 
 =============================================*/
function GetvalueGiro(){

	$("#NuevoGiro").val($('#cbgiro').val());

}

function GetvalueEstado(){

	$("#NuevoEstado").val($('#cbestado').val());

}

function GetvalueMunicipio(){

	$("#NuevoMunicipio").val($('#cbmunicipio').val());

}

function GetvalueColonia(){

	$("#NuevaColonia").val($('#cbcolonia').val());

}

function GetvalueTipoPago(){

	$("#NuevoTipoPago").val($('#cbtipopago').val());

}

function GetvalueEstatus(){

	$("#NuevoEstatus").val($('#cbestatus').val());

}