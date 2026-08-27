// El spinner de carga ahora es global (ver plantilla.php), así que este overlay local ya
// no se muestra para no encimarse visualmente con el global.
function mostrarCargaPreguntas(){}

function ocultarCargaPreguntas(){}

function refrescarTablaPreguntas(){

	mostrarCargaPreguntas();

	$.ajax({
		url: "ajax/preguntas.ajax.php",
		method: "GET",
		data: { accion: "listar" },
		dataType: "json",
		success: function(respuesta){

			var preguntas = respuesta.data || [];

			$("#pregContador").text(preguntas.length + (preguntas.length === 1 ? " pregunta cargada" : " preguntas cargadas"));

			var $cuerpo = $(".tablaPreguntas tbody");

			if ($cuerpo.length === 0){
				$(".tablaPreguntas").append("<tbody></tbody>");
				$cuerpo = $(".tablaPreguntas tbody");
			}

			$cuerpo.empty();

			if (preguntas.length === 0){
				$cuerpo.append('<tr><td colspan="4" class="text-center text-muted" style="padding:20px;">No hay preguntas registradas todavía.</td></tr>');
				return;
			}

			preguntas.forEach(function(pregunta, indice){

				var activa = parseInt(pregunta.idEstatus, 10) === 1;
				var badge = activa
					? '<span class="preg-badge preg-badge-activa">Activa</span>'
					: '<span class="preg-badge preg-badge-inhabilitada">Inhabilitada</span>';

				var botonEditar = '<button type="button" class="btn btnEditarPregunta"' +
					' data-id="' + pregunta.id + '"' +
					' data-pregunta="' + escaparHtmlPreguntas(pregunta.pregunta) + '"' +
					' data-id-estatus="' + pregunta.idEstatus + '">' +
					'<i class="fa fa-pencil"></i>' +
					'</button>';

				var botonEstatus = '<button type="button" class="btn btnCambiarEstatusPregunta ' + (activa ? "deshabilitar" : "habilitar") + '"' +
					' data-id="' + pregunta.id + '"' +
					' data-id-estatus="' + pregunta.idEstatus + '">' +
					'<i class="fa ' + (activa ? "fa-ban" : "fa-check") + '"></i> ' + (activa ? "Deshabilitar" : "Habilitar") +
					'</button>';

				$cuerpo.append(
					'<tr>' +
						'<td>' + (indice + 1) + '</td>' +
						'<td>' + escaparHtmlPreguntas(pregunta.pregunta) + '</td>' +
						'<td>' + badge + '</td>' +
						'<td><div class="btn-group" style="display:inline-flex; gap:6px;">' + botonEditar + botonEstatus + '</div></td>' +
					'</tr>'
				);
			});
		},
		error: function(){
			Swal.fire({ icon: "error", title: "Error", text: "No se pudieron cargar las preguntas." });
		},
		complete: ocultarCargaPreguntas
	});
}

