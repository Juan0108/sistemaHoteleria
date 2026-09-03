function escaparHtmlMtto(valor){
	valor = valor == null ? "" : String(valor);
	return valor
		.replace(/&/g, "&amp;")
		.replace(/</g, "&lt;")
		.replace(/>/g, "&gt;")
		.replace(/"/g, "&quot;")
		.replace(/'/g, "&#39;");
}

// Celda de texto largo: se ve truncada en una sola línea (para no deformar la tabla) y el
// texto completo aparece como tooltip nativo (title) al pasar el mouse encima.
function celdaTruncadaMtto(texto){
	texto = texto == null ? "" : String(texto);
	if (texto.trim() === ""){
		return '<span class="mtto-bitacora-vacio">—</span>';
	}
	return '<span class="mtto-celda-truncada" title="' + escaparHtmlMtto(texto) + '">' + escaparHtmlMtto(texto) + '</span>';
}

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

			// El flujo completo de ESTA incidencia (agregada -> pendiente -> proceso ->
			// resuelto -> reabierta, etc.), nunca las otras incidencias de la habitación.
			var lista = respuesta.data;

			if (lista.length === 0) {
				$("#mttoBitacoraIncidenciasContenido").html('<p class="mtto-bitacora-vacio">Esta incidencia no tiene historial registrado.</p>');
				return;
			}

			// El scroll solo se activa con más de 5 registros (con menos, forzar
			// overflow:auto de todos modos puede pintar una scrollbar fantasma por
			// redondeo de subpíxeles del navegador).
			var _claseScrollIncidencias = lista.length > 5 ? " mtto-tabla-wrap-incidencias" : "";

			var html = '<div class="mtto-tabla-wrap' + _claseScrollIncidencias + '"><table class="mtto-bitacora-tabla"><thead><tr>' +
				'<th>Fecha</th><th>Descripción</th><th>Proveedor</th><th>Estatus</th><th>Foto</th>' +
				'</tr></thead><tbody>';

			lista.forEach(function(i){
				html += '<tr>' +
					'<td>' + escaparHtmlMtto(i.fecha) + '</td>' +
					'<td>' + celdaTruncadaMtto(i.descripcion) + '</td>' +
					'<td>' + (i.proveedor ? escaparHtmlMtto(i.proveedor) : '<span class="mtto-bitacora-vacio">Sin especificar</span>') + '</td>' +
					'<td><span class="mtto-bitacora-estatus ' + claseEstatusBitacoraMtto(i.estatus) + '">' + escaparHtmlMtto(i.estatus) + '</span></td>' +
					'<td>' + (i.foto ? ('<button type="button" class="mtto-bitacora-ver-foto btnVerFotoBitacora" data-foto="' + escaparHtmlMtto(i.foto) + '" data-titulo="Foto de esta acción">Ver foto</button>') : '<span class="mtto-bitacora-vacio">Sin foto</span>') + '</td>' +
					'</tr>';
			});

			html += '</tbody></table></div>';

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
// Devuelve el HTML de la tabla de abonos, o null si la lista viene vacía (para que
// cada llamador decida su propio mensaje de "sin abonos").
function mttoConstruirTablaAbonosHtml(lista){

	if (lista.length === 0) {
		return null;
	}

	var html = '<div class="mtto-tabla-wrap"><table class="mtto-bitacora-tabla"><thead><tr>' +
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

	html += '</tbody></table></div>';

	return html;
}

function mttoRenderizarTablaAbonos(lista, mensajeVacio){
	var html = mttoConstruirTablaAbonosHtml(lista);
	$("#mttoBitacoraAbonosContenido").html(html || ('<p class="mtto-bitacora-vacio">' + mensajeVacio + '</p>'));
}
$(document).on("click", ".btnBitacoraAbonos", function(){

	var _idMantenimiento = $(this).attr("idMantenimiento");
	var _estatusTarjeta = $(this).attr("data-estatus");

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

			// Mismo criterio que Bitácora de Incidencias: solo abonos de incidencias con
			// el mismo estatus que la tarjeta desde la que se abrió.
			var lista = respuesta.data.filter(function(a){
				return a.estatus === _estatusTarjeta;
			});

			mttoRenderizarTablaAbonos(lista, "Esta habitación no tiene abonos con este mismo estatus.");
		},
		error: function(){
			$("#mttoBitacoraAbonosContenido").html('<p class="mtto-bitacora-vacio">No se pudo cargar la bitácora.</p>');
		}
	});
});

