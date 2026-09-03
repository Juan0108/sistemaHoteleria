// El spinner de carga ahora es global (ver plantilla.php), así que este overlay local ya
// no se muestra para no encimarse visualmente con el global.
function mostrarCargaServicio(){}

function ocultarCargaServicio(){}

function escaparHtmlServicio(valor){
	valor = valor == null ? "" : String(valor);
	return valor
		.replace(/&/g, "&amp;")
		.replace(/</g, "&lt;")
		.replace(/>/g, "&gt;")
		.replace(/"/g, "&quot;")
		.replace(/'/g, "&#39;");
}

function servPintarTareas($cuerpo, tareas){

	$cuerpo.empty();

	if (tareas.length === 0){
		$cuerpo.append('<tr><td class="text-center text-muted" style="padding:15px;">No hay tareas activas configuradas.</td></tr>');
		return;
	}

	tareas.forEach(function(t, indice){

		var idCheck = "servTareaToggle" + (t.idServicioTarea || t.Id_ServicioTarea || indice);
		var realizada = parseInt(t.realizada || t.Realizada, 10) === 1;
		var texto = t.tarea || t.Tarea;
		var idServicioTarea = t.idServicioTarea || t.Id_ServicioTarea;

		var toggle = '<input type="checkbox" class="servToggleTarea" id="' + idCheck + '" data-id-servicio-tarea="' + idServicioTarea + '"' +
			' data-toggle="toggle" data-size="mini" data-onstyle="success" data-offstyle="default"' + (realizada ? " checked" : "") + '>';

		$cuerpo.append(
			'<tr>' +
				'<td class="serv-tarea-num">' + (indice + 1) + '.</td>' +
				'<td>' + escaparHtmlServicio(texto) + '</td>' +
				'<td class="serv-tarea-toggle">' + toggle + '</td>' +
			'</tr>'
		);
	});

	$cuerpo.find('input[data-toggle="toggle"]').bootstrapToggle();
}

function servCargarChecklist(idServicio){

	$("#servTareasCuerpo").html('<tr><td class="text-center text-muted" style="padding:15px;">Cargando…</td></tr>');

	$.ajax({
		url: "ajax/servicio.ajax.php",
		method: "GET",
		data: { accion: "tareas", id_servicio: idServicio },
		dataType: "json",
		success: function(respuesta){
			servPintarTareas($("#servTareasCuerpo"), respuesta.data || []);
		}
	});
}

function servMostrarPasoChecklist(idHabitacion, idServicio){

	$("#servIdHabitacion").val(idHabitacion);
	$("#servIdServicio").val(idServicio);

	$("#servPasoSeleccion").hide();
	$("#servPasoInicio").hide();
	$("#servPasoChecklist").show();
	$("#servFinalizar").show();

	$("#servEvidencia").val("");
	$("#servEvidenciaPreview").hide().attr("src", "");

	servCargarChecklist(idServicio);
}

function servConsultarActivoYMostrarPaso(idHabitacion){

	mostrarCargaServicio();

	$.ajax({
		url: "ajax/servicio.ajax.php",
		method: "GET",
		data: { accion: "activo", id_habitacion: idHabitacion },
		dataType: "json",
		success: function(respuesta){

			$("#servIdHabitacion").val(idHabitacion);

			if (respuesta.data){

				servMostrarPasoChecklist(idHabitacion, respuesta.data.Id_Servicio);

			}else{

				var ahora = new Date();
				var pad = function(n){ return (n < 10 ? "0" : "") + n; };
				var fechaTexto = pad(ahora.getDate()) + "/" + pad(ahora.getMonth() + 1) + "/" + ahora.getFullYear() +
					" " + pad(ahora.getHours()) + ":" + pad(ahora.getMinutes());

				$("#servFechaInicioValor").text(fechaTexto);
				$("#servIdServicio").val("");

				$("#servPasoSeleccion").hide();
				$("#servPasoChecklist").hide();
				$("#servFinalizar").hide();
				$("#servPasoInicio").show();
			}
		},
		complete: ocultarCargaServicio
	});
}

$(document).ready(function(){

	// El modal de "Iniciar limpieza" no se renderiza para el perfil Administrador (no debe
	// ver ni usar esa acción), pero el historial y sus filtros sí deben seguir funcionando
	// para ese perfil — por eso el guard usa la tabla de historial, no el modal.
	if ($("#servHistorialCuerpo").length === 0){
		return;
	}

	$(document).on("click", ".btnAbrirRealizarTarea", function(){

		$("#servSelectHabitacion").val("");
		$("#servModalHabitacion").text("");
		$("#servIdHabitacion").val("");
		$("#servIdServicio").val("");

		$("#servPasoSeleccion").show();
		$("#servPasoInicio").hide();
		$("#servPasoChecklist").hide();
		$("#servFinalizar").hide();
		$("#servFotoInicio").val("");
		$("#servFotoInicioPreview").hide().attr("src", "");

		$("#modalRealizarTarea").modal("show");
	});

	$(document).on("change", "#servSelectHabitacion", function(){

		var idHabitacion = $(this).val();
		var nombreHabitacion = $(this).find("option:selected").text();

		if (!idHabitacion){
			return;
		}

		$("#servModalHabitacion").text(nombreHabitacion);
		servConsultarActivoYMostrarPaso(idHabitacion);
	});

	$(document).on("change", "#servFotoInicio", function(){

		var archivo = this.files && this.files[0];

		if (!archivo){
			$("#servFotoInicioPreview").hide().attr("src", "");
			return;
		}

		var lector = new FileReader();
		lector.onload = function(e){
			$("#servFotoInicioPreview").attr("src", e.target.result).show();
		};
		lector.readAsDataURL(archivo);
	});

	$(document).on("click", "#servComenzar", function(){

		var idHabitacion = $("#servIdHabitacion").val();
		var archivo = $("#servFotoInicio")[0].files[0];

		if (!archivo){
			Swal.fire({ icon: "warning", title: "Falta la foto inicial", text: "Sube una foto antes de comenzar la limpieza." });
			return;
		}

		var datos = new FormData();
		datos.append("accion", "iniciar");
		datos.append("id_habitacion", idHabitacion);
		datos.append("fotoInicio", archivo);

		mostrarCargaServicio();

		$.ajax({
			url: "ajax/servicio.ajax.php",
			method: "POST",
			data: datos,
			cache: false,
			contentType: false,
			processData: false,
			dataType: "json",
			success: function(respuesta){
				if (respuesta.ok){
					servMostrarPasoChecklist(idHabitacion, respuesta.idServicio);
				}else{
					Swal.fire({ icon: "error", title: "No se pudo iniciar", text: respuesta.mensaje });
				}
			},
			error: function(){
				Swal.fire({ icon: "error", title: "Error", text: "No se pudo iniciar el servicio. Intenta de nuevo." });
			},
			complete: ocultarCargaServicio
		});
	});

	$(document).on("change", ".servToggleTarea", function(){

		var idServicioTarea = $(this).data("idServicioTarea");
		var realizada = $(this).prop("checked") ? 1 : 0;

		$.ajax({
			url: "ajax/servicio.ajax.php",
			method: "POST",
			data: { accion: "cambiarEstatusTarea", id_servicio_tarea: idServicioTarea, realizada: realizada },
			dataType: "json",
			success: function(respuesta){
				if (!respuesta.ok){
					Swal.fire({ icon: "error", title: "No se pudo actualizar", text: respuesta.mensaje });
				}
			}
		});
	});

	$(document).on("change", "#servEvidencia", function(){

		var archivo = this.files && this.files[0];

		if (!archivo){
			$("#servEvidenciaPreview").hide().attr("src", "");
			return;
		}

		var lector = new FileReader();
		lector.onload = function(e){
			$("#servEvidenciaPreview").attr("src", e.target.result).show();
		};
		lector.readAsDataURL(archivo);
	});

	$(document).on("click", "#servFinalizar", function(){

		var idServicio = $("#servIdServicio").val();
		var archivo = $("#servEvidencia")[0].files[0];

		if (!archivo){
			Swal.fire({ icon: "warning", title: "Falta la evidencia", text: "Sube una foto de evidencia antes de finalizar." });
			return;
		}

		var datos = new FormData();
		datos.append("accion", "finalizar");
		datos.append("id_servicio", idServicio);
		datos.append("id_habitacion", $("#servIdHabitacion").val());
		datos.append("evidencia", archivo);

		mostrarCargaServicio();

		$.ajax({
			url: "ajax/servicio.ajax.php",
			method: "POST",
			data: datos,
			cache: false,
			contentType: false,
			processData: false,
			dataType: "json",
			success: function(respuesta){
				if (respuesta.ok){
					$("#modalRealizarTarea").modal("hide");
					Swal.fire({ icon: "success", title: "Servicio finalizado", timer: 1500, showConfirmButton: false });
					servCargarHistorial();
				}else{
					Swal.fire({ icon: "error", title: "No se pudo finalizar", text: respuesta.mensaje });
				}
			},
			error: function(){
				Swal.fire({ icon: "error", title: "Error", text: "No se pudo finalizar el servicio. Intenta de nuevo." });
			},
			complete: ocultarCargaServicio
		});
	});

	$("#modalRealizarTarea").on("hidden.bs.modal", function(){
		$("#servSelectHabitacion").val("");
		$("#servIdHabitacion").val("");
		$("#servIdServicio").val("");
		$("#servModalHabitacion").text("");
		$("#servPasoSeleccion").show();
		$("#servPasoInicio").hide();
		$("#servPasoChecklist").hide();
		$("#servFinalizar").hide();
		$("#servEvidencia").val("");
		$("#servEvidenciaPreview").hide().attr("src", "");
		$("#servFotoInicio").val("");
		$("#servFotoInicioPreview").hide().attr("src", "");
	});

	$(document).on("click", ".servFinalizarFila", function(){

		var $boton = $(this);
		var idHabitacion = $boton.data("idHabitacion");
		var idServicio = $boton.data("idServicio");
		var nombreHabitacion = $boton.data("habitacionNombre");

		$("#servModalHabitacion").text(nombreHabitacion);
		$("#modalRealizarTarea").modal("show");

		servMostrarPasoChecklist(idHabitacion, idServicio);
	});

	$(document).on("click", ".servVerFoto", function(){
		$("#servFotoModalTitulo").html('<i class="fa fa-camera"></i> ' + escaparHtmlServicio($(this).data("titulo") || "Foto"));
		$("#servFotoModalImg").attr("src", $(this).data("foto"));
		$("#modalFotoServicio").modal("show");
	});

	$(document).on("change", "#servFiltroHabitacion, #servFiltroUsuario, #servFiltroFechaDesde, #servFiltroFechaHasta, #servFiltroCantidad", function(){
		servAplicarFiltros();
	});

	$(document).on("click", "#servLimpiarFiltros", function(){
		$("#servFiltroFechaDesde, #servFiltroFechaHasta").datepicker("clearDates");
		servAplicarFiltros();
	});

	// Calendario propio en vez del selector nativo: solo se puede escoger la fecha, no
	// escribirla a mano (el input ya queda "readonly" en el HTML).
	$("#servFiltroFechaDesde, #servFiltroFechaHasta").datepicker({
		format: "yyyy-mm-dd",
		language: "es",
		autoclose: true,
		todayHighlight: true
	});

	servCargarHistorial();
});

// Tabla de limpiezas ya finalizadas, debajo del botón "Iniciar limpieza".
var servHistorialCompleto = [];

// Llena el filtro de Usuario con los nombres que realmente aparecen en el historial ya
// cargado (sin pedirle otra lista al servidor), conservando la selección actual si sigue
// siendo una opción válida.
function servLlenarFiltroUsuario(lista){

	var $select = $("#servFiltroUsuario");
	var seleccionado = $select.val();
	var usuarios = [];

	lista.forEach(function(s){
		if (s.usuario && usuarios.indexOf(s.usuario) === -1){
			usuarios.push(s.usuario);
		}
	});

	usuarios.sort(function(a, b){ return a.localeCompare(b); });

	$select.empty().append('<option value="">-- Selecciona un usuario --</option>');
	usuarios.forEach(function(usuario){
		$select.append(new Option(usuario, usuario));
	});

	if (seleccionado && usuarios.indexOf(seleccionado) !== -1){
		$select.val(seleccionado);
	}
}

function servAplicarFiltros(){

	var idHabitacion = $("#servFiltroHabitacion").val();
	var usuario = $("#servFiltroUsuario").val();
	var fechaDesde = $("#servFiltroFechaDesde").val();
	var fechaHasta = $("#servFiltroFechaHasta").val();

	var filtrado = servHistorialCompleto.filter(function(s){

		if (idHabitacion && String(s.idHabitacion) !== String(idHabitacion)){
			return false;
		}

		if (usuario && s.usuario !== usuario){
			return false;
		}

		if (fechaDesde && (!s.fechaInicioRaw || s.fechaInicioRaw < fechaDesde)){
			return false;
		}

		if (fechaHasta && (!s.fechaInicioRaw || s.fechaInicioRaw > fechaHasta)){
			return false;
		}

		return true;
	});

	// "Mostrar N registros" (10/20/25/45/50/100, igual que el resto del sistema): recorta
	// la lista ya filtrada a los primeros N, sin necesidad de una tabla de paginación aparte.
	var _cantidad = parseInt($("#servFiltroCantidad").val(), 10) || 10;
	servPintarHistorial(filtrado.slice(0, _cantidad));
}

function servPintarHistorial(lista){

	var $cuerpo = $("#servHistorialCuerpo");
	$cuerpo.empty();

	// La columna Acción no existe en el <thead> para el Administrador (ver servicio.php);
	// el modal de "Iniciar limpieza" tampoco se renderiza para ese perfil, así que se usa su
	// ausencia para saber si hay que pintar esa columna también en cada fila.
	var hayModalRealizarTarea = $("#modalRealizarTarea").length > 0;
	var colspanVacio = hayModalRealizarTarea ? 9 : 8;

	if (lista.length === 0){
		$cuerpo.append('<tr><td colspan="' + colspanVacio + '" class="text-center text-muted" style="padding:15px;">Todavía no hay limpiezas registradas.</td></tr>');
		return;
	}

	lista.forEach(function(s, indice){

		var accion = '<span class="serv-sin-dato">—</span>';

		if (s.enProceso && hayModalRealizarTarea){
			accion = '<button type="button" class="btn btn-success btn-xs servFinalizarFila"' +
				' data-id-habitacion="' + s.idHabitacion + '"' +
				' data-id-servicio="' + s.idServicio + '"' +
				' data-habitacion-nombre="' + escaparHtmlServicio(s.habitacion) + '">' +
				'<i class="fa fa-check"></i> Finalizar</button>';
		}

		$cuerpo.append(
			'<tr>' +
				'<td>' + (indice + 1) + '</td>' +
				'<td>' + escaparHtmlServicio(s.habitacion) + '</td>' +
				'<td>' + escaparHtmlServicio(s.usuario) + '</td>' +
				'<td>' + (s.fechaInicio ? escaparHtmlServicio(s.fechaInicio) : '<span class="serv-sin-dato">—</span>') + '</td>' +
				'<td>' + (s.fotoInicio ? ('<button type="button" class="serv-ver-foto servVerFoto" data-foto="' + escaparHtmlServicio(s.fotoInicio) + '" data-titulo="Foto inicial">Ver foto</button>') : '<span class="serv-sin-dato">Sin foto</span>') + '</td>' +
				'<td>' + (s.fechaFin ? escaparHtmlServicio(s.fechaFin) : '<span class="serv-sin-dato">En proceso…</span>') + '</td>' +
				'<td>' + (s.fotoResultado ? ('<button type="button" class="serv-ver-foto servVerFoto" data-foto="' + escaparHtmlServicio(s.fotoResultado) + '" data-titulo="Foto resultado">Ver foto</button>') : '<span class="serv-sin-dato">Sin foto</span>') + '</td>' +
				'<td>' + (s.tareasRealizadas ? escaparHtmlServicio(s.tareasRealizadas) : '<span class="serv-sin-dato">Ninguna</span>') + '</td>' +
				(hayModalRealizarTarea ? ('<td>' + accion + '</td>') : '') +
			'</tr>'
		);
	});
}

function servCargarHistorial(){

	if ($("#servHistorialCuerpo").length === 0){
		return;
	}

	$.ajax({
		url: "ajax/servicio.ajax.php",
		method: "GET",
		data: { accion: "historial" },
		dataType: "json",
		success: function(respuesta){
			servHistorialCompleto = respuesta.data || [];
			servLlenarFiltroUsuario(servHistorialCompleto);
			servAplicarFiltros();
		},
		error: function(){
			var _colspan = $("#modalRealizarTarea").length > 0 ? 9 : 8;
			$("#servHistorialCuerpo").html('<tr><td colspan="' + _colspan + '" class="text-center text-muted" style="padding:15px;">No se pudo cargar el historial.</td></tr>');
		}
	});
}

/*=============================================
 Corte diario por WhatsApp (solo Administrador): manda el reporte del día directo al
 teléfono guardado en la sesión, sin pedirlo (a diferencia del ticket de checkout).
 =============================================*/
$(document).on("click", "#servBtnReporteCorte", function(){

	var _fechaDesde = $("#servFiltroFechaDesde").val();
	var _fechaHasta = $("#servFiltroFechaHasta").val();

	if (!_fechaDesde || !_fechaHasta){
		Swal.fire({
			icon: "warning",
			title: "Selecciona un rango de fechas",
			text: "Para generar el reporte primero elige \"Desde\" y \"Hasta\" en los filtros de arriba."
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
			// Mismos filtros que ya están en pantalla (habitación, usuario, Desde, Hasta):
			// si no se tocaron, se mandan vacíos y el reporte se comporta igual que antes
			// (corte de hoy).
			return $.ajax({
				url: "extensions/tcpdf/Reportes/ReporteCorteLimpieza.php",
				method: "GET",
				dataType: "json",
				data: {
					idHabitacion: $("#servFiltroHabitacion").val() || "",
					nombreUsuarioFiltro: $("#servFiltroUsuario").val() || "",
					fechaDesde: $("#servFiltroFechaDesde").val() || "",
					fechaHasta: $("#servFiltroFechaHasta").val() || ""
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
