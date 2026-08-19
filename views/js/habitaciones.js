
$('.tablaHabitaciones').DataTable({
	"ajax": "ajax/datatable-habitaciones.ajax.php",
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
});

/*=============================================
 Editar Habitación
 =============================================*/
$(document).on("click", ".btnEditarHabitacion", function(){

	var _idHabitacion = $(this).attr("idHabitacion");

	var datos = new FormData();
	datos.append("idHabitacion", _idHabitacion);

	$.ajax({
		url: "ajax/habitaciones.ajax.php",
		method: "POST",
		data: datos,
		cache: false,
		contentType: false,
		processData: false,
		dataType: "json",
		success: function(respuesta){

			$("#editarIdHabitacion").val(respuesta["Id_Habitacion"]);
			$("#editarNumero").val(respuesta["NumeroHabitacion"]);
			$("#editarTipo").val(respuesta["TipoHabitacion"]);
			$("#editarCapacidad").val(respuesta["Capacidad"]);
			$("#editarPrecio").val(formatearPrecio(respuesta["PrecioNoche"]));
			$("#editarDescripcion").val(respuesta["Descripcion"]);

			if (Number(respuesta["Id_Estatus"]) === 1) {
				$("#editarEstatus").bootstrapToggle("on");
			} else {
				$("#editarEstatus").bootstrapToggle("off");
			}

			$("#editarFotoActual").val(respuesta["Foto"]);
			$("#editarFotoAdvertencia").hide();

			if (respuesta["Foto"]) {
				$("#editarFotoPreview").attr("src", respuesta["Foto"]).show();
			} else {
				$("#editarFotoPreview").hide();
				$("#editarFotoAdvertencia")
					.html('<i class="fa fa-exclamation-triangle"></i> Esta habitación no tiene una foto registrada.')
					.show();
			}

			actualizarContador($("#editarTipo")[0], "contadorEditarTipo", 100);
			actualizarContador($("#editarDescripcion")[0], "contadorEditarDescripcion", 255);
		}
	});
});

/*=============================================
 Suspender Habitación
 =============================================*/
$(document).on("click", ".btnSuspenderHabitacion", function() {

	const idHabitacion = Number($(this).attr("idHabitacion"));
	const fila = $(this).closest("tr");

	if (!idHabitacion) {
		Swal.fire({
			icon: "error",
			title: "Error",
			text: "No se pudo identificar la habitación.",
			confirmButtonText: "Cerrar"
		});
		return;
	}

	Swal.fire({
		title: "Sistema PosDit",
		text: "¿Desea inhabilitar esta habitación?",
		icon: "warning",
		showCancelButton: true,
		confirmButtonColor: "#3085d6",
		cancelButtonColor: "#d33",
		confirmButtonText: "Sí, inhabilitar",
		cancelButtonText: "Cancelar"
	}).then((result) => {
		if (result.isConfirmed) {
			$.ajax({
				url: "ajax/suspender-habitacion.ajax.php",
				method: "POST",
				data: { idHabitacion: idHabitacion },
				dataType: "json",
				success: function(respuesta) {
					if (respuesta && respuesta.status === "success") {
						$('.tablaHabitaciones').DataTable().ajax.reload(null, false);

						Swal.fire({
							icon: "success",
							title: "Sistema PosDit",
							text: respuesta.message || "Habitación inhabilitada correctamente.",
							confirmButtonText: "Aceptar"
						});
					} else {
						Swal.fire({
							icon: "error",
							title: "Error",
							text: respuesta.message || "No se pudo inhabilitar la habitación.",
							confirmButtonText: "Cerrar"
						});
					}
				},
				error: function() {
					Swal.fire({
						icon: "error",
						title: "Error",
						text: "Error al inhabilitar la habitación",
						confirmButtonText: "Cerrar"
					});
				}
			});
		}
	});
});

/*=============================================
 Foto de la habitación no encontrada (ruta guardada
 pero el archivo ya no existe / no carga)
 =============================================*/
function mostrarErrorFotoHabitacion(){
	$("#editarFotoPreview").hide();
	$("#editarFotoAdvertencia")
		.html('<i class="fa fa-exclamation-triangle"></i> No se encontró la foto actual de esta habitación.')
		.show();
}

function formatearPrecio(valor){

	valor = String(valor).replace(/[^0-9.]/g, "");

	var primerPunto = valor.indexOf(".");
	if (primerPunto !== -1) {
		valor = valor.slice(0, primerPunto + 1) + valor.slice(primerPunto + 1).replace(/\./g, "");
	}

	var partes = valor.split(".");
	var entero = partes[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
	var decimal = partes.length > 1 ? "." + partes[1].slice(0, 2) : "";

	return entero + decimal;
}

function desformatearPrecio(valor){
	return String(valor).replace(/,/g, "");
}

$(document).on("input", "#nuevoPrecio, #editarPrecio", function(){
	this.value = formatearPrecio(this.value);
});

$(document).on("submit", "#modalAgregarHabitacion form, #modalEditarHabitacion form", function(){
	$(this).find('input[name="nuevoPrecio"], input[name="editarPrecio"]').each(function(){
		this.value = desformatearPrecio(this.value);
	});
});


function actualizarContador(campo, idContador, maximo){

	var longitud = campo.value.length;
	var contador = $("#" + idContador);

	if (longitud >= maximo) {
		contador.text("¡Has alcanzado el límite de " + maximo + " caracteres!");
		contador.removeClass("text-muted").addClass("text-danger");
	} else {
		contador.text(longitud + "/" + maximo + " caracteres");
		contador.removeClass("text-danger").addClass("text-muted");
	}
}

$(document).on("input", "#nuevaDescripcion, #editarDescripcion", function(){
	actualizarContador(this, this.id === "nuevaDescripcion" ? "contadorNuevaDescripcion" : "contadorEditarDescripcion", 255);
});

$(document).on("input", "#nuevoTipo, #editarTipo", function(){
	actualizarContador(this, this.id === "nuevoTipo" ? "contadorNuevoTipo" : "contadorEditarTipo", 100);
});