// Botón "Ver abonos" de un renglón de la pestaña Bitácora: aquí ya se sabe exactamente
// qué incidencia es, así que se filtra por ese ticket puntual (no por estatus).
$(document).on("click", ".btnVerAbonosBitacora", function(){

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
				$("#mttoBitacoraAbonosContenido").html('<p class="mtto-bitacora-vacio">No se pudo cargar los abonos.</p>');
				return;
			}

			var lista = respuesta.data.filter(function(a){
				return String(a.idMantenimiento) === String(_idMantenimiento);
			});

			mttoRenderizarTablaAbonos(lista, "Esta incidencia no tiene abonos registrados.");
		},
		error: function(){
			$("#mttoBitacoraAbonosContenido").html('<p class="mtto-bitacora-vacio">No se pudo cargar los abonos.</p>');
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
 Calendario propio (bootstrap-datepicker) para "Nueva incidencia": no se puede escribir la
 fecha a mano (el input queda "readonly"), y la fecha fin no puede ser anterior a la fecha
 inicio elegida.
 =============================================*/
$(function(){
	var _hoyIso = new Date().toISOString().slice(0, 10);

	$("input[name='nuevaFechaInicioMtto']").datepicker({
		format: "yyyy-mm-dd",
		language: "es",
		autoclose: true,
		todayHighlight: true,
		startDate: _hoyIso
	});
	$("input[name='nuevaFechaFinMtto']").datepicker({
		format: "yyyy-mm-dd",
		language: "es",
		autoclose: true,
		todayHighlight: true,
		startDate: _hoyIso
	});
});

$(document).on("changeDate", "input[name='nuevaFechaInicioMtto']", function(){
	$("input[name='nuevaFechaFinMtto']").datepicker("setStartDate", $(this).val());
});

/*=============================================
 Cambiar estatus (Iniciar / Marcar resuelto / Reabrir)
 =============================================*/
// Igual que el formateo de montos usado en Recepción: agrega comas de miles mientras se
// escribe, sin dejar de aceptar el punto decimal.
function formatearMontoMtto(valor){
	valor = String(valor).replace(/[^\d.]/g, "");

	var _puntoIndice = valor.indexOf(".");
	if (_puntoIndice !== -1) {
		valor = valor.slice(0, _puntoIndice + 1) + valor.slice(_puntoIndice + 1).replace(/\./g, "");
	}

	var _partes = valor.split(".");
	_partes[0] = _partes[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
	if (_partes.length > 1) {
		_partes[1] = _partes[1].slice(0, 2);
	}

	return _partes.join(".");
}

function enviarCambioEstatusMtto(idMantenimiento, idEstatus, notaReapertura, fotoResuelto, fotoReapertura, presupuestoReapertura, fotoProceso){
	var _datosEnvio;
	var _llevaArchivo = !!(fotoResuelto || fotoReapertura || fotoProceso);

	if (_llevaArchivo) {
		_datosEnvio = new FormData();
		_datosEnvio.append("idMantenimiento", idMantenimiento);
		_datosEnvio.append("idEstatus", idEstatus);
		_datosEnvio.append("notaReapertura", notaReapertura || "");
		if (fotoResuelto) _datosEnvio.append("fotoResuelto", fotoResuelto);
		if (fotoReapertura) _datosEnvio.append("fotoReapertura", fotoReapertura);
		if (fotoProceso) _datosEnvio.append("fotoProceso", fotoProceso);
		if (presupuestoReapertura) {
			_datosEnvio.append("costoReapertura", presupuestoReapertura.costo);
			_datosEnvio.append("fechaInicioReapertura", presupuestoReapertura.fechaInicio);
			_datosEnvio.append("fechaFinReapertura", presupuestoReapertura.fechaFin);
		}
	} else {
		_datosEnvio = { idMantenimiento: idMantenimiento, idEstatus: idEstatus, notaReapertura: notaReapertura || "" };
	}

	$.ajax({
		url: "ajax/mantenimiento-cambiar-estatus.ajax.php",
		method: "POST",
		data: _datosEnvio,
		processData: !_llevaArchivo,
		contentType: _llevaArchivo ? false : "application/x-www-form-urlencoded; charset=UTF-8",
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
		// Reabrir exige motivo Y una foto nueva (evidencia de por qué se reabre); esa foto
		// se vuelve la foto activa de la incidencia, así que ya no tiene caso mostrar la
		// foto de resultado vieja hasta que se vuelva a marcar Resuelto.
		// El presupuesto (costo + fechas estimadas) del intento anterior ya no aplica a esta
		// reparación nueva, así que se piden de nuevo aquí en vez de dejarlos con el dato
		// viejo; las fechas no pueden ser anteriores a hoy.
		var _hoyIso = new Date().toISOString().slice(0, 10);

		Swal.fire({
			title: "Sistema PosDit",
			html:
				'<div style="text-align:left;">' +
					'<label style="font-weight:600; display:block; margin-bottom:6px;">¿Por qué se vuelve a reabrir esta incidencia?</label>' +
					'<textarea id="mttoReaperturaMotivo" class="swal2-textarea" placeholder="Describe el motivo de la reapertura" style="display:block; width:100%; box-sizing:border-box; resize:none; overflow-y:auto; min-height:60px; max-height:160px; margin:0 auto 16px;"></textarea>' +
					'<label style="font-weight:600; display:block; margin-bottom:6px;">Foto nueva de la incidencia (obligatoria)</label>' +
					'<input type="file" id="mttoReaperturaFoto" accept="image/*" class="swal2-file" style="margin:0 0 16px;">' +
					'<label style="font-weight:600; display:block; margin-bottom:6px;">Costo estimado de esta reparación</label>' +
					'<input type="text" inputmode="decimal" id="mttoReaperturaCosto" class="swal2-input" placeholder="0.00" style="margin:0 0 16px;">' +
					'<div style="display:flex; gap:10px;">' +
						'<div style="flex:1; min-width:0;">' +
							'<label style="font-weight:600; display:block; margin-bottom:6px;">Inicio estimado</label>' +
							'<input type="text" id="mttoReaperturaFechaInicio" class="swal2-input" style="margin:0; width:100%; box-sizing:border-box;" autocomplete="off" readonly>' +
						'</div>' +
						'<div style="flex:1; min-width:0;">' +
							'<label style="font-weight:600; display:block; margin-bottom:6px;">Fin estimado</label>' +
							'<input type="text" id="mttoReaperturaFechaFin" class="swal2-input" style="margin:0; width:100%; box-sizing:border-box;" autocomplete="off" readonly>' +
						'</div>' +
					'</div>' +
				'</div>',
			icon: "question",
			showCancelButton: true,
			confirmButtonColor: "#81412d",
			cancelButtonColor: "#3f342e",
			confirmButtonText: "Reabrir",
			cancelButtonText: "Cancelar",
			didOpen: function(){
				// Formatea el costo con comas de miles mientras se escribe (igual que en el
				// resto del sistema), y evita que "Fin estimado" quede antes de "Inicio".
				document.getElementById("mttoReaperturaCosto").addEventListener("input", function(){
					this.value = formatearMontoMtto(this.value);
				});
				// Calendario propio (bootstrap-datepicker) en vez del selector nativo: solo se
				// puede escoger la fecha, no escribirla a mano.
				$("#mttoReaperturaFechaInicio, #mttoReaperturaFechaFin").datepicker({
					format: "yyyy-mm-dd",
					language: "es",
					autoclose: true,
					todayHighlight: true,
					startDate: _hoyIso
				});
				$("#mttoReaperturaFechaInicio").on("changeDate", function(){
					$("#mttoReaperturaFechaFin").datepicker("setStartDate", this.value || _hoyIso);
				});

				// El textarea crece con el contenido (hasta max-height, definido en su propio
				// style) en vez de quedar fijo con su propia scrollbar interna desde el inicio;
				// pasado ese máximo, el overflow-y:auto ya puesto en el style se encarga de la
				// scrollbar.
				var _motivoTextarea = document.getElementById("mttoReaperturaMotivo");
				var _autoAjustarAltura = function(){
					_motivoTextarea.style.height = "auto";
					_motivoTextarea.style.height = _motivoTextarea.scrollHeight + "px";
				};
				_motivoTextarea.addEventListener("input", _autoAjustarAltura);
				_autoAjustarAltura();
			},
			preConfirm: function(){
				var _motivo = document.getElementById("mttoReaperturaMotivo").value.trim();
				var _archivo = document.getElementById("mttoReaperturaFoto").files[0];
				var _costo = desformatearPrecio(document.getElementById("mttoReaperturaCosto").value) || 0;
				var _fechaInicio = document.getElementById("mttoReaperturaFechaInicio").value;
				var _fechaFin = document.getElementById("mttoReaperturaFechaFin").value;

				if (!_motivo) {
					Swal.showValidationMessage("Debes escribir el motivo de la reapertura");
					return false;
				}
				if (!_archivo) {
					Swal.showValidationMessage("Debes adjuntar una foto para reabrir la incidencia");
					return false;
				}
				if (_archivo.size > 3 * 1024 * 1024) {
					Swal.showValidationMessage("La foto no puede pesar más de 3MB");
					return false;
				}
				if (!_fechaInicio || !_fechaFin) {
					Swal.showValidationMessage("Captura las fechas estimadas de la reparación");
					return false;
				}
				if (_fechaInicio < _hoyIso || _fechaFin < _hoyIso) {
					Swal.showValidationMessage("Las fechas estimadas no pueden ser anteriores a hoy");
					return false;
				}
				if (_fechaFin < _fechaInicio) {
					Swal.showValidationMessage("La fecha de fin estimada no puede ser anterior a la de inicio");
					return false;
				}

				return {
					motivo: _motivo,
					foto: _archivo,
					presupuesto: { costo: _costo, fechaInicio: _fechaInicio, fechaFin: _fechaFin }
				};
			}
		}).then(function(result){
			if (result.isConfirmed) {
				enviarCambioEstatusMtto(_idMantenimiento, _idEstatus, result.value.motivo, null, result.value.foto, result.value.presupuesto);
			}
		});
		return;
	}

	if (typeof MTTO_ESTATUS_PROCESO !== "undefined" && String(_idEstatus) === String(MTTO_ESTATUS_PROCESO)) {
		// "Iniciar" (Pendiente -> Proceso) también exige foto: evidencia de que se empezó a
		// trabajar, para que quede en la bitácora/historial junto con ese cambio de estado.
		Swal.fire({
			title: "Sistema PosDit",
			text: "Sube una foto de evidencia de que se está iniciando la reparación",
			icon: "question",
			input: "file",
			inputAttributes: {
				accept: "image/*"
			},
			inputValidator: function(value){
				if (!value) {
					return "Debes adjuntar la foto para iniciar la reparación";
				}
				if (value.size > 3 * 1024 * 1024) {
					return "La foto no puede pesar más de 3MB";
				}
			},
			showCancelButton: true,
			confirmButtonColor: "#3f7649",
			cancelButtonColor: "#3f342e",
			confirmButtonText: "Iniciar",
			cancelButtonText: "Cancelar"
		}).then(function(result){
			if (result.isConfirmed) {
				enviarCambioEstatusMtto(_idMantenimiento, _idEstatus, null, null, null, null, result.value);
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

// Compara solo la parte de fecha (YYYY-MM-DD) de un valor ISO contra el rango de los
// inputs "Desde"/"Hasta" (vacíos = sin límite de ese lado).
function mttoEnRangoFecha(fechaIso, $desde, $hasta){
	if (!fechaIso) return true;

	var _dia = String(fechaIso).substring(0, 10);
	var _desde = $desde.val();
	var _hasta = $hasta.val();

	if (_desde && _dia < _desde) return false;
	if (_hasta && _dia > _hasta) return false;

	return true;
}


/*=============================================
 Pop up "Ver info": datos capturados al registrar la incidencia (tab Bitácora)
 =============================================*/
$(document).on("click", ".btnVerInfoIncidenciaMtto", function(){

	var _idMantenimiento = $(this).attr("idMantenimiento");

	$("#mttoInfoRegistroContenido").html('<p class="text-muted text-center" style="padding:20px;">Cargando…</p>');
	$("#modalInfoRegistroMtto").modal("show");

	$.ajax({
		url: "ajax/mantenimiento-info-registro.ajax.php",
		method: "GET",
		data: { idMantenimiento: _idMantenimiento },
		dataType: "json",
		success: function(respuesta){

			if (!respuesta || respuesta.status !== "success"){
				$("#mttoInfoRegistroContenido").html('<p class="mtto-bitacora-vacio text-center">No se pudo cargar la información.</p>');
				return;
			}

			var d = respuesta.data;
			var _vacio = '<span class="mtto-bitacora-vacio">—</span>';

			$("#modalInfoRegistroMtto .modal-title").html('<i class="fa fa-list-alt"></i> Información de la incidencia — ' + escaparHtmlMtto(d.habitacion));

			var _titulos = [
				"Evento", "Tipo de mantenimiento", "Pieza / parte afectada", "Proveedor",
				"Descripción", "Estatus", "Registró", "Fecha de registro",
				"Fecha inicio estimada", "Fecha fin estimada", "Fecha resuelto",
				"Costo estimado", "Foto", "Motivo de reapertura"
			];

			var _filaRegistro = [
				'<span class="mtto-evento-badge">Registro inicial</span>',
				escaparHtmlMtto(d.tipoMantenimiento),
				escaparHtmlMtto(d.pieza),
				d.proveedor ? escaparHtmlMtto(d.proveedor) : '<span class="mtto-bitacora-vacio">Sin especificar</span>',
				celdaTruncadaMtto(d.descripcion),
				escaparHtmlMtto(d.estatus),
				escaparHtmlMtto(d.usuario),
				escaparHtmlMtto(d.fechaRegistro),
				d.fechaInicioEstimado ? escaparHtmlMtto(d.fechaInicioEstimado) : _vacio,
				d.fechaFinEstimado ? escaparHtmlMtto(d.fechaFinEstimado) : _vacio,
				d.fechaResuelto ? escaparHtmlMtto(d.fechaResuelto) : _vacio,
				"$" + Number(d.costo).toFixed(2),
				d.foto ? ('<button type="button" class="mtto-bitacora-ver-foto btnVerFotoBitacora" data-foto="' + escaparHtmlMtto(d.foto) + '" data-titulo="Foto de la incidencia">Ver foto</button>') : '<span class="mtto-bitacora-vacio">Sin foto</span>',
				_vacio
			];

			var _filas = [_filaRegistro];

			if (d.vecesReabierta > 0){
				_filas.push([
					'<span class="mtto-reabierta-badge"><i class="fa fa-undo"></i> Reapertura' + (d.vecesReabierta > 1 ? " (x" + d.vecesReabierta + ")" : "") + '</span>',
					_vacio, _vacio, _vacio, _vacio,
					escaparHtmlMtto(d.estatus),
					_vacio, _vacio, _vacio, _vacio,
					d.fechaResuelto ? escaparHtmlMtto(d.fechaResuelto) : _vacio,
					_vacio, _vacio,
					d.notaReapertura ? celdaTruncadaMtto(d.notaReapertura) : '<span class="mtto-bitacora-vacio">Sin especificar</span>'
				]);
			}

			// El scroll solo se activa con más de 4 filas (con menos, forzar
			// overflow:auto de todos modos puede pintar una scrollbar fantasma por
			// redondeo de subpíxeles del navegador).
			var _claseScrollInfo = _filas.length > 4 ? " mtto-tabla-wrap-info" : "";

			var html = '<div class="mtto-tabla-wrap' + _claseScrollInfo + '"><table class="mtto-bitacora-tabla">' +
				'<thead><tr>' +
					_titulos.map(function(t){ return '<th>' + t + '</th>'; }).join("") +
				'</tr></thead>' +
				'<tbody>' +
					_filas.map(function(fila){
						return '<tr>' + fila.map(function(v){ return '<td>' + v + '</td>'; }).join("") + '</tr>';
					}).join("") +
				'</tbody></table></div>';

			$("#mttoInfoRegistroContenido").html(html);
		},
		error: function(){
			$("#mttoInfoRegistroContenido").html('<p class="mtto-bitacora-vacio text-center">No se pudo cargar la información.</p>');
		}
	});
});

/*=============================================
 Tab Historial: grid de Habitación > Incidencia con filas expandibles. Al expandir una
 incidencia se carga (una sola vez) su flujo completo de estatus y sus abonos.
 =============================================*/
var mttoHistorialListaActual = [];
var mttoHistorialIdHabitacionActual = "";

// Agrupa la lista plana de incidencias por habitación, preservando el orden de llegada.
// Agrupa por Id_Habitacion (no por el nombre/tipo): dos habitaciones distintas pueden
// compartir el mismo TipoHabitacion (p.ej. dos cuartos "Individual") y no deben mezclarse.
function mttoAgruparPorHabitacionMtto(lista){
	var grupos = [];
	var indice = {};

	lista.forEach(function(i){
		var clave = (i.idHabitacion != null) ? i.idHabitacion : "__actual__";
		if (!(clave in indice)){
			indice[clave] = { idHabitacion: i.idHabitacion, nombre: i.habitacion || null, incidencias: [] };
			grupos.push(indice[clave]);
		}
		indice[clave].incidencias.push(i);
	});

	return grupos;
}

// Tabla de flujo de UNA incidencia (columnas del boceto: Fecha, Descripción, Proveedor,
// Estatus, Motivo de eliminación, Foto incidencia, Foto resultado, Acciones).
function mttoConstruirFlujoIncidenciaHtml(lista){

	if (lista.length === 0) {
		return null;
	}

	var html = '<div class="mtto-tabla-wrap"><table class="mtto-bitacora-tabla"><thead><tr>' +
		'<th>Fecha</th><th>Descripción</th><th>Proveedor</th><th>Estatus</th><th>Restauradas</th><th>Motivo de eliminación</th><th>Foto</th>' +
		'</tr></thead><tbody>';

	lista.forEach(function(i){
		// La foto es la propia de ESTE renglón/evento (guardada en el historial al
		// registrar, reabrir, resolver o restaurar) — ya no una columna fija de la
		// incidencia, así que cada transición muestra exactamente su propia evidencia.
		html += '<tr>' +
			'<td>' + escaparHtmlMtto(i.fecha) + '</td>' +
			'<td>' + celdaTruncadaMtto(i.descripcion) + '</td>' +
			'<td>' + (i.proveedor ? escaparHtmlMtto(i.proveedor) : '<span class="mtto-bitacora-vacio">Sin especificar</span>') + '</td>' +
			'<td><span class="mtto-bitacora-estatus ' + claseEstatusBitacoraMtto(i.estatus) + '">' + escaparHtmlMtto(i.estatus) + '</span></td>' +
			'<td>' + (i.nota ? celdaTruncadaMtto(i.nota) : '<span class="mtto-bitacora-vacio">—</span>') + '</td>' +
			'<td>' + (i.motivoEliminado ? escaparHtmlMtto(i.motivoEliminado) : '<span class="mtto-bitacora-vacio">—</span>') + '</td>' +
			'<td>' + (i.foto ? ('<button type="button" class="mtto-bitacora-ver-foto btnVerFotoBitacora" data-foto="' + escaparHtmlMtto(i.foto) + '" data-titulo="Foto de esta acción">Ver foto</button>') : '<span class="mtto-bitacora-vacio">Sin foto</span>') + '</td>' +
			'</tr>';
	});

	html += '</tbody></table></div>';

	return html;
}

function mttoRenderizarHistorial(){

	var $contenido = $("#mttoHistorialTabContenido");
	var $desde = $("#mttoHistorialDesde");
	var $hasta = $("#mttoHistorialHasta");

	var lista = mttoHistorialListaActual.filter(function(i){
		return mttoEnRangoFecha(i.fechaRegistroIso, $desde, $hasta);
	});

	// Solo se muestran habitaciones que SÍ tienen al menos una incidencia — antes, sin
	// filtros tocados, se rellenaba con todas las habitaciones existentes aunque no
	// tuvieran ninguna; ya no.
	if (lista.length === 0){
		var _vacioBase = mttoHistorialIdHabitacionActual ? "Esta habitación no tiene incidencias registradas." : "Todavía no hay incidencias registradas.";
		$contenido.html('<p class="mtto-grid-vacio">' + (mttoHistorialListaActual.length === 0 ? _vacioBase : "Ninguna incidencia cae en ese rango de fechas.") + '</p>');
		return;
	}

	// Cuando ya se filtró por una habitación específica, el nombre no viene en cada fila
	// (ya está implícito), así que se toma del texto seleccionado en el combo.
	var _nombreHabitacionFiltro = mttoHistorialIdHabitacionActual ? $("#mttoHistorialHabitacion option:selected").text() : null;
	var grupos = mttoAgruparPorHabitacionMtto(lista);

	if (grupos.length === 0){
		$contenido.html('<p class="mtto-grid-vacio">No hay habitaciones registradas.</p>');
		return;
	}

	var html = '';
	var _numGrupo = 0;

	grupos.forEach(function(grupo){
		_numGrupo++;
		var _nombreGrupo = grupo.nombre || _nombreHabitacionFiltro || "Habitación";
		var _idGrupo = "mttoGridHab" + _numGrupo;
		var _totalIncidencias = grupo.incidencias.length;

		html += '<div class="mtto-grid-habitacion">' +
			'<div class="mtto-grid-header mtto-grid-header-habitacion" data-toggle="collapse" data-target="#' + _idGrupo + '" aria-expanded="false">' +
				'<span class="mtto-grid-chevron"><i class="fa fa-chevron-right"></i></span>' +
				'<span class="mtto-grid-titulo">Habitación &rarr; ' + escaparHtmlMtto(_nombreGrupo) + '</span>' +
				'<span class="mtto-grid-fecha">' + (_totalIncidencias === 0 ? 'Sin incidencias' : (_totalIncidencias + (_totalIncidencias === 1 ? ' incidencia' : ' incidencias'))) + '</span>' +
			'</div>' +
			'<div class="collapse" id="' + _idGrupo + '">' +
				'<div class="mtto-grid-incidencias">';

		if (_totalIncidencias === 0){
			html += '<p class="mtto-grid-vacio">Esta habitación no tiene incidencias registradas.</p>';
		}

		grupo.incidencias.forEach(function(inc){
			var _idIncidencia = _idGrupo + "_" + inc.idMantenimiento;

			html += '<div class="mtto-grid-incidencia">' +
				'<div class="mtto-grid-header mtto-grid-header-incidencia" data-toggle="collapse" data-target="#' + _idIncidencia + '" aria-expanded="false" data-id-mantenimiento="' + inc.idMantenimiento + '" data-estatus="' + escaparHtmlMtto(inc.estatus) + '" data-cargado="0">' +
					'<span class="mtto-grid-chevron"><i class="fa fa-chevron-right"></i></span>' +
					'<span class="mtto-grid-titulo">Incidencia &rarr; ' + escaparHtmlMtto(inc.descripcion) + '</span>' +
					'<span class="mtto-bitacora-estatus ' + claseEstatusBitacoraMtto(inc.estatus) + '">' + escaparHtmlMtto(inc.estatus) + '</span>' +
					'<span class="mtto-grid-fecha">' + escaparHtmlMtto(inc.fechaRegistro) + '</span>' +
				'</div>' +
				'<div class="collapse" id="' + _idIncidencia + '">' +
					'<div class="mtto-grid-detalle">' +
						'<p class="text-muted text-center" style="padding:10px;">Cargando…</p>' +
					'</div>' +
				'</div>' +
			'</div>';
		});

		html += '</div></div></div>';
	});

	$contenido.html(html);
}

// Al expandir por primera vez una incidencia, carga su flujo de estatus y sus abonos
// (una sola vez; ya cargado se queda en el DOM aunque se vuelva a colapsar/expandir).
$(document).on("click", ".mtto-grid-header-incidencia", function(){

	var $header = $(this);

	if ($header.attr("data-cargado") === "1"){
		return;
	}

	var _idMantenimiento = $header.data("idMantenimiento");
	var _estatusActual = $header.attr("data-estatus");
	var $detalle = $($header.attr("data-target")).find(".mtto-grid-detalle");

	$header.attr("data-cargado", "1");

	$.ajax({
		url: "ajax/mantenimiento-bitacora-incidencias.ajax.php",
		method: "GET",
		data: { idMantenimiento: _idMantenimiento },
		dataType: "json"
	}).then(function(respuestaFlujo){

		var _htmlFlujo = (respuestaFlujo && respuestaFlujo.status === "success")
			? (mttoConstruirFlujoIncidenciaHtml(respuestaFlujo.data) || '<p class="mtto-grid-vacio">Esta incidencia no tiene historial registrado.</p>')
			: '<p class="mtto-grid-vacio">No se pudo cargar el flujo de esta incidencia.</p>';

		return $.ajax({
			url: "ajax/mantenimiento-bitacora-abonos.ajax.php",
			method: "GET",
			data: { idMantenimiento: _idMantenimiento },
			dataType: "json"
		}).then(function(respuestaAbonos){

			var _htmlAbonos;

			if (respuestaAbonos && respuestaAbonos.status === "success"){
				var _abonos = respuestaAbonos.data.filter(function(a){
					return String(a.idMantenimiento) === String(_idMantenimiento);
				});
				_htmlAbonos = mttoConstruirTablaAbonosHtml(_abonos) || '<p class="mtto-grid-vacio">Esta incidencia no tiene abonos registrados.</p>';
			}else{
				_htmlAbonos = '<p class="mtto-grid-vacio">No se pudieron cargar los abonos.</p>';
			}

			// El botón "Restaurar" se decide por el estatus ACTUAL de la incidencia (el de
			// la tarjeta/encabezado), nunca por una fila vieja del flujo: una incidencia que
			// ya avanzó de vuelta a Pendiente/Proceso/Resuelto no está eliminada ahora mismo,
			// aunque su historial todavía muestre un renglón "Eliminado" de cuando lo estuvo.
			var _htmlRestaurar = (_estatusActual === "Eliminado")
				? '<div class="mtto-grid-restaurar"><button type="button" class="mtto-btn-restaurar btnRestaurarMtto" idMantenimiento="' + _idMantenimiento + '"><i class="fa fa-undo"></i> Restaurar incidencia</button></div>'
				: '';

			$detalle.html(
				_htmlRestaurar +
				'<div class="mtto-grid-detalle-seccion">Flujo de la incidencia</div>' + _htmlFlujo +
				'<div class="mtto-grid-detalle-seccion">Abonos</div>' + _htmlAbonos
			);
		});
	}).catch(function(){
		$detalle.html('<p class="mtto-grid-vacio">No se pudo cargar la información de esta incidencia.</p>');
		$header.attr("data-cargado", "0");
	});
});

function mttoCargarHistorial(idHabitacion){

	var $contenido = $("#mttoHistorialTabContenido");

	mttoHistorialIdHabitacionActual = idHabitacion || "";

	$contenido.html('<p class="text-muted text-center" style="padding:20px;">Cargando…</p>');

	$.ajax({
		url: "ajax/mantenimiento-historial-incidencias.ajax.php",
		method: "GET",
		data: idHabitacion ? { idHabitacion: idHabitacion } : {},
		dataType: "json",
		success: function(respuesta){

			if (!respuesta || respuesta.status !== "success"){
				$contenido.html('<p class="mtto-bitacora-vacio">No se pudo cargar el historial.</p>');
				return;
			}

			mttoHistorialListaActual = respuesta.data;
			mttoRenderizarHistorial();
		},
		error: function(){
			$contenido.html('<p class="mtto-bitacora-vacio">No se pudo cargar el historial.</p>');
		}
	});
}

$(document).on("change", "#mttoHistorialHabitacion", function(){
	mttoCargarHistorial($(this).val());
});

$(document).on("change", "#mttoHistorialDesde, #mttoHistorialHasta", function(){
	if (mttoHistorialListaActual.length){
		mttoRenderizarHistorial();
	}
});

$(document).on("click", "#mttoHistorialLimpiarFecha", function(){
	$("#mttoHistorialDesde, #mttoHistorialHasta").datepicker("clearDates");
	if (mttoHistorialListaActual.length){
		mttoRenderizarHistorial();
	}
});

// Mientras no se toque el filtro de habitación, se ve el historial de todo el hotel junto.
$(document).ready(function(){
	if ($("#mttoHistorialHabitacion").length){
		// Calendario propio (bootstrap-datepicker) para los filtros del historial: solo se
		// puede escoger la fecha, no escribirla a mano (el input queda "readonly").
		$("#mttoHistorialDesde, #mttoHistorialHasta").datepicker({
			format: "yyyy-mm-dd",
			language: "es",
			autoclose: true,
			todayHighlight: true
		});

		mttoCargarHistorial("");
	}
});

/*=============================================
 Restaurar una incidencia eliminada por error (tab Historial)
 =============================================*/
$(document).on("click", ".btnRestaurarMtto", function(){

	var _idMantenimiento = $(this).attr("idMantenimiento");

	Swal.fire({
		title: "Restaurar incidencia",
		text: "Volverá a su último estatus antes de eliminarse. Sube una foto como evidencia para poder restaurarla.",
		icon: "question",
		input: "file",
		inputAttributes: { "accept": "image/*", "aria-label": "Foto de evidencia" },
		showCancelButton: true,
		confirmButtonColor: "#4c8c5a",
		cancelButtonColor: "#3f342e",
		confirmButtonText: "Restaurar",
		cancelButtonText: "Cancelar",
		preConfirm: function(archivo){
			if (!archivo){
				Swal.showValidationMessage("Debes subir una foto para restaurar la incidencia.");
			}
			return archivo;
		}
	}).then(function(result){
		if (!result.isConfirmed || !result.value) {
			return;
		}

		var datos = new FormData();
		datos.append("idMantenimiento", _idMantenimiento);
		datos.append("foto", result.value);

		$.ajax({
			url: "ajax/mantenimiento-restaurar.ajax.php",
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
						text: respuesta.message || "Incidencia restaurada correctamente",
						confirmButtonText: "Cerrar"
					}).then(function(){
						window.location.reload();
					});
				} else {
					Swal.fire({
						icon: "error",
						title: "Sistema PosDit",
						text: (respuesta && respuesta.message) || "No se pudo restaurar la incidencia",
						confirmButtonText: "Cerrar"
					});
				}
			},
			error: function(){
				Swal.fire({
					icon: "error",
					title: "Sistema PosDit",
					text: "Error al restaurar la incidencia",
					confirmButtonText: "Cerrar"
				});
			}
		});
	});
});

/*=============================================
 Corte diario por WhatsApp (solo Administrador): manda el reporte del día directo al
 teléfono guardado en la sesión, sin pedirlo (a diferencia del ticket de checkout).
 =============================================*/
$(document).on("click", "#mttoBtnReporteCorte", function(){

	var _fechaDesde = $("#mttoHistorialDesde").val();
	var _fechaHasta = $("#mttoHistorialHasta").val();

	if (!_fechaDesde || !_fechaHasta){
		Swal.fire({
			icon: "warning",
			title: "Selecciona un rango de fechas",
			text: "Para generar el reporte primero elige \"Desde\" y \"Hasta\" en los filtros del historial."
		});
		return;
	}

	Swal.fire({
		title: "¿Generar el corte diario?",
		text: "Se mandará por WhatsApp al teléfono registrado en tu cuenta.",
		icon: "question",
		showCancelButton: true,
		confirmButtonText: "Sí, generar",
		cancelButtonText: "Cancelar",
		confirmButtonColor: "#4c8c5a",
		cancelButtonColor: "#3f342e",
		showLoaderOnConfirm: true,
		preConfirm: function(){
			// Mismos filtros que ya están en pantalla en el tab Historial (habitación,
			// Desde, Hasta): si no se tocaron, se mandan vacíos y el reporte se comporta
			// igual que antes (corte de hoy).
			return $.ajax({
				url: "extensions/tcpdf/Reportes/ReporteCorteMantenimiento.php",
				method: "GET",
				dataType: "json",
				data: {
					idHabitacion: $("#mttoHistorialHabitacion").val() || "",
					fechaDesde: $("#mttoHistorialDesde").val() || "",
					fechaHasta: $("#mttoHistorialHasta").val() || ""
				}
			}).catch(function(){
				Swal.showValidationMessage("No se pudo contactar al servidor, intenta de nuevo");
				return Promise.reject();
			});
		},
		allowOutsideClick: function(){ return !Swal.isLoading(); }
	}).then(function(resultado){
		if (!resultado.isConfirmed){
			return;
		}

		var _respuesta = resultado.value;

		if (_respuesta && _respuesta.ok){
			Swal.fire({ icon: "success", title: "Reporte enviado", timer: 1800, showConfirmButton: false });
		}else{
			Swal.fire({
				icon: "error",
				title: "No se pudo enviar el reporte",
				text: (_respuesta && _respuesta.mensaje) || "La API de WhatsApp no confirmó el envío."
			});
		}
	});
});