function escaparHtmlPreguntas(valor){
	valor = valor == null ? "" : String(valor);
	return valor
		.replace(/&/g, "&amp;")
		.replace(/</g, "&lt;")
		.replace(/>/g, "&gt;")
		.replace(/"/g, "&quot;")
		.replace(/'/g, "&#39;");
}

$(document).ready(function(){

	if ($(".tablaPreguntas").length === 0){
		return;
	}

	refrescarTablaPreguntas();

	$(document).on("input", "#pregTexto", function(){
		$("#pregContadorCaracteres").text($(this).val().length + "/255 caracteres");
	});

	$("#modalAgregarPregunta").on("hidden.bs.modal", function(){
		$("#formAgregarPregunta")[0].reset();
		$("#pregContadorCaracteres").text("0/255 caracteres");
	});

	$(document).on("click", "#pregGuardar", function(){

		var pregunta = $.trim($("#pregTexto").val());

		if (!pregunta){
			Swal.fire({ icon: "warning", title: "Falta la pregunta", text: "Escribe el texto de la pregunta." });
			return;
		}

		mostrarCargaPreguntas();

		$.ajax({
			url: "ajax/preguntas.ajax.php",
			method: "POST",
			data: { accion: "agregar", pregunta: pregunta },
			dataType: "json",
			success: function(respuesta){
				if (respuesta.ok){
					$("#modalAgregarPregunta").modal("hide");
					Swal.fire({ icon: "success", title: "Pregunta agregada", timer: 1500, showConfirmButton: false });
					refrescarTablaPreguntas();
				}else{
					Swal.fire({ icon: "error", title: "No se pudo guardar", text: respuesta.mensaje });
				}
			},
			error: function(){
				Swal.fire({ icon: "error", title: "Error", text: "No se pudo guardar la pregunta. Intenta de nuevo." });
			},
			complete: ocultarCargaPreguntas
		});
	});

	$(document).on("click", ".btnCambiarEstatusPregunta", function(){

		var $boton = $(this);
		var idPregunta = $boton.data("id");
		var idEstatusActual = $boton.data("idEstatus");
		var activa = parseInt(idEstatusActual, 10) === 1;

		Swal.fire({
			title: activa ? "¿Deshabilitar esta pregunta?" : "¿Habilitar esta pregunta?",
			text: activa
				? "Ya no aparecerá disponible para usarse en checkout."
				: "Volverá a estar disponible para usarse en checkout.",
			icon: "warning",
			showCancelButton: true,
			confirmButtonText: activa ? "Sí, deshabilitar" : "Sí, habilitar",
			cancelButtonText: "Cancelar",
			confirmButtonColor: "#3f6b4a"
		}).then(function(resultado){

			if (!resultado.value){
				return;
			}

			mostrarCargaPreguntas();

			$.ajax({
				url: "ajax/preguntas.ajax.php",
				method: "POST",
				data: { accion: "cambiarEstatus", id_pregunta: idPregunta, id_estatus_actual: idEstatusActual },
				dataType: "json",
				success: function(respuesta){
					if (respuesta.ok){
						refrescarTablaPreguntas();
					}else{
						Swal.fire({ icon: "error", title: "No se pudo actualizar", text: respuesta.mensaje });
					}
				},
				error: function(){
					Swal.fire({ icon: "error", title: "Error", text: "No se pudo actualizar la pregunta. Intenta de nuevo." });
				},
				complete: ocultarCargaPreguntas
			});
		});
	});

	$(document).on("input", "#pregEditarTexto", function(){
		$("#pregEditarContadorCaracteres").text($(this).val().length + "/255 caracteres");
	});

	$("#modalEditarPregunta").on("hidden.bs.modal", function(){
		$("#formEditarPregunta")[0].reset();
		$("#pregEditarContadorCaracteres").text("0/255 caracteres");
	});

	$(document).on("click", ".btnEditarPregunta", function(){

		var $boton = $(this);
		var idPregunta = $boton.data("id");
		var pregunta = $boton.data("pregunta");
		var idEstatus = parseInt($boton.data("idEstatus"), 10);

		$("#pregEditarId").val(idPregunta);
		$("#pregEditarTexto").val(pregunta);
		$("#pregEditarContadorCaracteres").text(String(pregunta).length + "/255 caracteres");

		if (idEstatus === 1){
			$("#pregEditarEstatus").bootstrapToggle("on");
		}else{
			$("#pregEditarEstatus").bootstrapToggle("off");
		}

		$("#modalEditarPregunta").modal("show");
	});

	$(document).on("click", "#pregGuardarEdicion", function(){

		var idPregunta = $("#pregEditarId").val();
		var pregunta = $.trim($("#pregEditarTexto").val());
		var idEstatus = $("#pregEditarEstatus").prop("checked") ? 1 : 2;

		if (!pregunta){
			Swal.fire({ icon: "warning", title: "Falta la pregunta", text: "Escribe el texto de la pregunta." });
			return;
		}

		mostrarCargaPreguntas();

		$.ajax({
			url: "ajax/preguntas.ajax.php",
			method: "POST",
			data: { accion: "editar", id_pregunta: idPregunta, pregunta: pregunta, id_estatus: idEstatus },
			dataType: "json",
			success: function(respuesta){
				if (respuesta.ok){
					$("#modalEditarPregunta").modal("hide");
					Swal.fire({ icon: "success", title: "Pregunta modificada", timer: 1500, showConfirmButton: false });
					refrescarTablaPreguntas();
				}else{
					Swal.fire({ icon: "error", title: "No se pudo guardar", text: respuesta.mensaje });
				}
			},
			error: function(){
				Swal.fire({ icon: "error", title: "Error", text: "No se pudo guardar la edición. Intenta de nuevo." });
			},
			complete: ocultarCargaPreguntas
		});
	});
});
