function mostrarCargaReserva(){
	$("#reservaOverlay").show();
}

function ocultarCargaReserva(){
	$("#reservaOverlay").hide();
}

// Ambas persisten entre cambios de mes (la tabla se reemplaza por completo vía AJAX).
var reservaHabitacionSeleccionada = null;
var reservaFiltroDisponibles = null; // null = sin filtro activo; array = ids disponibles

function reservaAplicarEstadoFilas(){
	$(".reserva-fila").removeClass("reserva-fila-activa reserva-fila-disponible reserva-fila-descartada");

	if (reservaHabitacionSeleccionada !== null) {
		$(".reserva-fila[data-habitacion='" + reservaHabitacionSeleccionada + "']").addClass("reserva-fila-activa");
	}

	if (reservaFiltroDisponibles !== null) {
		$(".reserva-fila").each(function(){
			var idHabitacion = parseInt($(this).data("habitacion"), 10);
			var disponible = reservaFiltroDisponibles.indexOf(idHabitacion) !== -1;
			$(this).toggleClass("reserva-fila-disponible", disponible);
			$(this).toggleClass("reserva-fila-descartada", !disponible);
		});
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

function reservaBuscarDisponibilidad(){

	var fechaInicio = $("#reservaFiltroInicio").val();
	var fechaFin = $("#reservaFiltroFin").val();

	if (!fechaInicio || !fechaFin) {
		$("#reservaFiltroMensaje").text("Selecciona ambas fechas.");
		return;
	}

	if (fechaFin < fechaInicio) {
		$("#reservaFiltroMensaje").text("La fecha final debe ser posterior a la inicial.");
		return;
	}

	mostrarCargaReserva();
	$("#reservaFiltroMensaje").text("Buscando...");

	$.ajax({
		url: "ajax/reservaciones.ajax.php",
		method: "GET",
		data: { accion: "disponibilidad", fecha_inicio: fechaInicio, fecha_fin: fechaFin },
		dataType: "json",
		success: function(respuesta){
			if (!respuesta.ok) {
				$("#reservaFiltroMensaje").text(respuesta.mensaje || "No se pudo buscar disponibilidad.");
				return;
			}

			reservaFiltroDisponibles = respuesta.habitaciones.map(function(h){ return parseInt(h.id, 10); });
			$("#reservaFiltroLimpiar").show();
			$("#reservaFiltroMensaje").text(reservaFiltroDisponibles.length + " habitación(es) disponible(s) en ese rango.");
			reservaAplicarEstadoFilas();
		},
		error: function(){
			$("#reservaFiltroMensaje").text("Error al buscar disponibilidad.");
		},
		complete: ocultarCargaReserva
	});
}

function reservaLimpiarFiltro(){
	reservaFiltroDisponibles = null;
	$("#reservaFiltroInicio").val("");
	$("#reservaFiltroFin").val("");
	$("#reservaFiltroMensaje").text("");
	$("#reservaFiltroLimpiar").hide();
	reservaAplicarEstadoFilas();
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

	$(document).on("click", "#reservaFiltroBuscar", reservaBuscarDisponibilidad);
	$(document).on("click", "#reservaFiltroLimpiar", reservaLimpiarFiltro);
});
