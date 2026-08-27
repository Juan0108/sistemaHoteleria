// El spinner de carga ahora es global (ver plantilla.php), así que este overlay local ya
// no se muestra para no encimarse visualmente con el global.
function mostrarCargaTareas(){}

function ocultarCargaTareas(){}

function refrescarTablaTareas(){

	mostrarCargaTareas();

	$.ajax({
		url: "ajax/tareas.ajax.php",
		method: "GET",
		data: { accion: "listar" },
		dataType: "json",
		success: function(respuesta){

			var tareas = respuesta.data || [];

			$("#tarContador").text(tareas.length + (tareas.length === 1 ? " tarea cargada" : " tareas cargadas"));

			var $cuerpo = $(".tablaTareas tbody");

			if ($cuerpo.length === 0){
				$(".tablaTareas").append("<tbody></tbody>");
				$cuerpo = $(".tablaTareas tbody");
			}

			$cuerpo.empty();

			if (tareas.length === 0){
				$cuerpo.append('<tr><td colspan="4" class="text-center text-muted" style="padding:20px;">No hay tareas registradas todavía.</td></tr>');
				return;
			}

			tareas.forEach(function(tarea, indice){

				var activa = parseInt(tarea.idEstatus, 10) === 1;
				var badge = activa
					? '<span class="tar-badge tar-badge-activa">Activa</span>'
					: '<span class="tar-badge tar-badge-inhabilitada">Inhabilitada</span>';

				var botonEditar = '<button type="button" class="btn btnEditarTarea"' +
					' data-id="' + tarea.id + '"' +
					' data-tarea="' + escaparHtmlTareas(tarea.tarea) + '"' +
					' data-id-estatus="' + tarea.idEstatus + '">' +
					'<i class="fa fa-pencil"></i>' +
					'</button>';

				var botonEstatus = '<button type="button" class="btn btnCambiarEstatusTarea ' + (activa ? "deshabilitar" : "habilitar") + '"' +
					' data-id="' + tarea.id + '"' +
					' data-id-estatus="' + tarea.idEstatus + '">' +
					'<i class="fa ' + (activa ? "fa-ban" : "fa-check") + '"></i> ' + (activa ? "Deshabilitar" : "Habilitar") +
					'</button>';

				$cuerpo.append(
					'<tr>' +
						'<td>' + (indice + 1) + '</td>' +
						'<td>' + escaparHtmlTareas(tarea.tarea) + '</td>' +
						'<td>' + badge + '</td>' +
						'<td><div class="btn-group" style="display:inline-flex; gap:6px;">' + botonEditar + botonEstatus + '</div></td>' +
					'</tr>'
				);
			});
		},
		error: function(){
			Swal.fire({ icon: "error", title: "Error", text: "No se pudieron cargar las tareas." });
		},
		complete: ocultarCargaTareas
	});
}

