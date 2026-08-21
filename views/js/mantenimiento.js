function escaparHtmlMtto(valor){
	valor = valor == null ? "" : String(valor);
	return valor
		.replace(/&/g, "&amp;")
		.replace(/</g, "&lt;")
		.replace(/>/g, "&gt;")
		.replace(/"/g, "&quot;")
		.replace(/'/g, "&#39;");
}

/*=============================================
 Detalle de la incidencia (encargado y fechas)
 =============================================*/
$(document).on("click", ".btnDetalleMtto", function(){

	var _idMantenimiento = $(this).attr("idMantenimiento");

	$("#mttoDetalleContenido").html('<p class="text-muted text-center" style="padding:20px;">Cargando…</p>');
	$("#mttoDetalleSubtitulo").html("");
	$("#modalDetalleMantenimiento").modal("show");

	$.ajax({
		url: "ajax/mantenimiento-detalle.ajax.php",
		method: "GET",
		data: { idMantenimiento: _idMantenimiento },
		dataType: "json",
		success: function(respuesta){

			if (!respuesta || respuesta.status !== "success") {
				$("#mttoDetalleContenido").html('<p class="mtto-detalle-vacio text-center">No se pudo cargar el detalle.</p>');
				return;
			}

			var d = respuesta.data;

			var _vecesReabiertaTexto = d.vecesReabierta > 0 ? (d.vecesReabierta + (d.vecesReabierta === 1 ? " vez" : " veces")) : "nunca se ha reabierto";
			$("#mttoDetalleSubtitulo").html(
				'<i class="fa fa-user"></i> Persona encargada: ' + escaparHtmlMtto(d.encargado) +
				' &nbsp;·&nbsp; <i class="fa fa-undo"></i> Se ha reabierto: ' + escaparHtmlMtto(_vecesReabiertaTexto)
			);

			var _columnas = [
				{ titulo: "Pieza / parte afectada", valor: escaparHtmlMtto(d.pieza) },
				{ titulo: "Proveedor", valor: d.proveedor ? escaparHtmlMtto(d.proveedor) : '<span class="mtto-bitacora-vacio">Sin especificar</span>' },
				{ titulo: "Fecha de registro", valor: escaparHtmlMtto(d.fechaRegistro) },
				{ titulo: "Fecha inicio estimada", valor: escaparHtmlMtto(d.fechaInicioEstimado) },
				{ titulo: "Fecha fin estimada", valor: escaparHtmlMtto(d.fechaFinEstimado) },
				{ titulo: "Resuelto (real)", valor: d.fechaResuelto ? escaparHtmlMtto(d.fechaResuelto) : '<span class="mtto-bitacora-vacio">Aún no se resuelve</span>' }
			];

			if (d.vecesReabierta > 0) {
				_columnas.push({ titulo: "Por qué se volvió a reabrir", valor: d.notaReapertura ? escaparHtmlMtto(d.notaReapertura) : '<span class="mtto-bitacora-vacio">Sin nota</span>' });
			}

			_columnas.push({ titulo: "Foto", valor: d.foto ? ('<button type="button" class="mtto-bitacora-ver-foto btnVerFotoBitacora" data-foto="' + escaparHtmlMtto(d.foto) + '" data-titulo="Foto de la incidencia">Ver foto</button>') : '<span class="mtto-bitacora-vacio">Sin foto</span>' });
			_columnas.push({ titulo: "Foto de resultado", valor: d.fotoResuelto ? ('<button type="button" class="mtto-bitacora-ver-foto btnVerFotoBitacora" data-foto="' + escaparHtmlMtto(d.fotoResuelto) + '" data-titulo="Foto de cómo quedó resuelta la incidencia">Ver foto</button>') : '<span class="mtto-bitacora-vacio">Aún no se resuelve</span>' });

			var html = '<div style="overflow-x:auto;"><table class="mtto-bitacora-tabla"><thead><tr>' +
				_columnas.map(function(c){ return '<th>' + c.titulo + '</th>'; }).join("") +
				'</tr></thead><tbody><tr>' +
				_columnas.map(function(c){ return '<td>' + c.valor + '</td>'; }).join("") +
				'</tr></tbody></table></div>';

			$("#mttoDetalleContenido").html(html);
		},
		error: function(){
			$("#mttoDetalleContenido").html('<p class="mtto-detalle-vacio text-center">No se pudo cargar el detalle.</p>');
		}
	});
});

/*=============================================
 Aviso "Pendiente de liquidar" al cargar el tablero
 (incidencias Resueltas que todavía tienen saldo por cobrar)
 =============================================*/
$(document).ready(function(){
	if (typeof MTTO_PENDIENTES_LIQUIDAR === "undefined" || !MTTO_PENDIENTES_LIQUIDAR.length) {
		return;
	}

	// Solo se muestra la primera vez que se entra al módulo en esta pestaña;
	// no se repite en cada F5/recarga automática.
	if (sessionStorage.getItem("mttoPendienteLiquidarMostrado")) {
		return;
	}
	sessionStorage.setItem("mttoPendienteLiquidarMostrado", "1");

	var _lista = MTTO_PENDIENTES_LIQUIDAR.map(function(p){
		return "Habitación " + escaparHtmlMtto(p.habitacion) + ": " + formatearMontoMtto(p.saldoRestante);
	}).join("<br>");

	Swal.fire({
		icon: "warning",
		title: "Pendiente de liquidar",
		html: "Estas incidencias ya están resueltas pero aún tienen saldo por cobrar:<br><br>" + _lista,
		confirmButtonText: "Entendido"
	});
});

/*=============================================
 Abonos de la incidencia
 =============================================*/
function formatearMontoMtto(valor){
	return "$" + Number(valor || 0).toLocaleString("es-MX", { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function cargarAbonosMtto(idMantenimiento){
	$("#mttoAbonosContenido").html('<p class="text-muted text-center" style="padding:20px;">Cargando…</p>');

	$.ajax({
		url: "ajax/mantenimiento-abonos.ajax.php",
		method: "GET",
		data: { idMantenimiento: idMantenimiento },
		dataType: "json",
		success: function(respuesta){

			if (!respuesta || respuesta.status !== "success") {
				$("#mttoAbonosContenido").html('<p class="mtto-abonos-vacio">No se pudo cargar la información de abonos.</p>');
				return;
			}

			var d = respuesta.data;

			var html = '<div class="mtto-abonos-resumen">' +
				'<div class="mtto-abonos-tarjeta"><div class="mtto-abonos-tarjeta-label">Saldo inicial</div><div class="mtto-abonos-tarjeta-valor">' + formatearMontoMtto(d.saldoInicial) + '</div></div>' +
				'<div class="mtto-abonos-tarjeta"><div class="mtto-abonos-tarjeta-label">Saldo restante</div><div class="mtto-abonos-tarjeta-valor">' + formatearMontoMtto(d.saldoRestante) + '</div></div>' +
				'<div class="mtto-abonos-tarjeta"><div class="mtto-abonos-tarjeta-label"># Abonos</div><div class="mtto-abonos-tarjeta-valor">' + d.numAbonos + '</div></div>' +
				'</div>';

			if (d.abonos.length === 0) {
				html += '<p class="mtto-abonos-vacio">Todavía no se han registrado abonos.</p>';
			} else {
				html += '<table class="mtto-abonos-tabla"><thead><tr>' +
					'<th>Fecha</th><th>Monto</th><th>Registró</th>' +
					'</tr></thead><tbody>';

				d.abonos.forEach(function(a){
					html += '<tr>' +
						'<td>' + escaparHtmlMtto(a.fecha) + '</td>' +
						'<td>' + formatearMontoMtto(a.monto) + '</td>' +
						'<td>' + escaparHtmlMtto(a.usuario) + '</td>' +
						'</tr>';
				});

				html += '</tbody></table>';
			}

			$("#mttoAbonosContenido").html(html);

			var _saldoRestante = Number(d.saldoRestante || 0);
			$("#abonoMonto").attr("max", _saldoRestante > 0 ? _saldoRestante.toFixed(2) : 0);
			$("#abonoMontoAyuda").text(_saldoRestante > 0.009 ? ("Máximo a abonar: " + formatearMontoMtto(_saldoRestante)) : "Esta incidencia ya está completamente liquidada.");
		},
		error: function(){
			$("#mttoAbonosContenido").html('<p class="mtto-abonos-vacio">No se pudo cargar la información de abonos.</p>');
		}
	});
}

/*=============================================
 Formato con comas y decimales para el "Costo estimado"
 al registrar una nueva incidencia
 =============================================*/
$(document).on("input", "#nuevoCostoMtto", function(){
	var _valor = $(this).val();

	// Solo dígitos y un único punto decimal, máximo 2 decimales
	_valor = _valor.replace(/[^0-9.]/g, "");
	var _partes = _valor.split(".");
	if (_partes.length > 2) {
		_valor = _partes[0] + "." + _partes.slice(1).join("");
	}
	_partes = _valor.split(".");
	if (_partes[1] !== undefined) {
		_partes[1] = _partes[1].slice(0, 2);
	}

	var _entero = _partes[0].replace(/^0+(?=\d)/, "");
	var _enteroFormateado = _entero === "" ? "" : Number(_entero).toLocaleString("es-MX");

	$(this).val(_partes.length > 1 ? (_enteroFormateado + "." + _partes[1]) : _enteroFormateado);
});

$(document).on("blur", "#nuevoCostoMtto", function(){
	var _crudo = parseFloat($(this).val().replace(/,/g, ""));
	if (!isNaN(_crudo)) {
		$(this).val(_crudo.toLocaleString("es-MX", { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
	}
});

$(document).on("submit", "#modalAgregarMantenimiento form", function(){
	var _costo = $("#nuevoCostoMtto").val().replace(/,/g, "").replace(/\.$/, "");
	$("#nuevoCostoMtto").val(_costo);
});

$(document).on("input", "#abonoMonto", function(){
	var _max = parseFloat($(this).attr("max"));
	var _valor = parseFloat($(this).val());

	if (!isNaN(_max) && !isNaN(_valor) && _valor > _max) {
		$(this).val(_max.toFixed(2));
	}
});

$(document).on("click", ".btnAbonosMtto", function(){

	var _idMantenimiento = $(this).attr("idMantenimiento");

	$("#formAbonoMtto")[0].reset();
	$("#abonoIdMantenimiento").val(_idMantenimiento);

	$("#modalAbonosMantenimiento").modal("show");
	cargarAbonosMtto(_idMantenimiento);
});

$(document).on("submit", "#formAbonoMtto", function(e){
	e.preventDefault();

	var _form = this;
	var _idMantenimiento = $("#abonoIdMantenimiento").val();
	var _formData = new FormData(_form);

	$.ajax({
		url: "ajax/mantenimiento-abono-insertar.ajax.php",
		method: "POST",
		data: _formData,
		processData: false,
		contentType: false,
		dataType: "json",
		success: function(respuesta){
			if (respuesta && respuesta.status === "success") {
				_form.reset();
				$("#abonoIdMantenimiento").val(_idMantenimiento);
				cargarAbonosMtto(_idMantenimiento);

				if (respuesta.pagada) {
					Swal.fire({
						icon: "success",
						title: '<span style="color:#3f7649">Pagada</span>',
						html: "El abono se registró y esta incidencia ya quedó completamente liquidada.",
						confirmButtonText: "Cerrar",
						confirmButtonColor: "#3f7649"
					}).then(function(){
						window.location.reload();
					});
				} else {
					Swal.fire({
						icon: "success",
						title: "Sistema PosDit",
						text: respuesta.message || "Abono registrado correctamente",
						timer: 1500,
						showConfirmButton: false
					});
				}
			} else {
				Swal.fire({
					icon: "error",
					title: "Sistema PosDit",
					text: (respuesta && respuesta.message) || "No se pudo registrar el abono",
					confirmButtonText: "Cerrar"
				});
			}
		},
		error: function(){
			Swal.fire({
				icon: "error",
				title: "Sistema PosDit",
				text: "Error al registrar el abono",
				confirmButtonText: "Cerrar"
			});
		}
	});
});

/*=============================================
 Bitácora de incidencias de la habitación
 =============================================*/
function claseEstatusBitacoraMtto(estatus){
	return "mtto-bitacora-estatus-" + String(estatus || "otro").toLowerCase().replace(/\s+/g, "-");
}

$(document).on("click", ".btnBitacoraIncidencias", function(){

	var _idMantenimiento = $(this).attr("idMantenimiento");

	$("#mttoBitacoraIncidenciasContenido").html('<p class="text-muted text-center" style="padding:20px;">Cargando…</p>');
	$("#modalBitacoraIncidencias").modal("show");

	$.ajax({
		url: "ajax/mantenimiento-bitacora-incidencias.ajax.php",
		method: "GET",
		data: { idMantenimiento: _idMantenimiento },
		dataType: "json",
		success: function(respuesta){

			if (!respuesta || respuesta.status !== "success") {
				$("#mttoBitacoraIncidenciasContenido").html('<p class="mtto-bitacora-vacio">No se pudo cargar la bitácora.</p>');
				return;
			}

			var lista = respuesta.data;

			if (lista.length === 0) {
				$("#mttoBitacoraIncidenciasContenido").html('<p class="mtto-bitacora-vacio">Esta habitación no tiene incidencias registradas.</p>');
				return;
			}

			var html = '<table class="mtto-bitacora-tabla"><thead><tr>' +
				'<th>Fecha</th><th>Descripción</th><th>Proveedor</th><th>Estatus</th><th>Foto</th><th>Foto resultado</th>' +
				'</tr></thead><tbody>';

			lista.forEach(function(i){
				html += '<tr>' +
					'<td>' + escaparHtmlMtto(i.fecha) + '</td>' +
					'<td>' + escaparHtmlMtto(i.descripcion) + '</td>' +
					'<td>' + (i.proveedor ? escaparHtmlMtto(i.proveedor) : '<span class="mtto-bitacora-vacio">Sin especificar</span>') + '</td>' +
					'<td><span class="mtto-bitacora-estatus ' + claseEstatusBitacoraMtto(i.estatus) + '">' + escaparHtmlMtto(i.estatus) + '</span></td>' +
					'<td>' + (i.foto ? ('<button type="button" class="mtto-bitacora-ver-foto btnVerFotoBitacora" data-foto="' + escaparHtmlMtto(i.foto) + '" data-titulo="Foto de la incidencia">Ver foto</button>') : '<span class="mtto-bitacora-vacio">Sin foto</span>') + '</td>' +
					'<td>' + (i.fotoResuelto ? ('<button type="button" class="mtto-bitacora-ver-foto btnVerFotoBitacora" data-foto="' + escaparHtmlMtto(i.fotoResuelto) + '" data-titulo="Foto de cómo quedó resuelta la incidencia">Ver foto</button>') : '<span class="mtto-bitacora-vacio">Sin foto</span>') + '</td>' +
					'</tr>';
			});

			html += '</tbody></table>';

			$("#mttoBitacoraIncidenciasContenido").html(html);
		},
		error: function(){
			$("#mttoBitacoraIncidenciasContenido").html('<p class="mtto-bitacora-vacio">No se pudo cargar la bitácora.</p>');
		}
	});
});

/*=============================================
 Soporte para modales apilados (Bootstrap 3 no lo maneja solo: el popup de
 foto se abre encima de la bitácora, no detrás). Sube el z-index del modal y
 su backdrop cada vez que se abre uno mientras ya hay otro visible.
 =============================================*/
$(document).on("show.bs.modal", ".modal", function(){
	var _abiertos = $(".modal.in").length;

	if (_abiertos > 0) {
		var _zIndex = 1040 + (_abiertos * 20);
		$(this).css("z-index", _zIndex + 10);

		$(this).one("shown.bs.modal", function(){
			$(".modal-backdrop").not(".mtto-backdrop-apilado").last()
				.css("z-index", _zIndex)
				.addClass("mtto-backdrop-apilado");
		});
	}
});

/*=============================================
 Bitácora de incidencias eliminadas (historial global del hotel)
 =============================================*/
$(document).on("click", ".btnBitacoraEliminadas", function(){

	$("#mttoBitacoraEliminadasContenido").html('<p class="text-muted text-center" style="padding:20px;">Cargando…</p>');
	$("#modalBitacoraEliminadas").modal("show");

	$.ajax({
		url: "ajax/mantenimiento-bitacora-eliminadas.ajax.php",
		method: "GET",
		dataType: "json",
		success: function(respuesta){

			if (!respuesta || respuesta.status !== "success") {
				$("#mttoBitacoraEliminadasContenido").html('<p class="mtto-bitacora-vacio">No se pudo cargar la bitácora.</p>');
				return;
			}

			var lista = respuesta.data;

			if (lista.length === 0) {
				$("#mttoBitacoraEliminadasContenido").html('<p class="mtto-bitacora-vacio">Todavía no se ha eliminado ninguna incidencia.</p>');
				return;
			}

			var html = '<table class="mtto-bitacora-tabla"><thead><tr>' +
				'<th>Fecha</th><th>Habitación</th><th>Descripción</th><th>Motivo</th>' +
				'</tr></thead><tbody>';

			lista.forEach(function(e){
				html += '<tr>' +
					'<td>' + escaparHtmlMtto(e.fecha) + '</td>' +
					'<td>' + escaparHtmlMtto(e.habitacion) + '</td>' +
					'<td>' + escaparHtmlMtto(e.descripcion) + '</td>' +
					'<td>' + escaparHtmlMtto(e.motivo) + '</td>' +
					'</tr>';
			});

			html += '</tbody></table>';

			$("#mttoBitacoraEliminadasContenido").html(html);
		},
		error: function(){
			$("#mttoBitacoraEliminadasContenido").html('<p class="mtto-bitacora-vacio">No se pudo cargar la bitácora.</p>');
		}
	});
});

/*=============================================
 Popup de foto de la bitácora de incidencias
 =============================================*/
$(document).on("click", ".btnVerFotoBitacora", function(){
	var _foto = $(this).attr("data-foto");
	var _titulo = $(this).attr("data-titulo") || "Foto";
	$("#mttoFotoBitacoraImg").attr("src", _foto);
	$("#mttoFotoBitacoraTitulo").html('<i class="fa fa-camera"></i> ' + escaparHtmlMtto(_titulo));
	$("#modalFotoBitacora").modal("show");
});

/*=============================================
 Bitácora de abonos de la habitación
 =============================================*/
$(document).on("click", ".btnBitacoraAbonos", function(){

	var _idMantenimiento = $(this).attr("idMantenimiento");

	$("#mttoBitacoraAbonosContenido").html('<p class="text-muted text-center" style="padding:20px;">Cargando…</p>');
	$("#modalBitacoraAbonos").modal("show");

	$.ajax({
		url: "ajax/mantenimiento-bitacora-abonos.ajax.php",
		method: "GET",
		data: { idMantenimiento: _idMantenimiento },
		dataType: "json",
		success: function(respuesta){

			if (!respuesta || respuesta.status !== "success") {
				$("#mttoBitacoraAbonosContenido").html('<p class="mtto-bitacora-vacio">No se pudo cargar la bitácora.</p>');
				return;
			}

			var lista = respuesta.data;

			if (lista.length === 0) {
				$("#mttoBitacoraAbonosContenido").html('<p class="mtto-bitacora-vacio">Esta habitación no tiene abonos registrados.</p>');
				return;
			}

			var html = '<table class="mtto-bitacora-tabla"><thead><tr>' +
				'<th>Fecha</th><th>Monto</th><th>Incidencia</th><th>Registró</th><th>Ticket</th>' +
				'</tr></thead><tbody>';

			lista.forEach(function(a){
				html += '<tr>' +
					'<td>' + escaparHtmlMtto(a.fecha) + '</td>' +
					'<td>' + formatearMontoMtto(a.monto) + '</td>' +
					'<td>' + escaparHtmlMtto(a.descripcion) + '</td>' +
					'<td>' + escaparHtmlMtto(a.usuario) + '</td>' +
					'<td>' + (a.foto ? ('<button type="button" class="mtto-bitacora-ver-foto btnVerFotoBitacora" data-foto="' + escaparHtmlMtto(a.foto) + '" data-titulo="Foto del ticket">Ver foto</button>') : '<span class="mtto-bitacora-vacio">Sin foto</span>') + '</td>' +
					'</tr>';
			});

			html += '</tbody></table>';

			$("#mttoBitacoraAbonosContenido").html(html);
		},
		error: function(){
			$("#mttoBitacoraAbonosContenido").html('<p class="mtto-bitacora-vacio">No se pudo cargar la bitácora.</p>');
		}
	});
});

/*=============================================
 Contador de caracteres y palabras de la descripción
 (mínimo 10 palabras, máximo 255 caracteres)
 =============================================*/
function contarPalabrasMtto(texto){
	var _palabras = String(texto || "").trim().split(/\s+/).filter(function(p){ return p.length > 0; });
	return _palabras.length;
}

function actualizarContadorDescripcionMtto(campo){
	var _longitud = campo.value.length;
	var _palabras = contarPalabrasMtto(campo.value);
	var _contador = $("#contadorNuevaDescripcionMtto");

	_contador.text(_longitud + "/255 caracteres · " + _palabras + " palabras (mínimo 10)");

	if (_palabras < 10 || _longitud >= 255) {
		_contador.removeClass("text-muted").addClass("text-danger");
	} else {
		_contador.removeClass("text-danger").addClass("text-muted");
	}
}

$(document).on("input", "#nuevaDescripcionMtto", function(){
	actualizarContadorDescripcionMtto(this);
});

$(document).on("submit", "#modalAgregarMantenimiento form", function(e){
	var _campo = document.getElementById("nuevaDescripcionMtto");
	var _campoFoto = document.querySelector(".nuevaFotoMtto");

	if (contarPalabrasMtto(_campo.value) < 10) {
		e.preventDefault();
		actualizarContadorDescripcionMtto(_campo);
		Swal.fire({
			icon: "error",
			title: "Sistema PosDit",
			text: "La descripción es obligatoria y debe tener al menos 10 palabras.",
			confirmButtonText: "Cerrar"
		});
		return;
	}

	if (!_campoFoto.files || _campoFoto.files.length === 0) {
		e.preventDefault();
		Swal.fire({
			icon: "error",
			title: "Sistema PosDit",
			text: "¡La foto es obligatoria y no puede pesar más de 3MB!",
			confirmButtonText: "Cerrar"
		});
	}
});

/*=============================================
 Aviso instantáneo si la foto de la incidencia pesa más de 3MB
 (sin esperar a que se envíe el formulario)
 =============================================*/
$(document).on("change", ".nuevaFotoMtto", function(){
	if (this.files && this.files[0] && this.files[0].size > 3 * 1024 * 1024) {
		Swal.fire({
			icon: "error",
			title: "Sistema PosDit",
			text: "¡La foto no puede pesar más de 3MB!",
			confirmButtonText: "Cerrar"
		});
		this.value = "";
	}
});

/*=============================================
 La fecha fin del formulario no puede ser anterior a la fecha inicio
 =============================================*/
$(document).on("change", "input[name='nuevaFechaInicioMtto']", function(){
	$("input[name='nuevaFechaFinMtto']").attr("min", $(this).val());
});

/*=============================================
 Cambiar estatus (Iniciar / Marcar resuelto / Reabrir)
 =============================================*/
function enviarCambioEstatusMtto(idMantenimiento, idEstatus, notaReapertura, fotoResuelto){
	var _datosEnvio;

	if (fotoResuelto) {
		_datosEnvio = new FormData();
		_datosEnvio.append("idMantenimiento", idMantenimiento);
		_datosEnvio.append("idEstatus", idEstatus);
		_datosEnvio.append("notaReapertura", notaReapertura || "");
		_datosEnvio.append("fotoResuelto", fotoResuelto);
	} else {
		_datosEnvio = { idMantenimiento: idMantenimiento, idEstatus: idEstatus, notaReapertura: notaReapertura || "" };
	}

	$.ajax({
		url: "ajax/mantenimiento-cambiar-estatus.ajax.php",
		method: "POST",
		data: _datosEnvio,
		processData: !fotoResuelto,
		contentType: fotoResuelto ? false : "application/x-www-form-urlencoded; charset=UTF-8",
		dataType: "json",
		success: function(respuesta){
			if (respuesta && respuesta.status === "success") {
				window.location.reload();
			} else {
				Swal.fire({
					icon: "error",
					title: "Sistema PosDit",
					text: (respuesta && respuesta.message) || "No se pudo actualizar la incidencia",
					confirmButtonText: "Cerrar"
				});
			}
		},
		error: function(){
			Swal.fire({
				icon: "error",
				title: "Sistema PosDit",
				text: "Error al actualizar la incidencia",
				confirmButtonText: "Cerrar"
			});
		}
	});
}

$(document).on("click", ".btnCambiarEstatus", function(){

	var _idMantenimiento = $(this).attr("idMantenimiento");
	var _idEstatus = $(this).attr("idEstatus");

	if (typeof MTTO_ESTATUS_PENDIENTE !== "undefined" && String(_idEstatus) === String(MTTO_ESTATUS_PENDIENTE)) {
		Swal.fire({
			title: "Sistema PosDit",
			text: "¿Por qué se vuelve a reabrir esta incidencia?",
			icon: "question",
			input: "textarea",
			inputPlaceholder: "Describe el motivo de la reapertura",
			inputValidator: function(value){
				if (!value || !value.trim()) {
					return "Debes escribir el motivo de la reapertura";
				}
			},
			showCancelButton: true,
			confirmButtonColor: "#81412d",
			cancelButtonColor: "#3f342e",
			confirmButtonText: "Reabrir",
			cancelButtonText: "Cancelar"
		}).then(function(result){
			if (result.isConfirmed) {
				enviarCambioEstatusMtto(_idMantenimiento, _idEstatus, result.value.trim());
			}
		});
		return;
	}

	if (typeof MTTO_ESTATUS_RESUELTO !== "undefined" && String(_idEstatus) === String(MTTO_ESTATUS_RESUELTO)) {
		Swal.fire({
			title: "Sistema PosDit",
			text: "Sube una foto de cómo quedó la incidencia ya resuelta",
			icon: "question",
			input: "file",
			inputAttributes: {
				accept: "image/*"
			},
			inputValidator: function(value){
				if (!value) {
					return "Debes adjuntar la foto de cómo quedó resuelta la incidencia";
				}
				if (value.size > 3 * 1024 * 1024) {
					return "La foto no puede pesar más de 3MB";
				}
			},
			showCancelButton: true,
			confirmButtonColor: "#3f7649",
			cancelButtonColor: "#3f342e",
			confirmButtonText: "Marcar resuelto",
			cancelButtonText: "Cancelar"
		}).then(function(result){
			if (result.isConfirmed) {
				enviarCambioEstatusMtto(_idMantenimiento, _idEstatus, null, result.value);
			}
		});
		return;
	}

	enviarCambioEstatusMtto(_idMantenimiento, _idEstatus, null);
});

/*=============================================
 Reordenar dentro de la misma columna
 =============================================*/
$(document).on("click", ".btnMoverOrden", function(){

	var _idMantenimiento = $(this).attr("idMantenimiento");
	var _direccion = $(this).attr("direccion");

	$.ajax({
		url: "ajax/mantenimiento-mover-orden.ajax.php",
		method: "POST",
		data: { idMantenimiento: _idMantenimiento, direccion: _direccion },
		dataType: "json",
		success: function(respuesta){
			if (respuesta && respuesta.status === "success") {
				window.location.reload();
			}
			// status "sin_cambios": ya está en un extremo de la columna, no hay nada que hacer.
		},
		error: function(){
			Swal.fire({
				icon: "error",
				title: "Sistema PosDit",
				text: "Error al reordenar la incidencia",
				confirmButtonText: "Cerrar"
			});
		}
	});
});

/*=============================================
 Eliminar incidencia
 =============================================*/
$(document).on("click", ".btnEliminarMtto", function(){

	var _idMantenimiento = $(this).attr("idMantenimiento");

	Swal.fire({
		title: "Sistema PosDit",
		text: "¿Por qué se elimina esta incidencia de mantenimiento?",
		icon: "warning",
		input: "select",
		inputOptions: (typeof MTTO_MOTIVOS !== "undefined") ? MTTO_MOTIVOS : {},
		inputPlaceholder: "-- Selecciona un motivo --",
		inputValidator: function(value){
			if (!value) {
				return "Debes seleccionar un motivo";
			}
		},
		showCancelButton: true,
		confirmButtonColor: "#c85c3c",
		cancelButtonColor: "#3f342e",
		confirmButtonText: "Sí, eliminar",
		cancelButtonText: "Cancelar"
	}).then(function(result){
		if (result.isConfirmed) {
			var _idMotivo = result.value;

			$.ajax({
				url: "ajax/mantenimiento-eliminar.ajax.php",
				method: "POST",
				data: { idMantenimiento: _idMantenimiento, idMotivo: _idMotivo },
				dataType: "json",
				success: function(respuesta){
					if (respuesta && respuesta.status === "success") {
						Swal.fire({
							icon: "success",
							title: "Sistema PosDit",
							text: respuesta.message || "Incidencia eliminada correctamente",
							confirmButtonText: "Cerrar"
						}).then(function(){
							window.location.reload();
						});
					} else {
						Swal.fire({
							icon: "error",
							title: "Sistema PosDit",
							text: (respuesta && respuesta.message) || "No se pudo eliminar la incidencia",
							confirmButtonText: "Cerrar"
						});
					}
				},
				error: function(){
					Swal.fire({
						icon: "error",
						title: "Sistema PosDit",
						text: "Error al eliminar la incidencia",
						confirmButtonText: "Cerrar"
					});
				}
			});
		}
	});
});
