/*=============================================
 Recepción: calendario y refresco de las tarjetas de habitaciones
 =============================================*/
function escaparHtmlRecepcion(valor){
	valor = valor == null ? "" : String(valor);
	return valor
		.replace(/&/g, "&amp;")
		.replace(/</g, "&lt;")
		.replace(/>/g, "&gt;")
		.replace(/"/g, "&quot;")
		.replace(/'/g, "&#39;");
}

function pintarTarjetasRecepcion(habitaciones, mensajeVacio){

	var $contenido = $(".recepcion-contenido");

	if (!habitaciones || habitaciones.length === 0){
		$contenido.html('<div class="callout callout-info recepcion-empty"><p>' + mensajeVacio + '</p></div>');
		return;
	}

	var html = '<div class="recepcion-grid">';

	habitaciones.forEach(function(hab){
		var nombre = escaparHtmlRecepcion(hab.nombre);
		var cliente = '';
		var horasExtra = '';
		var tieneHorasExtra = parseInt(hab.horasExtras, 10) > 0;

		if (hab.nombreCliente){
			cliente = '<div class="hc-cliente"><i class="fa fa-user-o"></i> ' + escaparHtmlRecepcion(hab.nombreCliente) + '</div>';

			if (hab.entradaCorta && hab.salidaCorta){
				var tieneHoraAnticipada = parseInt(hab.horaAnticipada, 10) > 0;
				var claseEntrada = tieneHoraAnticipada ? ' hc-stay-val-anticipada' : '';
				var tituloEntrada = tieneHoraAnticipada ? ' title="Incluye ' + parseInt(hab.horaAnticipada, 10) + 'h anticipada"' : '';
				var claseSalida = tieneHorasExtra ? ' hc-stay-val-extra' : '';
				var tituloSalida = tieneHorasExtra ? ' title="Incluye ' + parseInt(hab.horasExtras, 10) + 'h extra"' : '';

				cliente += '<div class="hc-stay">' +
					'<div class="hc-stay-pair"><span class="hc-stay-lbl">Entrada</span><span class="hc-stay-val' + claseEntrada + '"' + tituloEntrada + '>' + escaparHtmlRecepcion(hab.entradaCorta) + '</span></div>' +
					'<span class="hc-stay-arrow"><i class="fa fa-long-arrow-right"></i></span>' +
					'<div class="hc-stay-pair"><span class="hc-stay-lbl">Salida</span><span class="hc-stay-val' + claseSalida + '"' + tituloSalida + '>' + escaparHtmlRecepcion(hab.salidaCorta) + '</span></div>' +
				'</div>';
			}
		}else if (hab.proximaReservacionTexto){
			cliente = '<div class="hc-proxima"><i class="fa fa-clock-o"></i> ' + escaparHtmlRecepcion(hab.proximaReservacionTexto) + '</div>';
		}

		if (tieneHorasExtra){
			horasExtra += '<span class="hc-horas-extra" title="Incluye ' + parseInt(hab.horasExtras, 10) + 'h extra"><i class="fa fa-clock-o"></i> +' + parseInt(hab.horasExtras, 10) + 'h</span>';
		}

		if (parseInt(hab.horaAnticipada, 10) > 0){
			horasExtra += '<span class="hc-hora-anticipada" title="Llegada anticipada"><i class="fa fa-history"></i> Anticipada ' + parseInt(hab.horaAnticipada, 10) + 'h</span>';
		}

		var reservasProximas = parseInt(hab.reservasProximas, 10) || 0;
		var htmlReservasProximas = '';

		if (reservasProximas > 0){
			var etiquetaReservas = reservasProximas === 1 ? 'reserva próxima' : 'reservas próximas';
			htmlReservasProximas = '<div class="hc-reservas-proximas" title="Reservaciones próximas para esta habitación">' +
				'<i class="fa fa-calendar"></i> ' + reservasProximas + ' ' + etiquetaReservas +
			'</div>';
		}

		var htmlVerReservas = '';

		if (hab.estadoClase !== 'disponible' || reservasProximas > 0){
			htmlVerReservas = '<button type="button" class="hc-ver-reservas" title="Ver todas las reservas de esta habitación"' +
				' data-id-habitacion="' + escaparHtmlRecepcion(hab.id) + '"' +
				' data-tipo-habitacion="' + nombre + '">' +
				'<i class="fa fa-list-alt"></i> Ver reservas' +
			'</button>';
		}

		var accionesEstado = '';

		if (hab.puedeCheckin){
			accionesEstado += '<button type="button" class="hc-icon-btn checkin" title="Confirmar check-in"' +
				' data-id-reservacion="' + escaparHtmlRecepcion(hab.idReservacion) + '"' +
				' data-tipo-habitacion="' + nombre + '">' +
				'<i class="fa fa-sign-in"></i>' +
			'</button>';
		}

		if (hab.estadoClase === 'ocupada' || hab.estadoClase === 'reservada'){
			var tituloCancelar = hab.estadoClase === 'ocupada' ? 'Cancelar estadía' : 'Cancelar reservación';
			accionesEstado += '<button type="button" class="hc-icon-btn cancelar" title="' + tituloCancelar + '"' +
				' data-id-reservacion="' + escaparHtmlRecepcion(hab.idReservacion) + '"' +
				' data-tipo-habitacion="' + nombre + '"' +
				' data-estado="' + hab.estadoClase + '">' +
				'<i class="fa fa-times"></i>' +
			'</button>';
		}

		html += '<div class="habitacion-card">' +
					'<div class="hc-top">' +
						'<span class="hc-status-pill estado-' + hab.estadoClase + '" title="' + escaparHtmlRecepcion(hab.tituloPill) + '">' +
							'<i class="fa ' + hab.estadoIcono + '"></i> ' + hab.estadoTexto +
						'</span>' +
						'<div class="hc-badges">' + horasExtra + '</div>' +
					'</div>' +
					htmlReservasProximas +
					htmlVerReservas +
					'<div class="hc-bed"><i class="fa fa-bed"></i></div>' +
					cliente +
					'<div class="hc-footer">' +
						'<span class="hc-info-icon" title="Ver detalles"' +
							' data-nombre="' + nombre + '"' +
							' data-descripcion="' + escaparHtmlRecepcion(hab.descripcion) + '"' +
							' data-capacidad="' + escaparHtmlRecepcion(hab.capacidad) + '"' +
							' data-precio="' + escaparHtmlRecepcion(hab.precio) + '"' +
							' data-foto="' + escaparHtmlRecepcion(hab.foto) + '">' +
							'<i class="fa fa-question"></i>' +
						'</span>' +
						'<div class="hc-nombre">' + nombre + '</div>' +
						accionesEstado +
						'<span class="hc-arrow" title="Nueva reservación"' +
							' data-id-habitacion="' + escaparHtmlRecepcion(hab.id) + '"' +
							' data-tipo-habitacion="' + nombre + '"' +
							' data-precio-noche="' + escaparHtmlRecepcion(hab.precioNoche) + '">' +
							'<i class="fa fa-arrow-circle-right"></i>' +
						'</span>' +
					'</div>' +
				'</div>';
	});

	html += '</div>';

	$contenido.html(html);
}

function mostrarCargaRecepcion(){
	$("#recepcionOverlay").show();
}

function ocultarCargaRecepcion(){
	$("#recepcionOverlay").hide();
}

function actualizarRecepcion(){

	var fecha = $("#recepcionFecha").val();

	mostrarCargaRecepcion();

	$.ajax({
		url: "ajax/recepcion.ajax.php",
		method: "GET",
		data: { fecha: fecha },
		dataType: "json",
		success: function(respuesta){
			pintarTarjetasRecepcion(respuesta.data, "No hay habitaciones disponibles para este día. Las habitaciones inhabilitadas en el módulo de Habitaciones no se muestran aquí.");
			$(".recepcion-actualizado").text("Actualizado: " + new Date().toLocaleTimeString());
		},
		complete: ocultarCargaRecepcion
	});
}

function buscarRecepcion(termino){

	mostrarCargaRecepcion();

	$.ajax({
		url: "ajax/recepcion.ajax.php",
		method: "GET",
		data: { busqueda: termino },
		dataType: "json",
		success: function(respuesta){
			pintarTarjetasRecepcion(respuesta.data, "Ninguna habitación coincide con la búsqueda.");
		},
		complete: ocultarCargaRecepcion
	});
}

var recepcionBusquedaTimeout = null;

$(document).on("input", "#recepcionBusqueda", function(){

	var termino = $.trim($(this).val());

	clearTimeout(recepcionBusquedaTimeout);

	recepcionBusquedaTimeout = setTimeout(function(){
		if (termino === ""){
			actualizarRecepcion();
		}else{
			buscarRecepcion(termino);
		}
	}, 300);
});

if ($("#recepcionFecha").length){

	var $fecha = $("#recepcionFecha");

	$fecha.datepicker({
		format: "yyyy-mm-dd",
		language: "es",
		autoclose: true,
		todayHighlight: true,
		startDate: $fecha.data("min-date")
	}).on("changeDate", function(){
		clearTimeout(recepcionBusquedaTimeout);
		$("#recepcionBusqueda").val("");
		actualizarRecepcion();
	});
}

/*=============================================
 Detalle de habitación (modal de solo lectura)
 =============================================*/
function mostrarErrorFotoRecepcion(){
	$("#detalleHabFoto").hide();
	$("#detalleHabSinFoto").show();
}

$(document).on("click", ".hc-info-icon", function(){

	var $icono = $(this);

	$("#detalleHabNombre").text($icono.data("nombre"));
	$("#detalleHabDescripcion").text($icono.data("descripcion"));
	$("#detalleHabCapacidad").text($icono.data("capacidad"));
	$("#detalleHabPrecio").text($icono.data("precio"));

	var foto = $icono.data("foto");

	if (foto){
		$("#detalleHabFoto").attr("src", foto).show();
		$("#detalleHabSinFoto").hide();
	}else{
		$("#detalleHabFoto").hide();
		$("#detalleHabSinFoto").show();
	}

	$("#modalDetalleHabitacion").modal("show");
});

/*=============================================
 Historial de reservas de una habitación
 =============================================*/
$(document).on("click", ".hc-ver-reservas", function(){

	var $boton = $(this);
	var idHabitacion = $boton.data("idHabitacion");

	$("#hrHabitacionNombre").text($boton.data("tipoHabitacion"));
	$("#hrContenido").html('<p class="text-muted text-center" style="padding:20px;">Cargando…</p>');
	$("#modalHistorialReservas").modal("show");

	$.ajax({
		url: "ajax/reservaciones.ajax.php",
		method: "GET",
		data: { accion: "historial", id_habitacion: idHabitacion },
		dataType: "json",
		success: function(respuesta){

			var reservas = respuesta.data || [];

			if (reservas.length === 0){
				$("#hrContenido").html('<p class="text-muted text-center" style="padding:20px;">Esta habitación no tiene próximas reservaciones.</p>');
				return;
			}

			var html = '<div style="overflow-x:auto;"><table class="hr-tabla"><thead><tr>' +
				'<th>Folio</th><th>Cliente</th><th>Entrada</th><th>Salida</th><th>Estado</th>' +
				'</tr></thead><tbody>';

			reservas.forEach(function(r){
				html += '<tr>' +
					'<td>' + escaparHtmlRecepcion(r.folio) + '</td>' +
					'<td>' + escaparHtmlRecepcion(r.cliente) + '</td>' +
					'<td>' + escaparHtmlRecepcion(r.entrada) + '</td>' +
					'<td>' + escaparHtmlRecepcion(r.salida) + '</td>' +
					'<td><span class="hr-estado estado-' + r.estadoClase + '">' + escaparHtmlRecepcion(r.estadoTexto) + '</span></td>' +
					'</tr>';
			});

			html += '</tbody></table></div>';

			$("#hrContenido").html(html);
		},
		error: function(){
			$("#hrContenido").html('<p class="text-muted text-center" style="padding:20px;">No se pudo cargar el historial.</p>');
		}
	});
});

/*=============================================
 Check-in y cancelación (íconos del pie de la tarjeta)
 =============================================*/
function ejecutarCheckin(idReservacion){

	mostrarCargaRecepcion();

	$.ajax({
		url: "ajax/reservaciones.ajax.php",
		method: "POST",
		data: { accion: "checkin", id_reservacion: idReservacion },
		dataType: "json",
		success: function(respuesta){
			if (respuesta.ok){

				var horas = parseInt(respuesta.horasAnticipada, 10) || 0;
				var texto = horas > 0
					? "Se cobraron " + horas + " hora(s) anticipada(s)."
					: undefined;

				Swal.fire({ icon: "success", title: "Check-in confirmado", text: texto, timer: horas > 0 ? 2500 : 1500, showConfirmButton: false });
				refrescarRecepcion();

			}else{
				Swal.fire({ icon: "error", title: "No se pudo confirmar", text: respuesta.mensaje });
			}
		},
		complete: ocultarCargaRecepcion
	});

}

$(document).on("click", ".hc-icon-btn.checkin", function(){

	var $boton = $(this);
	var idReservacion = $boton.data("idReservacion");
	var tipoHabitacion = $boton.data("tipoHabitacion");

	mostrarCargaRecepcion();

	$.ajax({
		url: "ajax/reservaciones.ajax.php",
		method: "GET",
		data: { accion: "validarLlegadaAnticipada", id_reservacion: idReservacion },
		dataType: "json",
		success: function(respuesta){

			if (!respuesta.ok){
				Swal.fire({ icon: "error", title: "No se pudo validar", text: respuesta.mensaje });
				return;
			}

			if (!respuesta.requiereCargo){

				Swal.fire({
					icon: "question",
					title: "¿Confirmar check-in?",
					text: "Se marcará " + tipoHabitacion + " como Ocupada.",
					showCancelButton: true,
					confirmButtonText: "Sí, confirmar",
					cancelButtonText: "Cancelar",
					confirmButtonColor: "#3f6b4a"
				}).then(function(resultado){
					if (resultado.value){
						ejecutarCheckin(idReservacion);
					}
				});

				return;
			}

			if (respuesta.permitido === false){

				if (respuesta.salidaAnterior){
					Swal.fire({
						icon: "warning",
						title: "No se puede hacer check-in todavía",
						html:
							'<p style="margin:0 0 14px;color:#3f342e;">La habitación todavía tiene una reservación previa activa, así que no es posible cubrir esa cantidad de horas anticipadas.</p>' +
							'<div style="background:#f4efe4;border:1px solid #eee3d2;border-radius:8px;padding:12px 16px;text-align:left;">' +
								'<p style="margin:6px 0;color:#3f342e;font-size:14px;"><i class="fa fa-sign-out" style="color:#b96a37;width:20px;display:inline-block;"></i> Salida de la reservación anterior: <strong>' + escaparHtmlRecepcion(respuesta.salidaAnterior) + '</strong></p>' +
							'</div>',
						confirmButtonText: "Entendido",
						confirmButtonColor: "#81412d"
					});
				}else{
					Swal.fire({
						icon: "warning",
						title: "No se puede hacer check-in todavía",
						text: respuesta.mensaje || "Es muy pronto para hacer check-in.",
						confirmButtonText: "Entendido",
						confirmButtonColor: "#81412d"
					});
				}

				return;
			}

			var horas = parseInt(respuesta.horas, 10) || 0;
			var precioTotal = parseFloat(respuesta.precioTotal) || 0;

			Swal.fire({
				icon: "info",
				title: "Puedes hacer check-in pero se tomarán las HrsAnticipadas",
				html: "Se cobrarán <b>" + horas + " hora" + (horas === 1 ? "" : "s") + " anticipada" + (horas === 1 ? "" : "s") + "</b> ($" + precioTotal.toFixed(2) + ") a " + escaparHtmlRecepcion(tipoHabitacion) + ".",
				showCancelButton: true,
				confirmButtonText: "Sí, confirmar",
				cancelButtonText: "Cancelar",
				confirmButtonColor: "#3f6b4a"
			}).then(function(resultado){
				if (resultado.value){
					ejecutarCheckin(idReservacion);
				}
			});

		},
		error: function(){
			Swal.fire({ icon: "error", title: "No se pudo validar el check-in", text: "Intenta de nuevo." });
		},
		complete: ocultarCargaRecepcion
	});
});

$(document).on("click", ".hc-icon-btn.cancelar", function(){

	var $boton = $(this);
	var idReservacion = $boton.data("idReservacion");
	var tipoHabitacion = $boton.data("tipoHabitacion");
	var esOcupada = $boton.data("estado") === "ocupada";

	mostrarCargaRecepcion();

	$.ajax({
		url: "ajax/reservaciones.ajax.php",
		method: "GET",
		data: { accion: "motivosCancelacion" },
		dataType: "json",
		success: function(respuesta){

			var motivos = respuesta.data || [];

			if (motivos.length === 0){
				Swal.fire({ icon: "error", title: "Sin motivos", text: "No hay motivos de cancelación configurados." });
				return;
			}

			var inputOptions = {};
			motivos.forEach(function(motivo){
				inputOptions[motivo.id] = motivo.nombre;
			});

			Swal.fire({
				icon: "warning",
				title: esOcupada ? "¿Cancelar estadía?" : "¿Cancelar reservación?",
				text: (esOcupada ? "Se cancelará la estadía actual de " : "Se cancelará la reservación de ") + tipoHabitacion + ". Esta acción no se puede deshacer.",
				input: "select",
				inputOptions: inputOptions,
				inputPlaceholder: "Selecciona un motivo",
				inputValidator: function(value){
					return value ? undefined : "Selecciona un motivo para continuar.";
				},
				showCancelButton: true,
				confirmButtonText: "Sí, cancelar",
				cancelButtonText: "Regresar",
				confirmButtonColor: "#9a3b2c"
			}).then(function(resultado){

				if (!resultado.value){
					return;
				}

				mostrarCargaRecepcion();

				$.ajax({
					url: "ajax/reservaciones.ajax.php",
					method: "POST",
					data: { accion: "cancelar", id_reservacion: idReservacion, id_motivo: resultado.value },
					dataType: "json",
					success: function(respuesta){
						if (respuesta.ok){
							Swal.fire({ icon: "success", title: "Cancelada", text: "Folio de cancelación: " + respuesta.folio, timer: 2500, showConfirmButton: false });
							refrescarRecepcion();
						}else{
							Swal.fire({ icon: "error", title: "No se pudo cancelar", text: respuesta.mensaje });
						}
					},
					complete: ocultarCargaRecepcion
				});
			});
		},
		error: function(){
			Swal.fire({ icon: "error", title: "Error", text: "No se pudieron cargar los motivos de cancelación." });
		},
		complete: ocultarCargaRecepcion
	});
});

/*=============================================
 Nueva reservación (modal desde la flecha de la tarjeta)
 =============================================*/
function refrescarRecepcion(){
	var termino = $.trim($("#recepcionBusqueda").val());

	if (termino === ""){
		actualizarRecepcion();
	}else{
		buscarRecepcion(termino);
	}
}

// Selects propios de hora (00-23) y minuto, para no depender de que el navegador respete el
// formato 24h del selector nativo de hora (varios lo muestran en a.m./p.m. sin importar el idioma).
function nrPoblarSelectsHora(){
	var pad = function(n){ return n < 10 ? "0" + n : n; };
	var horas = "";

	for (var h = 0; h < 24; h++){
		horas += '<option value="' + pad(h) + '">' + pad(h) + '</option>';
	}

	var minutos = "";
	["00", "15", "30", "45"].forEach(function(m){
		minutos += '<option value="' + m + '">' + m + '</option>';
	});

	$("#nrFechaEntradaHora, #nrFechaSalidaHora").html(horas);
	$("#nrFechaEntradaMin, #nrFechaSalidaMin").html(minutos);
}
nrPoblarSelectsHora();


function nrSincronizarFecha(prefijo){
	var dia = $("#nrFecha" + prefijo + "Dia").val();
	var hora = $("#nrFecha" + prefijo + "Hora").val();
	var minuto = $("#nrFecha" + prefijo + "Min").val();

	var valor = dia ? (dia + "T" + hora + ":" + minuto) : "";
	$("#nrFecha" + prefijo).val(valor).trigger("change");
}

$(document).on("change", "#nrFechaEntradaDia, #nrFechaEntradaHora, #nrFechaEntradaMin", function(){
	nrSincronizarFecha("Entrada");
});
$(document).on("change", "#nrFechaSalidaDia, #nrFechaSalidaHora, #nrFechaSalidaMin", function(){
	nrSincronizarFecha("Salida");
});

function nrCalcularPrecio(){
	var entrada = $("#nrFechaEntrada").val();
	var salida = $("#nrFechaSalida").val();
	var precioNoche = parseFloat($("#nrPrecioNoche").val()) || 0;

	if (!entrada || !salida || !precioNoche){
		return;
	}

	var tsEntrada = new Date(entrada).getTime();
	var tsSalida = new Date(salida).getTime();

	if (isNaN(tsEntrada) || isNaN(tsSalida) || tsSalida <= tsEntrada){
		return;
	}

	var noches = Math.ceil((tsSalida - tsEntrada) / (1000 * 60 * 60 * 24));
	if (noches < 1){
		noches = 1;
	}

	$("#nrPrecio").val((noches * precioNoche).toFixed(2));
}

$(document).on("click", ".hc-arrow", function(){

	var $arrow = $(this);

	$("#formNuevaReservacion")[0].reset();
	$("#nrIdClienteSeleccionado").val("");
	$("#nrResultadosCliente").hide().empty();
	$("#nrClienteSeleccionadoTexto").hide();
	$("#nrClienteNuevoCampos").hide();
	$("#nrBuscarCliente").val("").prop("disabled", false);
	$("#nrToggleClienteNuevo").show();
	nrLimpiarAdvertenciaTelefono();

	$("#nrIdHabitacion").val($arrow.data("idHabitacion"));
	$("#nrPrecioNoche").val($arrow.data("precioNoche"));
	$("#nrHabitacionNombre").text($arrow.data("tipoHabitacion"));

	// No se pueden reservar días anteriores a hoy (igual que el calendario de Recepción).
	var pad = function(n){ return n < 10 ? "0" + n : n; };
	var hoy = new Date();
	var hoyStr = hoy.getFullYear() + "-" + pad(hoy.getMonth() + 1) + "-" + pad(hoy.getDate());
	$("#nrFechaEntradaDia, #nrFechaSalidaDia").attr("min", hoyStr);

	// El día de entrada se toma del calendario de Recepción ya seleccionado, con la hora de
	// check-in habitual (3:00 pm); la salida queda en blanco para que la elijan.
	var fechaSeleccionada = $("#recepcionFecha").val() || hoyStr;
	$("#nrFechaEntradaDia").val(fechaSeleccionada);
	$("#nrFechaEntradaHora").val("15");
	$("#nrFechaEntradaMin").val("00");
	nrSincronizarFecha("Entrada");

	$("#nrFechaSalidaDia").val("").attr("min", fechaSeleccionada);
	$("#nrFechaSalidaHora").val("12");
	$("#nrFechaSalidaMin").val("00");
	$("#nrFechaSalida").val("");

	$("#modalNuevaReservacion").modal("show");
});

$(document).on("change", "#nrFechaEntrada", function(){
	var dia = $("#nrFechaEntradaDia").val();
	if (dia){
		$("#nrFechaSalidaDia").attr("min", dia);
	}
});

var nrBusquedaClienteTimeout = null;

$(document).on("input", "#nrBuscarCliente", function(){

	var termino = $.trim($(this).val());

	clearTimeout(nrBusquedaClienteTimeout);

	if (termino.length < 2){
		$("#nrResultadosCliente").hide().empty();
		return;
	}

	nrBusquedaClienteTimeout = setTimeout(function(){
		$.ajax({
			url: "ajax/reservaciones.ajax.php",
			method: "GET",
			data: { accion: "buscarClientes", termino: termino },
			dataType: "json",
			success: function(respuesta){

				var $resultados = $("#nrResultadosCliente");
				$resultados.empty();

				if (!respuesta.data || respuesta.data.length === 0){
					$resultados.html('<div class="nr-resultado-item text-muted">Sin coincidencias</div>').show();
					return;
				}

				respuesta.data.forEach(function(cliente){
					var nombreCompleto = escaparHtmlRecepcion(cliente.nombre + " " + cliente.apaterno + " " + cliente.amaterno);

					var $item = $('<div class="nr-resultado-item"></div>')
						.html('<i class="fa fa-user"></i> ' + nombreCompleto + ' <small class="text-muted">' + escaparHtmlRecepcion(cliente.telefono) + '</small>')
						.data("id", cliente.id)
						.data("nombre", nombreCompleto);

					$resultados.append($item);
				});

				$resultados.show();
			}
		});
	}, 300);
});

$(document).on("click", ".nr-resultado-item", function(){

	var $item = $(this);
	var id = $item.data("id");

	if (!id){
		return;
	}

	$("#nrIdClienteSeleccionado").val(id);
	$("#nrClienteSeleccionadoNombre").text($item.data("nombre"));
	$("#nrClienteSeleccionadoTexto").show();
	$("#nrBuscarCliente").val("").prop("disabled", true);
	$("#nrResultadosCliente").hide().empty();
	$("#nrClienteNuevoCampos").hide();
	$("#nrToggleClienteNuevo").hide();
});

$(document).on("click", "#nrQuitarCliente", function(e){
	e.preventDefault();
	$("#nrIdClienteSeleccionado").val("");
	$("#nrClienteSeleccionadoTexto").hide();
	$("#nrBuscarCliente").prop("disabled", false);
	$("#nrToggleClienteNuevo").show();
});

$(document).on("click", "#nrToggleClienteNuevo", function(e){
	e.preventDefault();

	var seVanAMostrar = !$("#nrClienteNuevoCampos").is(":visible");

	$("#nrClienteNuevoCampos").slideToggle(150);
	$("#nrIdClienteSeleccionado").val("");
	$("#nrClienteSeleccionadoTexto").hide();

	if (seVanAMostrar){
		// Mientras se captura un cliente nuevo no se puede buscar uno existente en paralelo.
		$("#nrBuscarCliente").val("").prop("disabled", true);
		$("#nrResultadosCliente").hide().empty();
	}else{
		$("#nrBuscarCliente").prop("disabled", false);
		$("#nrTelefono").val("");
		nrLimpiarAdvertenciaTelefono();
	}
});

var nrTelefonoDuplicado = false;
var nrTelefonoCheckTimeout = null;

function nrLimpiarAdvertenciaTelefono(){
	nrTelefonoDuplicado = false;
	clearTimeout(nrTelefonoCheckTimeout);
	$("#nrTelefonoAdvertencia").hide().text("");
}

$(document).on("input", "#nrTelefono", function(){

	this.value = this.value.replace(/\D/g, "").slice(0, 10);

	var telefono = this.value;

	nrLimpiarAdvertenciaTelefono();

	if (telefono.length !== 10){
		return;
	}

	nrTelefonoCheckTimeout = setTimeout(function(){
		$.ajax({
			url: "ajax/reservaciones.ajax.php",
			method: "GET",
			data: { accion: "buscarClientes", termino: telefono },
			dataType: "json",
			success: function(respuesta){
				var existe = (respuesta.data || []).some(function(cliente){
					return cliente.telefono === telefono;
				});

				if (existe){
					nrTelefonoDuplicado = true;
					$("#nrTelefonoAdvertencia")
						.text("Ya existe un cliente registrado con este teléfono. Búscalo arriba en vez de crear uno nuevo.")
						.show();
				}
			}
		});
	}, 400);
});

$(document).on("change", "#nrFechaEntrada, #nrFechaSalida", nrCalcularPrecio);

$(document).on("click", "#nrGuardar", function(){

	var datos = {
		id_habitacion: $("#nrIdHabitacion").val(),
		fecha_entrada: $("#nrFechaEntrada").val(),
		fecha_salida: $("#nrFechaSalida").val(),
		precio: $("#nrPrecio").val(),
		id_cliente: $("#nrIdClienteSeleccionado").val()
	};

	if (!datos.id_cliente){
		datos.nombre = $("#nrNombre").val();
		datos.apaterno = $("#nrApaterno").val();
		datos.amaterno = $("#nrAmaterno").val();
		datos.telefono = $("#nrTelefono").val();
	}

	if (!datos.fecha_entrada || !datos.fecha_salida || !datos.precio){
		Swal.fire({ icon: "warning", title: "Faltan datos", text: "Captura entrada, salida y precio." });
		return;
	}

	if (!datos.id_cliente && (!datos.nombre || !datos.apaterno || !datos.telefono)){
		Swal.fire({ icon: "warning", title: "Falta el cliente", text: "Selecciona un cliente existente o captura los datos del cliente nuevo." });
		return;
	}

	if (!datos.id_cliente && datos.telefono && !/^\d{10}$/.test(datos.telefono)){
		Swal.fire({ icon: "warning", title: "Teléfono inválido", text: "El teléfono debe tener exactamente 10 dígitos." });
		return;
	}

	if (!datos.id_cliente && nrTelefonoDuplicado){
		Swal.fire({ icon: "warning", title: "Teléfono duplicado", text: "Ya existe un cliente con ese teléfono. Selecciónalo desde la búsqueda en vez de crear uno nuevo." });
		return;
	}

	mostrarCargaRecepcion();

	$.ajax({
		url: "ajax/reservaciones.ajax.php",
		method: "POST",
		data: datos,
		dataType: "json",
		success: function(respuesta){
			if (respuesta.ok){
				$("#modalNuevaReservacion").modal("hide");
				Swal.fire({ icon: "success", title: "Reservación creada", text: "Folio: " + respuesta.folio });
				refrescarRecepcion();
			}else{
				Swal.fire({ icon: "error", title: "No se pudo guardar", text: respuesta.mensaje });
			}
		},
		complete: ocultarCargaRecepcion
	});
});