function escaparHtmlTareas(valor){
	valor = valor == null ? "" : String(valor);
	return valor
		.replace(/&/g, "&amp;")
		.replace(/</g, "&lt;")
		.replace(/>/g, "&gt;")
		.replace(/"/g, "&quot;")
		.replace(/'/g, "&#39;");
}

$(document).ready(function(){

	if ($(".tablaTareas").length === 0){
		return;
	}

	refrescarTablaTareas();

	$(document).on("input", "#tarTexto", function(){
		$("#tarContadorCaracteres").text($(this).val().length + "/255 caracteres");
	});

	$("#modalAgregarTarea").on("hidden.bs.modal", function(){
		$("#formAgregarTarea")[0].reset();
		$("#tarContadorCaracteres").text("0/255 caracteres");
	});

	$(document).on("click", "#tarGuardar", function(){

		var tarea = $.trim($("#tarTexto").val());

		if (!tarea){
			Swal.fire({ icon: "warning", title: "Falta la tarea", text: "Escribe el texto de la tarea." });
			return;
		}

		mostrarCargaTareas();

		$.ajax({
			url: "ajax/tareas.ajax.php",
			method: "POST",
			data: { accion: "agregar", tarea: tarea },
			dataType: "json",
			success: function(respuesta){
				if (respuesta.ok){
					$("#modalAgregarTarea").modal("hide");
					Swal.fire({ icon: "success", title: "Tarea agregada", timer: 1500, showConfirmButton: false });
					refrescarTablaTareas();
				}else{
					Swal.fire({ icon: "error", title: "No se pudo guardar", text: respuesta.mensaje });
				}
			},
			error: function(){
				Swal.fire({ icon: "error", title: "Error", text: "No se pudo guardar la tarea. Intenta de nuevo." });
			},
			complete: ocultarCargaTareas
		});
	});

	$(document).on("click", ".btnCambiarEstatusTarea", function(){

		var $boton = $(this);
		var idTarea = $boton.data("id");
		var idEstatusActual = $boton.data("idEstatus");
		var activa = parseInt(idEstatusActual, 10) === 1;

		Swal.fire({
			title: activa ? "¿Deshabilitar esta tarea?" : "¿Habilitar esta tarea?",
			text: activa
				? "Ya no aparecerá disponible para validarse en servicios."
				: "Volverá a estar disponible para validarse en servicios.",
			icon: "warning",
			showCancelButton: true,
			confirmButtonText: activa ? "Sí, deshabilitar" : "Sí, habilitar",
			cancelButtonText: "Cancelar",
			confirmButtonColor: "#3f6b4a"
		}).then(function(resultado){

			if (!resultado.value){
				return;
			}

			mostrarCargaTareas();

			$.ajax({
				url: "ajax/tareas.ajax.php",
				method: "POST",
				data: { accion: "cambiarEstatus", id_tarea: idTarea, id_estatus_actual: idEstatusActual },
				dataType: "json",
				success: function(respuesta){
					if (respuesta.ok){
						refrescarTablaTareas();
					}else{
						Swal.fire({ icon: "error", title: "No se pudo actualizar", text: respuesta.mensaje });
					}
				},
				error: function(){
					Swal.fire({ icon: "error", title: "Error", text: "No se pudo actualizar la tarea. Intenta de nuevo." });
				},
				complete: ocultarCargaTareas
			});
		});
	});

	$(document).on("input", "#tarEditarTexto", function(){
		$("#tarEditarContadorCaracteres").text($(this).val().length + "/255 caracteres");
	});

	$("#modalEditarTarea").on("hidden.bs.modal", function(){
		$("#formEditarTarea")[0].reset();
		$("#tarEditarContadorCaracteres").text("0/255 caracteres");
	});

	$(document).on("click", ".btnEditarTarea", function(){

		var $boton = $(this);
		var idTarea = $boton.data("id");
		var tarea = $boton.data("tarea");
		var idEstatus = parseInt($boton.data("idEstatus"), 10);

		$("#tarEditarId").val(idTarea);
		$("#tarEditarTexto").val(tarea);
		$("#tarEditarContadorCaracteres").text(String(tarea).length + "/255 caracteres");

		if (idEstatus === 1){
			$("#tarEditarEstatus").bootstrapToggle("on");
		}else{
			$("#tarEditarEstatus").bootstrapToggle("off");
		}

		$("#modalEditarTarea").modal("show");
	});

	$(document).on("click", "#tarGuardarEdicion", function(){

		var idTarea = $("#tarEditarId").val();
		var tarea = $.trim($("#tarEditarTexto").val());
		var idEstatus = $("#tarEditarEstatus").prop("checked") ? 1 : 2;

		if (!tarea){
			Swal.fire({ icon: "warning", title: "Falta la tarea", text: "Escribe el texto de la tarea." });
			return;
		}

		mostrarCargaTareas();

		$.ajax({
			url: "ajax/tareas.ajax.php",
			method: "POST",
			data: { accion: "editar", id_tarea: idTarea, tarea: tarea, id_estatus: idEstatus },
			dataType: "json",
			success: function(respuesta){
				if (respuesta.ok){
					$("#modalEditarTarea").modal("hide");
					Swal.fire({ icon: "success", title: "Tarea modificada", timer: 1500, showConfirmButton: false });
					refrescarTablaTareas();
				}else{
					Swal.fire({ icon: "error", title: "No se pudo guardar", text: respuesta.mensaje });
				}
			},
			error: function(){
				Swal.fire({ icon: "error", title: "Error", text: "No se pudo guardar la edición. Intenta de nuevo." });
			},
			complete: ocultarCargaTareas
		});
	});
});
