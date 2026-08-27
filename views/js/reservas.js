// El spinner de carga ahora es global (ver plantilla.php), así que este overlay local ya
// no se muestra para no encimarse visualmente con el global.
function mostrarCargaReserva(){}

function ocultarCargaReserva(){}

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

	// Al pasar el mouse por cualquier carril de una habitación, se resaltan TODOS sus
	// carriles a la vez (no solo el que está bajo el cursor), para que se vea claro que
	// todos pertenecen a la misma habitación.
	$(document).on("mouseenter", ".reserva-fila", function(){
		var idHabitacion = $(this).data("habitacion");
		$(".reserva-fila[data-habitacion='" + idHabitacion + "']").addClass("reserva-fila-hover");
	});
	$(document).on("mouseleave", ".reserva-fila", function(){
		var idHabitacion = $(this).data("habitacion");
		$(".reserva-fila[data-habitacion='" + idHabitacion + "']").removeClass("reserva-fila-hover");
	});
});
