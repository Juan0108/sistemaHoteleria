function mostrarCargaReserva(){
	$("#reservaOverlay").show();
}

function ocultarCargaReserva(){
	$("#reservaOverlay").hide();
}

// Persiste entre cambios de mes (la tabla se reemplaza por completo vía AJAX).
var reservaHabitacionSeleccionada = null;

function reservaAplicarEstadoFilas(){
	$(".reserva-fila").removeClass("reserva-fila-activa");

	if (reservaHabitacionSeleccionada !== null) {
		$(".reserva-fila[data-habitacion='" + reservaHabitacionSeleccionada + "']").addClass("reserva-fila-activa");
	}
}

function reservaCargarMes(mes, anio){

	mostrarCargaReserva();

	$.ajax({
		url: "ajax/reservas.ajax.php",
		method: "GET",
		data: { mes: mes, anio: anio },
		dataType: "json",
		success: function(respuesta){
			$("#reservaTabla").html(respuesta.tabla);
			$("#reservaTitulo").text(respuesta.titulo);
			$("#reservaPrev").data("mes", respuesta.mesAnterior).data("anio", respuesta.anioMesAnterior);
			$("#reservaNext").data("mes", respuesta.mesSiguiente).data("anio", respuesta.anioMesSiguiente);
			reservaAplicarEstadoFilas();
		},
		complete: ocultarCargaReserva
	});
}

$(document).ready(function(){

	if ($("#reservaTabla").length === 0) {
		return;
	}

	reservaAplicarEstadoFilas();

	$(document).on("click", "#reservaPrev", function(){
		reservaCargarMes($(this).data("mes"), $(this).data("anio"));
	});

	$(document).on("click", "#reservaNext", function(){
		reservaCargarMes($(this).data("mes"), $(this).data("anio"));
	});

	$(document).on("click", "#reservaHoy", function(){
		var hoy = new Date();
		reservaCargarMes(hoy.getMonth() + 1, hoy.getFullYear());
	});

	$(document).on("click", ".reserva-fila", function(){
		var idHabitacion = $(this).data("habitacion");

		if (!idHabitacion) {
			return;
		}

		reservaHabitacionSeleccionada = (reservaHabitacionSeleccionada === idHabitacion) ? null : idHabitacion;
		reservaAplicarEstadoFilas();
	});
});
