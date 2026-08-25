function mostrarCargaServicio(){
	$("#servOverlay").show();
}

function ocultarCargaServicio(){
	$("#servOverlay").hide();
}

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
				'<td>' + (indice + 1) + '.</td>' +
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

	if ($("#modalRealizarTarea").length === 0){
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

	$(document).on("click", "#servComenzar", function(){

		var idHabitacion = $("#servIdHabitacion").val();

		mostrarCargaServicio();

		$.ajax({
			url: "ajax/servicio.ajax.php",
			method: "POST",
			data: { accion: "iniciar", id_habitacion: idHabitacion },
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
	});
});
