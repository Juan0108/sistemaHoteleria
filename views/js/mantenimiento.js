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
 Helpers de tablero: refrescar el contador y el mensaje "vacío" de una
 columna después de mover/quitar una tarjeta en el DOM (sin recargar)
 =============================================*/
function mttoColumnaBody(nombreColumna){
	return $(".mtto-columna-" + nombreColumna + " .mtto-columna-body");
}

function mttoActualizarColumna($body){
	var _cantidad = $body.find(".mtto-tarjeta").length;

	$body.closest(".mtto-columna").find(".mtto-columna-contador").text(_cantidad);

	var $vacio = $body.find(".mtto-vacio");
	if (_cantidad === 0) {
		if ($vacio.length === 0) {
			$body.append('<div class="mtto-vacio">' + escaparHtmlMtto($body.data("vacio")) + '</div>');
		}
	} else {
		$vacio.remove();
	}
}

/*=============================================
 Detalle de la incidencia (encargado y fechas)
 =============================================*/
$(document).on("click", ".btnDetalleMtto", function(){

	var _idMantenimiento = $(this).attr("idMantenimiento");

	$("#mttoDetalleContenido").html('<p class="text-muted text-center" style="padding:20px;">Cargando…</p>');
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

			var html = '<table class="mtto-detalle-tabla">' +
				'<tr><th><i class="fa fa-user"></i> Persona encargada</th><td>' + escaparHtmlMtto(d.encargado) + '</td></tr>' +
				'<tr><th><i class="fa fa-calendar-plus-o"></i> Fecha de registro</th><td>' + escaparHtmlMtto(d.fechaRegistro) + '</td></tr>' +
				'<tr><th><i class="fa fa-calendar-plus-o"></i> Fecha inicio estimada</th><td>' + escaparHtmlMtto(d.fechaInicioEstimado) + '</td></tr>' +
				'<tr><th><i class="fa fa-hourglass-half"></i> Fecha fin estimada</th><td>' + escaparHtmlMtto(d.fechaFinEstimado) + '</td></tr>' +
				'<tr><th><i class="fa fa-check-circle"></i> Resuelto (real)</th><td>' + (d.fechaResuelto ? escaparHtmlMtto(d.fechaResuelto) : '<span class="mtto-detalle-vacio">Aún no se resuelve</span>') + '</td></tr>' +
				'<tr><th><i class="fa fa-undo"></i> Veces reabierta</th><td>' + (d.vecesReabierta > 0 ? d.vecesReabierta : '<span class="mtto-detalle-vacio">Nunca se ha reabierto</span>') + '</td></tr>' +
				'<tr><th><i class="fa fa-camera"></i> Foto</th><td>' + (d.foto ? ('<a href="' + escaparHtmlMtto(d.foto) + '" target="_blank"><img src="' + escaparHtmlMtto(d.foto) + '" class="mtto-detalle-foto" alt="Foto de la incidencia"></a>') : '<span class="mtto-detalle-vacio">Sin foto</span>') + '</td></tr>' +
				'</table>';

			$("#mttoDetalleContenido").html(html);
		},
		error: function(){
			$("#mttoDetalleContenido").html('<p class="mtto-detalle-vacio text-center">No se pudo cargar el detalle.</p>');
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

	if (contarPalabrasMtto(_campo.value) < 10) {
		e.preventDefault();
		actualizarContadorDescripcionMtto(_campo);
		Swal.fire({
			icon: "error",
			title: "Sistema PosDit",
			text: "La descripción es obligatoria y debe tener al menos 10 palabras.",
			confirmButtonText: "Cerrar"
		});
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
$(document).on("click", ".btnCambiarEstatus", function(){

	var _idMantenimiento = $(this).attr("idMantenimiento");
	var _idEstatus = $(this).attr("idEstatus");
	var _tarjeta = $(this).closest(".mtto-tarjeta");
	var $origenBody = _tarjeta.closest(".mtto-columna-body");

	$.ajax({
		url: "ajax/mantenimiento-cambiar-estatus.ajax.php",
		method: "POST",
		data: { idMantenimiento: _idMantenimiento, idEstatus: _idEstatus },
		dataType: "json",
		success: function(respuesta){
			if (respuesta && respuesta.status === "success" && respuesta.columna && respuesta.html) {
				// En vez de recargar la página, se mueve la tarjeta a su nueva
				// columna con el HTML que ya regresó el servidor.
				_tarjeta.remove();
				mttoActualizarColumna($origenBody);

				var $destinoBody = mttoColumnaBody(respuesta.columna);
				$destinoBody.append(respuesta.html);
				mttoActualizarColumna($destinoBody);
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
});

/*=============================================
 Reordenar dentro de la misma columna
 =============================================*/
$(document).on("click", ".btnMoverOrden", function(){

	var _boton = $(this);
	var _tarjeta = _boton.closest(".mtto-tarjeta");
	var _idMantenimiento = _boton.attr("idMantenimiento");
	var _direccion = _boton.attr("direccion");

	$.ajax({
		url: "ajax/mantenimiento-mover-orden.ajax.php",
		method: "POST",
		data: { idMantenimiento: _idMantenimiento, direccion: _direccion },
		dataType: "json",
		success: function(respuesta){
			if (respuesta && respuesta.status === "success") {
				// En vez de recargar la página, se mueve la tarjeta directamente
				// en pantalla para que se sienta inmediato (sin spinner).
				if (_direccion === "arriba") {
					var _anterior = _tarjeta.prev(".mtto-tarjeta");
					if (_anterior.length) { _tarjeta.insertBefore(_anterior); }
				} else {
					var _siguiente = _tarjeta.next(".mtto-tarjeta");
					if (_siguiente.length) { _tarjeta.insertAfter(_siguiente); }
				}
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
	var _tarjeta = $(this).closest(".mtto-tarjeta");
	var $body = _tarjeta.closest(".mtto-columna-body");

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
							// En vez de recargar la página, se quita la tarjeta del DOM.
							_tarjeta.remove();
							mttoActualizarColumna($body);
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
