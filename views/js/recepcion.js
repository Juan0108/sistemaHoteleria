/*=============================================
 Recepción: calendario y refresco de las tarjetas de habitaciones
 =============================================*/
// Corta un texto cada 3 palabras con un salto de línea, para que los tooltips (como el de
// mantenimiento) no salgan como una sola línea larguísima.
function cortarCada3PalabrasRecepcion(texto){
	var palabras = String(texto || "").trim().split(/\s+/);
	var grupos = [];

	for (var i = 0; i < palabras.length; i += 3){
		grupos.push(palabras.slice(i, i + 3).join(" "));
	}

	return grupos.join("\n");
}

// Une las descripciones de todas las incidencias activas de una habitación (puede tener más
// de una a la vez), numerándolas y cortando cada una cada 3 palabras.
function armarTooltipMantenimientoRecepcion(descripciones){
	if (!descripciones || descripciones.length === 0){
		return "En mantenimiento";
	}

	var bloques = descripciones.map(function(descripcion, indice){
		var prefijo = descripciones.length > 1 ? (indice + 1) + ") " : "";
		return prefijo + cortarCada3PalabrasRecepcion(descripcion);
	});

	return bloques.join("\n\n");
}

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
		var badgeMantenimiento = '';
		if (hab.enMantenimiento){
			var _tituloMtto = armarTooltipMantenimientoRecepcion(hab.mantenimientoDescripciones);

			badgeMantenimiento = '<span class="hc-badge-mantenimiento" title="' + escaparHtmlRecepcion(_tituloMtto) + '">' +
				'<img src="views/img/Iconos/mantenimiento.jpg" alt="Mantenimiento">' +
			'</span>';
		}

		var badgeServicio = '';
		if (hab.enServicio){
			var _tituloServ = "En limpieza (Intendencia)";
			if (hab.servicioInicio) _tituloServ += "\nInicio: " + formatearFechaHora(hab.servicioInicio);

			badgeServicio = '<span class="hc-badge-servicio" title="' + escaparHtmlRecepcion(_tituloServ) + '">' +
			'<img src="views/img/Iconos/limpieza.png" alt="Limpieza">' +
			'</span>';
		}

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

		// El contador y "Ver reservas" cuentan lo mismo: las adicionales en cola
		// (reservasProximas, que ya cuenta cualquier Reservada con entrada futura) más la
		// reservación de hoy solo si es Reservada Y su entrada YA llegó (entradaYaLlego):
		// si todavía no llega, ese conteo de arriba ya la incluyó y sumarla de nuevo la
		// duplicaría. Si ya está Ocupada, esa estadía ya sucedió: no cuenta.
		var reservasProximas = parseInt(hab.reservasProximas, 10) || 0;
		var tieneReservaActual = hab.estadoClase === 'reservada' && hab.entradaYaLlego;
		var totalReservas = reservasProximas + (tieneReservaActual ? 1 : 0);
		var htmlReservasProximas = '';

		if (totalReservas > 0){
			var etiquetaReservas = totalReservas === 1 ? 'reserva' : 'reservas';
			htmlReservasProximas = '<div class="hc-reservas-proximas" title="Reservaciones para esta habitación">' +
				'<i class="fa fa-calendar"></i> ' + totalReservas + ' ' + etiquetaReservas +
			'</div>';
		}

		var htmlVerReservas = '';

		if (totalReservas > 0){
			htmlVerReservas = '<button type="button" class="hc-ver-reservas" title="Ver todas las reservas de esta habitación"' +
				' data-id-habitacion="' + escaparHtmlRecepcion(hab.id) + '"' +
				' data-tipo-habitacion="' + nombre + '"' +
				' data-precio-noche="' + escaparHtmlRecepcion(hab.precioNoche) + '">' +
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

		if (hab.puedeCheckout){
			accionesEstado += '<button type="button" class="hc-icon-btn checkout" title="Check out"' +
				' data-id-reservacion="' + escaparHtmlRecepcion(hab.idReservacion) + '"' +
				' data-tipo-habitacion="' + nombre + '">' +
				'<i class="fa fa-sign-out"></i>' +
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
						'<div class="hc-badges">' + horasExtra + badgeMantenimiento + badgeServicio + '</div>' +
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

// El spinner de carga ahora es global (ver plantilla.php: se activa solo con cualquier
// $.ajax de cualquier módulo), así que este overlay local ya no se muestra para no
// encimarse visualmente con el global. Se dejan las funciones como no-op en vez de
// borrarlas para no tener que tocar cada punto donde ya se llaman.
function mostrarCargaRecepcion(){}

function ocultarCargaRecepcion(){}

function actualizarRecepcion(){

	var fecha = $("#recepcionFecha").val();
	var tipo = $("#recepcionTipo").val();

	mostrarCargaRecepcion();

	$.ajax({
		url: "ajax/recepcion.ajax.php",
		method: "GET",
		data: { fecha: fecha, tipo: tipo },
		dataType: "json",
		success: function(respuesta){
			pintarTarjetasRecepcion(respuesta.data, "No hay habitaciones disponibles para este día. Las habitaciones inhabilitadas en el módulo de Habitaciones no se muestran aquí.");
			$(".recepcion-actualizado").text("Actualizado: " + new Date().toLocaleTimeString());
		},
		complete: ocultarCargaRecepcion
	});
}

function buscarRecepcion(termino){

	var tipo = $("#recepcionTipo").val();

	mostrarCargaRecepcion();

	$.ajax({
		url: "ajax/recepcion.ajax.php",
		method: "GET",
		data: { busqueda: termino, tipo: tipo },
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

$(document).on("change", "#recepcionTipo", function(){

	var termino = $.trim($("#recepcionBusqueda").val());

	if (termino === ""){
		actualizarRecepcion();
	}else{
		buscarRecepcion(termino);
	}
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
	var tipoHabitacion = $boton.data("tipoHabitacion");
	var precioNoche = $boton.data("precioNoche");

	$("#hrHabitacionNombre").text(tipoHabitacion);
	$("#hrContenido").html('<p class="text-muted text-center" style="padding:20px;">Cargando…</p>');
	$("#modalHistorialReservas").modal("show");

	$.ajax({
		url: "ajax/reservaciones.ajax.php",
		method: "GET",
		data: { accion: "historial", id_habitacion: idHabitacion, fecha: $("#recepcionFecha").val() },
		dataType: "json",
		success: function(respuesta){

			var reservas = respuesta.data || [];

			if (reservas.length === 0){
				$("#hrContenido").html('<p class="text-muted text-center" style="padding:20px;">Esta habitación no tiene próximas reservaciones.</p>');
				return;
			}

			var html = '<div style="overflow-x:auto;"><table class="hr-tabla"><thead><tr>' +
				'<th>Folio</th><th>Cliente</th><th>Entrada</th><th>Salida</th><th>Estado</th><th></th>' +
				'</tr></thead><tbody>';

			reservas.forEach(function(r){
				var puedeMover = r.estadoClase === "ocupada" || r.estadoClase === "reservada";

				var botonMover = puedeMover
					? '<button type="button" class="hr-mover" title="Mover a otra habitación/fechas"' +
						' data-folio="' + escaparHtmlRecepcion(r.folio) + '"' +
						' data-cliente="' + escaparHtmlRecepcion(r.cliente) + '"' +
						' data-entrada-raw="' + escaparHtmlRecepcion(r.entradaRaw) + '"' +
						' data-salida-raw="' + escaparHtmlRecepcion(r.salidaRaw) + '"' +
						' data-id-habitacion="' + escaparHtmlRecepcion(idHabitacion) + '"' +
						' data-tipo-habitacion="' + escaparHtmlRecepcion(tipoHabitacion) + '"' +
						' data-precio-noche="' + escaparHtmlRecepcion(precioNoche) + '">' +
						'<i class="fa fa-exchange"></i> Mover' +
					'</button>'
					: '';

				html += '<tr>' +
					'<td>' + escaparHtmlRecepcion(r.folio) + '</td>' +
					'<td>' + escaparHtmlRecepcion(r.cliente) + '</td>' +
					'<td>' + escaparHtmlRecepcion(r.entrada) + '</td>' +
					'<td>' + escaparHtmlRecepcion(r.salida) + '</td>' +
					'<td><span class="hr-estado estado-' + r.estadoClase + '">' + escaparHtmlRecepcion(r.estadoTexto) + '</span></td>' +
					'<td>' + botonMover + '</td>' +
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
 Mover reservación (botón "Mover" dentro de "Ver reservas")
 =============================================*/

// "YYYY-MM-DD HH:MM:SS" -> partes para los selects día/hora/minuto del modal.
function mrPartesFecha(fechaMysql){
	if (!fechaMysql){
		return { dia: "", hora: "15", minuto: "00" };
	}

	var partes = fechaMysql.split(" ");
	var horaMin = partes[1] ? partes[1].split(":") : ["15", "00"];

	return { dia: partes[0], hora: horaMin[0], minuto: horaMin[1] };
}

$(document).on("click", ".hr-mover", function(){

	var $boton = $(this);

	$("#mrIdReservacion").val($boton.data("folio"));
	$("#mrFolioActual").html(
		"Folio " + escaparHtmlRecepcion($boton.data("folio")) +
		'<span class="mr-subtitulo-sep">·</span>' + escaparHtmlRecepcion($boton.data("cliente"))
	);
	$("#mrIdHabitacion").val($boton.data("idHabitacion"));
	$("#mrHabitacionNombre").val($boton.data("tipoHabitacion"));
	$("#mrPrecioNoche").val($boton.data("precioNoche"));

	var entrada = mrPartesFecha(String($boton.data("entradaRaw") || ""));
	$("#mrFechaEntradaDia").val(entrada.dia);
	$("#mrFechaEntradaHora").val(entrada.hora);
	$("#mrFechaEntradaMin").val(entrada.minuto);
	mrSincronizarFecha("Entrada");

	var salida = mrPartesFecha(String($boton.data("salidaRaw") || ""));
	$("#mrFechaSalidaDia").val(salida.dia);
	$("#mrFechaSalidaHora").val(salida.hora);
	$("#mrFechaSalidaMin").val(salida.minuto);
	mrSincronizarFecha("Salida");

	mrCalcularPrecio();

	// Se encadena con un spinner y se espera a que "Ver reservas" termine de cerrar
	// (evento hidden.bs.modal) antes de abrir "Mover": mostrar/ocultar ambos modales
	// al mismo tiempo dejaba un parpadeo/backdrop encimado entre los dos.
	mostrarCargaRecepcion();

	$("#modalHistorialReservas").one("hidden.bs.modal", function(){
		$("#modalMoverReservacion")
			.one("shown.bs.modal", function(){ ocultarCargaRecepcion(); })
			.modal("show");
	}).modal("hide");
});

$(document).on("click", "#mrGuardar", function(){

	var datos = {
		id_reservacion: $("#mrIdReservacion").val(),
		id_habitacion: $("#mrIdHabitacion").val(),
		fecha_entrada: $("#mrFechaEntrada").val(),
		fecha_salida: $("#mrFechaSalida").val()
	};

	if (!datos.id_reservacion || !datos.id_habitacion || !datos.fecha_entrada || !datos.fecha_salida){
		Swal.fire({ icon: "warning", title: "Faltan datos", text: "Selecciona habitación, entrada y salida nuevas." });
		return;
	}

	if (new Date(datos.fecha_salida).getTime() <= new Date(datos.fecha_entrada).getTime()){
		Swal.fire({ icon: "warning", title: "Fechas inválidas", text: "La salida debe ser posterior a la entrada." });
		return;
	}

	mostrarCargaRecepcion();

	$.ajax({
		url: "ajax/reservaciones.ajax.php",
		method: "POST",
		data: $.extend({ accion: "mover" }, datos),
		dataType: "json",
		success: function(respuesta){
			if (respuesta.ok){
				$("#modalMoverReservacion").modal("hide");
				Swal.fire({ icon: "success", title: "Reservación movida", text: "Folio nuevo: " + respuesta.folio });
				refrescarRecepcion();
			}else{
				Swal.fire({ icon: "error", title: "No se pudo mover", text: respuesta.mensaje });
			}
		},
		error: function(){
			Swal.fire({ icon: "error", title: "Error", text: "No se pudo mover la reservación. Intenta de nuevo." });
		},
		complete: ocultarCargaRecepcion
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

// Último paso antes de ejecutar el check-in de verdad, sin importar si hubo o no cobro de
// horas anticipadas: siempre se pide esta confirmación simple al final.
function confirmarCheckinFinal(idReservacion, tipoHabitacion){
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
				confirmarCheckinFinal(idReservacion, tipoHabitacion);
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
				html: "Se cobrarán <b>" + horas + " hora" + (horas === 1 ? "" : "s") + " anticipada" + (horas === 1 ? "" : "s") + "</b> ($" + formatearPrecio(precioTotal) + ") a " + escaparHtmlRecepcion(tipoHabitacion) + ".",
				showCancelButton: true,
				confirmButtonText: "Sí, confirmar",
				cancelButtonText: "Cancelar",
				confirmButtonColor: "#3f6b4a"
			}).then(function(resultado){
				if (resultado.value){
					confirmarCheckinFinal(idReservacion, tipoHabitacion);
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

				// Una estadía Ocupada todavía tiene una cuenta que cobrar (hospedaje +
				// consumo), así que además de la validación del motivo se necesita hacer el
				// Check Out: se reutiliza el mismo modal, con el motivo ya elegido guardado.
				if (esOcupada){
					abrirModalCheckOut(idReservacion, resultado.value);
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
 Check Out (preguntas + consumo de la estadía)

 El mismo modal se reutiliza para "Cancelar estadía" de una habitación Ocupada: además de
 la validación de la cancelación (motivo), ese caso también necesita cobrar el hospedaje
 y capturar el pago, igual que un Check Out normal. idMotivoCancelacion viene vacío en un
 Check Out normal; cuando trae un valor, #coConfirmar cancela la estadía en vez de
 completarla.
 =============================================*/
// Cache de los tipos de pago disponibles, para poder construir el <select> de cada línea
// de pago nueva sin tener que volver a pedirlos al servidor.
var coTiposDePago = [];

function coOpcionesTipoPagoHtml(seleccionado){
	var _html = '<option value="">-- Tipo de pago --</option>';
	coTiposDePago.forEach(function(tipo){
		_html += '<option value="' + tipo.id + '"' + (String(tipo.id) === String(seleccionado) ? " selected" : "") + '>' + escaparHtmlRecepcion(tipo.pago) + '</option>';
	});
	return _html;
}

function coEsEfectivoLinea($linea){
	return $linea.find(".co-linea-tipo option:selected").text().trim() === "Efectivo";
}

// Aplica el mismo formato (comas de miles, un solo punto decimal) que ya usaba
// #coMontoRecibido, reutilizable para cualquier input de monto del modal de check out.
function coFormatearInputMonto($input){
	var _valor = $input.val().replace(/[^\d.]/g, "");

	var _puntoIndice = _valor.indexOf(".");
	if (_puntoIndice !== -1){
		_valor = _valor.slice(0, _puntoIndice + 1) + _valor.slice(_puntoIndice + 1).replace(/\./g, "");
	}

	var _partes = _valor.split(".");
	_partes[0] = _partes[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
	if (_partes.length > 1){
		_partes[1] = _partes[1].slice(0, 2);
	}

	$input.val(_partes.join("."));
}

// El monto siempre se deja vacío para que el cajero lo capture a mano — no se sugiere ni
// se autocompleta con el total ni con lo que falte cubrir, para evitar que se confirme un
// monto sin que alguien lo haya escrito conscientemente.
function coAgregarLineaPago(){
	var $linea = $(
		'<div class="co-linea-pago">' +
			'<select class="form-control co-linea-tipo">' + coOpcionesTipoPagoHtml() + '</select>' +
			'<input type="text" inputmode="decimal" class="form-control co-linea-monto" placeholder="0.00">' +
			'<input type="text" class="form-control co-linea-referencia" placeholder="Referencia" style="display:none;">' +
			'<button type="button" class="co-linea-quitar" title="Quitar este pago"><i class="fa fa-times"></i></button>' +
		'</div>'
	);

	$("#coLineasPago").append($linea);
	coActualizarCobertura();
}

// Suma de lo capturado en todas las líneas de pago (sin importar el método).
function coSumaLineasPago(){
	var _suma = 0;
	$("#coLineasPago .co-linea-monto").each(function(){
		_suma += desformatearPrecio($(this).val()) || 0;
	});
	return _suma;
}

// Solo lo asignado a líneas en Efectivo, para poder calcular el cambio de esa parte.
function coSumaLineasEfectivo(){
	var _suma = 0;
	$("#coLineasPago .co-linea-pago").each(function(){
		var $linea = $(this);
		if (coEsEfectivoLinea($linea)){
			_suma += desformatearPrecio($linea.find(".co-linea-monto").val()) || 0;
		}
	});
	return _suma;
}

// true si alguna línea ya en pantalla todavía no tiene tipo de pago o monto capturado —
// no tiene caso dejar agregar una línea nueva si la anterior sigue vacía.
function coHayLineaIncompleta(){
	var _incompleta = false;
	$("#coLineasPago .co-linea-pago").each(function(){
		var $linea = $(this);
		var _idTipoPago = $linea.find(".co-linea-tipo").val();
		var _monto = desformatearPrecio($linea.find(".co-linea-monto").val()) || 0;
		if (!_idTipoPago || _monto <= 0){
			_incompleta = true;
			return false;
		}
	});
	return _incompleta;
}

// El aviso de cobertura se evalúa siempre (con 1 línea o con varias): solo se oculta
// cuando lo capturado cuadra exactamente con el total; si falta o sobra, se avisa aunque
// sea un único método de pago.
function coActualizarCobertura(){
	var _total = Number($("#coTotalPagar").data("total")) || 0;
	var _cubierto = coSumaLineasPago();
	var _diferencia = Math.round((_total - _cubierto) * 100) / 100;

	var $resumen = $("#coCobertura");

	if (Math.abs(_diferencia) < 0.005){
		$resumen.hide();
	}else if (_diferencia > 0){
		$resumen.removeClass("co-cobertura-ok").addClass("co-cobertura-falta")
			.html("Falta cubrir $" + formatearPrecio(_diferencia))
			.show();
	}else{
		$resumen.removeClass("co-cobertura-ok").addClass("co-cobertura-falta")
			.html("Excede el total por $" + formatearPrecio(Math.abs(_diferencia)))
			.show();
	}

	var _hayEfectivo = coSumaLineasEfectivo() > 0;
	$("#coEfectivoGrupo, #coCambioGrupo").toggle(_hayEfectivo);
	calcularCambioCheckOut();

	// Se deshabilita "Agregar otro método de pago" si ya está cubierto/excedido (no tiene
	// caso seguir agregando), o si alguna línea existente todavía está incompleta (no se
	// deja apilar líneas vacías sin llenar la anterior primero).
	$("#coAgregarLineaPago").prop("disabled", _diferencia <= 0.005 || coHayLineaIncompleta());
}

function abrirModalCheckOut(idReservacion, idMotivoCancelacion){

	$("#coIdReservacion").val(idReservacion);
	$("#coIdMotivoCancelacion").val(idMotivoCancelacion || "");
	$("#coPreguntasCuerpo").html('<div class="co-pregunta-vacio text-center text-muted">Cargando…</div>').show();
	$("#coPreguntasChevron").removeClass("co-colapsado");
	$("#coConsumoCuerpo").html('<tr><td colspan="4" class="text-center text-muted" style="padding:15px;">Cargando…</td></tr>');
	$("#coConsumoTotal").text("0.00");
	$("#coTotalPagar").text("0.00").data("total", 0);
	$("#coTotalPagarTexto").text("0.00");
	$("#coMontoRecibido").val("");
	$("#coCambio").text("0.00");
	$("#coLineasPago").empty();
	coAgregarLineaPago();

	if (idMotivoCancelacion){
		$("#coModalTitulo").html('<i class="fa fa-sign-out"></i> Cancelar estadía (Check Out)');
		$("#coConfirmarTexto").text("Confirmar cancelación");
	}else{
		$("#coModalTitulo").html('<i class="fa fa-sign-out"></i> Check Out');
		$("#coConfirmarTexto").text("Confirmar Check Out");
	}

	$("#modalCheckOut").modal("show");

	$.ajax({
		url: "ajax/reservaciones.ajax.php",
		method: "GET",
		data: { accion: "tiposDePago" },
		dataType: "json",
		success: function(respuesta){
			coTiposDePago = respuesta.data || [];
			// Las líneas ya construidas (normalmente la única línea inicial) todavía no
			// tenían opciones porque este catálogo llega en paralelo al abrir el modal.
			$("#coLineasPago .co-linea-tipo").each(function(){
				var _actual = $(this).val();
				$(this).html(coOpcionesTipoPagoHtml(_actual));
			});
		}
	});

	$.ajax({
		url: "ajax/preguntas.ajax.php",
		method: "GET",
		data: { accion: "activas" },
		dataType: "json",
		success: function(respuesta){

			var preguntas = respuesta.data || [];
			var $cuerpo = $("#coPreguntasCuerpo");
			$cuerpo.empty();

			if (preguntas.length === 0){
				$cuerpo.append('<div class="co-pregunta-vacio text-center text-muted">No hay preguntas configuradas.</div>');
				return;
			}

			preguntas.forEach(function(pregunta, indice){
				$cuerpo.append(
					'<div class="co-pregunta-item">' +
						'<span class="co-pregunta-texto"><span class="co-pregunta-num">' + (indice + 1) + '.</span>' + escaparHtmlRecepcion(pregunta) + '</span>' +
						'<input type="checkbox" data-toggle="toggle" data-size="mini" data-onstyle="success" data-offstyle="default">' +
					'</div>'
				);
			});

			$cuerpo.find('input[data-toggle="toggle"]').bootstrapToggle();
		},
		error: function(){
			$("#coPreguntasCuerpo").html('<div class="co-pregunta-vacio text-center text-muted">No se pudieron cargar las preguntas.</div>');
		}
	});

	$.ajax({
		url: "ajax/reservaciones.ajax.php",
		method: "GET",
		data: { accion: "consumo", id_reservacion: idReservacion },
		dataType: "json",
		success: function(respuesta){

			var consumo = respuesta.data || [];
			var $cuerpo = $("#coConsumoCuerpo");
			$cuerpo.empty();

			// El hospedaje se muestra como un renglón más de esta tabla (no aparte); al
			// confirmar el check out, el servidor lo registra como un consumo real.
			var _hospedaje = Number(respuesta.hospedaje) || 0;
			if (_hospedaje > 0){
				$cuerpo.append(
					'<tr>' +
						'<td>Hospedaje</td>' +
						'<td>1</td>' +
						'<td>$' + formatearPrecio(_hospedaje) + '</td>' +
						'<td>$' + formatearPrecio(_hospedaje) + '</td>' +
					'</tr>'
				);
			}

			if (consumo.length === 0 && _hospedaje <= 0){
				$cuerpo.append('<tr><td colspan="4" class="text-center text-muted" style="padding:15px;">Sin consumo registrado.</td></tr>');
			}else{
				consumo.forEach(function(item){
					$cuerpo.append(
						'<tr>' +
							'<td>' + escaparHtmlRecepcion(item.producto) + '</td>' +
							'<td>' + item.cantidad + '</td>' +
							'<td>$' + formatearPrecio(item.precioVenta) + '</td>' +
							'<td>$' + formatearPrecio(item.total) + '</td>' +
						'</tr>'
					);
				});
			}

			$("#coConsumoTotal").text(formatearPrecio((respuesta.totalConsumo || 0) + _hospedaje));

			var _total = Number(respuesta.total) || 0;
			$("#coTotalPagar").text(formatearPrecio(_total)).data("total", _total);
			$("#coTotalPagarTexto").text(formatearPrecio(_total));

			coActualizarCobertura();
		},
		error: function(){
			$("#coConsumoCuerpo").html('<tr><td colspan="4" class="text-center text-muted" style="padding:15px;">No se pudo cargar el consumo.</td></tr>');
		}
	});
}

$(document).on("click", ".hc-icon-btn.checkout", function(){
	abrirModalCheckOut($(this).data("idReservacion"), null);
});

$(document).on("click", "#coPreguntasToggle", function(){
	$("#coPreguntasCuerpo").slideToggle(150);
	$("#coPreguntasChevron").toggleClass("co-colapsado");
});

// "Efectivo recibido" ya no se captura a mano: es la suma de lo capturado en TODAS las
// líneas de pago (el propio campo antes duplicaba lo que el cajero ya tecleó por línea).
// El cambio solo tiene sentido si hay alguna línea en Efectivo (es lo único que se puede
// regresar en mano), y es el sobrante cuando esa suma excede el total a cobrar.
function calcularCambioCheckOut(){

	var _recibido = coSumaLineasPago();
	var _total = Number($("#coTotalPagar").data("total")) || 0;
	var _cambio = _recibido - _total;

	$("#coMontoRecibido").val(formatearPrecio(_recibido));
	$("#coCambio").text(formatearPrecio(_cambio > 0 ? _cambio : 0));
}

$(document).on("input", ".co-linea-monto", function(){
	coFormatearInputMonto($(this));
	coActualizarCobertura();
});

// La Referencia no aplica para pago en Efectivo; para cualquier otro tipo (tarjeta,
// transferencia) sí es obligatoria.
$(document).on("change", ".co-linea-tipo", function(){
	var $linea = $(this).closest(".co-linea-pago");
	var _esEfectivo = coEsEfectivoLinea($linea);
	$linea.find(".co-linea-referencia").toggle(!_esEfectivo);
	if (_esEfectivo) $linea.find(".co-linea-referencia").val("");
	coActualizarCobertura();
});

$(document).on("click", "#coAgregarLineaPago", function(){
	coAgregarLineaPago();
});

// Siempre debe quedar al menos una línea de pago capturada.
$(document).on("click", ".co-linea-quitar", function(){
	if ($("#coLineasPago .co-linea-pago").length <= 1){
		return;
	}
	$(this).closest(".co-linea-pago").remove();
	coActualizarCobertura();
});

$(document).on("click", "#coConfirmar", function(){

	var idReservacion = $("#coIdReservacion").val();
	var idMotivoCancelacion = $("#coIdMotivoCancelacion").val();
	var _esCancelacion = !!idMotivoCancelacion;

	var _pagos = [];
	var _lineaInvalida = null;

	$("#coLineasPago .co-linea-pago").each(function(){
		var $linea = $(this);
		var _idTipoPago = $linea.find(".co-linea-tipo").val();
		var _esEfectivo = coEsEfectivoLinea($linea);
		var _monto = desformatearPrecio($linea.find(".co-linea-monto").val()) || 0;
		var _referencia = $.trim($linea.find(".co-linea-referencia").val());

		if (!_idTipoPago || _monto <= 0 || (!_esEfectivo && _referencia.length < 5)){
			_lineaInvalida = { idTipoPago: _idTipoPago, monto: _monto, esEfectivo: _esEfectivo, referencia: _referencia };
			return false;
		}

		_pagos.push({ idTipoPago: _idTipoPago, monto: _monto, referencia: _referencia, esEfectivo: _esEfectivo });
	});

	if (_lineaInvalida){
		if (!_lineaInvalida.idTipoPago){
			Swal.fire({ icon: "warning", title: "Falta el tipo de pago", text: "Selecciona cómo se realizó cada pago." });
		}else if (_lineaInvalida.monto <= 0){
			Swal.fire({ icon: "warning", title: "Falta el monto", text: "Captura un monto mayor a cero en cada línea de pago." });
		}else{
			Swal.fire({ icon: "warning", title: "Referencia incompleta", text: "Captura una referencia de al menos 5 caracteres." });
		}
		return;
	}

	var _total = Number($("#coTotalPagar").data("total")) || 0;
	var _cubierto = _pagos.reduce(function(suma, pago){ return suma + pago.monto; }, 0);
	var _excedente = Math.round((_cubierto - _total) * 100) / 100;
	var _cambio = 0;

	if (_excedente > 0.004){
		// El billete que entregó el cliente puede ser mayor al total (p.ej. paga $500 una
		// cuenta de $380): el excedente solo se puede "devolver" si viene de Efectivo, nunca
		// de tarjeta/transferencia. Si alcanza, se resta de la(s) línea(s) en Efectivo (de la
		// última hacia la primera) para que lo que se manda al servidor sí cuadre exacto con
		// el total — el campo "Cambio" en pantalla ya le mostró al cajero cuánto entregar.
		var _sumaEfectivo = _pagos.reduce(function(s, p){ return s + (p.esEfectivo ? p.monto : 0); }, 0);

		if (_excedente - _sumaEfectivo > 0.004){
			Swal.fire({
				icon: "warning",
				title: "El pago no cuadra",
				text: "Lo capturado ($" + formatearPrecio(_cubierto) + ") excede el total a cobrar ($" + formatearPrecio(_total) + ") y el sobrante no cabe en Efectivo. Solo se puede dar cambio en Efectivo; ajusta los montos de tarjeta/transferencia para que no rebasen el total."
			});
			return;
		}

		_cambio = _excedente;
		var _restante = _excedente;
		for (var i = _pagos.length - 1; i >= 0 && _restante > 0.004; i--){
			if (!_pagos[i].esEfectivo){
				continue;
			}
			var _descuento = Math.min(_pagos[i].monto, _restante);
			_pagos[i].monto = Math.round((_pagos[i].monto - _descuento) * 100) / 100;
			_restante = Math.round((_restante - _descuento) * 100) / 100;
		}
		_pagos = _pagos.filter(function(pago){ return pago.monto > 0.004; });
	}else if (_excedente < -0.004){
		Swal.fire({
			icon: "warning",
			title: "Falta el monto",
			text: "Lo capturado ($" + formatearPrecio(_cubierto) + ") no cubre el total a cobrar ($" + formatearPrecio(_total) + ")."
		});
		return;
	}

	Swal.fire({
		icon: "question",
		title: _esCancelacion ? "¿Confirmar cancelación de la estadía?" : "¿Confirmar Check Out?",
		text: _cambio > 0.004
			? "La habitación quedará disponible. Cambio a entregar: $" + formatearPrecio(_cambio) + "."
			: "La habitación quedará disponible.",
		showCancelButton: true,
		confirmButtonText: "Sí, confirmar",
		cancelButtonText: "Cancelar",
		confirmButtonColor: "#3f6b4a"
	}).then(function(resultado){

		if (!resultado.value){
			return;
		}

		mostrarCargaRecepcion();

		var _pagosJson = JSON.stringify(_pagos.map(function(pago){
			return { idTipoPago: pago.idTipoPago, monto: pago.monto, referencia: pago.referencia };
		}));

		var _datos = _esCancelacion
			? {
				accion: "cancelarConCheckout",
				id_reservacion: idReservacion,
				id_motivo: idMotivoCancelacion,
				pagos: _pagosJson
			}
			: {
				accion: "checkout",
				id_reservacion: idReservacion,
				pagos: _pagosJson
			};

		$.ajax({
			url: "ajax/reservaciones.ajax.php",
			method: "POST",
			data: _datos,
			dataType: "json",
			success: function(respuesta){
				if (respuesta.ok){
					$("#modalCheckOut").modal("hide");
					Swal.fire({
						icon: "success",
						title: _esCancelacion ? "Estadía cancelada" : "Check out completado",
						text: _esCancelacion && respuesta.folio ? "Folio de cancelación: " + respuesta.folio : undefined,
						timer: 2500,
						showConfirmButton: false
					}).then(function(){
						coOfrecerEnvioTicketWhatsapp(idReservacion);
					});
					refrescarRecepcion();
				}else{
					Swal.fire({ icon: "error", title: _esCancelacion ? "No se pudo cancelar" : "No se pudo completar", text: respuesta.mensaje });
				}
			},
			error: function(){
				Swal.fire({ icon: "error", title: "Error", text: "No se pudo completar el check out. Intenta de nuevo." });
			},
			complete: ocultarCargaRecepcion
		});
	});
});

// Al terminar un checkout (o cancelación con checkout), el ticket de la estadía (hospedaje +
// todo el consumo cargado) se manda por WhatsApp SIEMPRE, sin preguntar ni pedir el teléfono
// a mano — TicketReservacion.php ya lo toma directo del cliente en cat_clientes. Mismo patrón
// ya usado en Punto de Venta (ver views/js/ventas.js), reutilizando la misma cuenta de
// WhatsApp ya contratada para el sistema.
function coOfrecerEnvioTicketWhatsapp(idReservacion){
	$.ajax({
		url: "extensions/tcpdf/Reportes/TicketReservacion.php",
		method: "GET",
		data: { IdReservacion: idReservacion },
		dataType: "json"
	}).then(function(respuesta){
		if (respuesta && respuesta.ok){
			Swal.fire({ icon: "success", title: "Ticket enviado por WhatsApp", timer: 1800, showConfirmButton: false });
		}else{
			// No se asume éxito solo porque la petición HTTP funcionó: la API de WhatsApp
			// puede responder 200 y aun así no haber entregado el mensaje (token vencido,
			// cliente sin teléfono guardado, número mal formateado, instancia desconectada,
			// etc.). El check-out ya quedó completado de cualquier forma; esto solo avisa que
			// el ticket no llegó, para que se mande a mano por otro medio si hace falta.
			Swal.fire({
				icon: "error",
				title: "No se pudo enviar el ticket",
				text: (respuesta && respuesta.mensaje) || "La API de WhatsApp no confirmó el envío."
			});
		}
	}, function(){
		Swal.fire({
			icon: "error",
			title: "No se pudo enviar el ticket",
			text: "No se pudo contactar al servidor."
		});
	});
}

// Refuerzo: además de resetearse al ABRIR el modal (arriba), también se resetea al
// CERRARSE (se haya confirmado el check out o no), para que nunca quede "Referencia"
// visible de una elección anterior si el modal se vuelve a abrir.
$("#modalCheckOut").on("hidden.bs.modal", function(){
	$("#coLineasPago").empty();
	coAgregarLineaPago();
	$("#coCobertura").hide();
	$("#coEfectivoGrupo, #coCambioGrupo").hide();
	$("#coMontoRecibido").val("");
	$("#coCambio").text("0.00");
	$("#coIdMotivoCancelacion").val("");
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

	$("#nrFechaEntradaHora, #nrFechaSalidaHora, #mrFechaEntradaHora, #mrFechaSalidaHora").html(horas);
	$("#nrFechaEntradaMin, #nrFechaSalidaMin, #mrFechaEntradaMin, #mrFechaSalidaMin").html(minutos);
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

// Mismo patrón que nrSincronizarFecha, para los selects día/hora/minuto del modal "Mover reservación".
function mrSincronizarFecha(prefijo){
	var dia = $("#mrFecha" + prefijo + "Dia").val();
	var hora = $("#mrFecha" + prefijo + "Hora").val();
	var minuto = $("#mrFecha" + prefijo + "Min").val();

	var valor = dia ? (dia + "T" + hora + ":" + minuto) : "";
	$("#mrFecha" + prefijo).val(valor);
}

$(document).on("change", "#mrFechaEntradaDia, #mrFechaEntradaHora, #mrFechaEntradaMin", function(){
	mrSincronizarFecha("Entrada");
	mrCalcularPrecio();
});
$(document).on("change", "#mrFechaSalidaDia, #mrFechaSalidaHora, #mrFechaSalidaMin", function(){
	mrSincronizarFecha("Salida");
	mrCalcularPrecio();
});

// Mismo cálculo que nrCalcularPrecio, pero para el modal "Mover reservación": noches ×
// precio por noche de la habitación (que ya no se puede cambiar en este modal).
function mrCalcularPrecio(){
	var entrada = $("#mrFechaEntrada").val();
	var salida = $("#mrFechaSalida").val();
	var precioNoche = parseFloat($("#mrPrecioNoche").val()) || 0;

	if (!entrada || !salida || !precioNoche){
		$("#mrPrecio").val("");
		return;
	}

	var tsEntrada = new Date(entrada).getTime();
	var tsSalida = new Date(salida).getTime();

	if (isNaN(tsEntrada) || isNaN(tsSalida) || tsSalida <= tsEntrada){
		$("#mrPrecio").val("");
		return;
	}

	var noches = Math.ceil((tsSalida - tsEntrada) / (1000 * 60 * 60 * 24));
	if (noches < 1){
		noches = 1;
	}

	$("#mrPrecio").val(formatearPrecio(noches * precioNoche));
}

function nrCalcularPrecio(){
	var entrada = $("#nrFechaEntrada").val();
	var salida = $("#nrFechaSalida").val();
	var precioNoche = parseFloat($("#nrPrecioNoche").val()) || 0;

	if (!entrada || !salida || !precioNoche){
		// Sin entrada/salida completas no hay precio válido que mostrar: se limpia en vez
		// de dejar pegado el último cálculo (ej. si borran la fecha de salida ya elegida).
		$("#nrPrecio").val("");
		return;
	}

	var tsEntrada = new Date(entrada).getTime();
	var tsSalida = new Date(salida).getTime();

	if (isNaN(tsEntrada) || isNaN(tsSalida) || tsSalida <= tsEntrada){
		$("#nrPrecio").val("");
		return;
	}

	var noches = Math.ceil((tsSalida - tsEntrada) / (1000 * 60 * 60 * 24));
	if (noches < 1){
		noches = 1;
	}

	$("#nrPrecio").val(formatearPrecio(noches * precioNoche));
}

$(document).on("click", ".hc-arrow", function(){

	var $arrow = $(this);

	$("#formNuevaReservacion")[0].reset();
	// nrCalcularPrecio() no limpia #nrPrecio si faltan entrada/salida (solo calcula cuando
	// ambas están completas), así que hay que vaciarlo explícitamente aquí y no depender
	// del reset del <form> (el precio de la reservación anterior se quedaba pegado).
	$("#nrPrecio").val("");
	$("#nrIdClienteSeleccionado").val("");
	$("#nrResultadosCliente").hide().empty();
	$("#nrClienteSeleccionadoTexto").hide();
	$("#nrClienteNuevoCampos").hide();
	$("#nrBuscarCliente").val("").prop("disabled", false);
	$("#nrToggleClienteNuevo").show();

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

	// Seguro adicional: si el navegador repone un valor "recordado" en #nrPrecio justo al
	// volverse visible el modal (autocompletado fuera de nuestro control), se limpia de
	// nuevo un tick después de mostrarlo.
	setTimeout(function(){ $("#nrPrecio").val(""); }, 0);
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
		$("#nrNombre, #nrApaterno, #nrAmaterno").val("");
	}
});

// La validación de cliente duplicado (nombre completo o teléfono) ya NO se hace en vivo
// mientras se escribe: con 4 campos buscando en el servidor por cada tecla, el usuario podía
// seguir escribiendo mientras la búsqueda anterior seguía en curso y disparar una petición
// nueva por cada carácter, saturando el servidor. Ahora solo se valida una vez, del lado del
// servidor, al dar clic en "Agregar reservación" (ControladorReservaciones::crtCrearReservacion
// ya la bloquea ahí y regresa el mismo mensaje de "ya existe" si encuentra coincidencia).
$(document).on("input", "#nrTelefono", function(){
	this.value = this.value.replace(/\D/g, "").slice(0, 10);
});

$(document).on("change", "#nrFechaEntrada, #nrFechaSalida", nrCalcularPrecio);

$(document).on("click", "#nrGuardar", function(){

	var datos = {
		id_habitacion: $("#nrIdHabitacion").val(),
		fecha_entrada: $("#nrFechaEntrada").val(),
		fecha_salida: $("#nrFechaSalida").val(),
		// #nrPrecio se muestra con comas de miles (ej. "1,400.00"); se le quitan antes de
		// mandarlo al servidor para que (float) lo interprete completo, no solo hasta la coma.
		precio: desformatearPrecio($("#nrPrecio").val()),
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

	// El duplicado (mismo nombre completo o mismo teléfono) ya no se detecta en vivo mientras
	// se escribe — se valida una sola vez aquí, del lado del servidor
	// (ControladorReservaciones::crtCrearReservacion), que bloquea el guardado y regresa el
	// mensaje de "ya existe" en respuesta.mensaje si encuentra coincidencia.
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
