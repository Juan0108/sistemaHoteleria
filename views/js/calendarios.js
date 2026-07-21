/*=============================================
 Obtener Fechas
 =============================================*/
$(document).ready(function(){

	var _negocio = $("#negocio").val();

	$('.tablaCalendario').DataTable( {
		destroy: true,
        'ajax' : {
            'url' : 'ajax/datatable-calendarios.ajax.php',
            'data' : { 'idNegocio' : _negocio },
            'type' : 'post'
        },
        "deferRender": true,
        "processing": true,
		"language": {

			
				"sProcessing":     "Procesando...",
				"sLengthMenu":     "Mostrar _MENU_ registros",
				"sZeroRecords":    "No se encontraron resultados",
				"sEmptyTable":     "Ningún dato disponible en esta tabla",
				"sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_",
				"sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0",
				"sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
				"sInfoPostFix":    "",
				"sSearch":         "Buscar:",
				"sUrl":            "",
				"sInfoThousands":  ",",
				"sLoadingRecords": "Cargando...",
				"oPaginate": {
				"sFirst":    "Primero",
				"sLast":     "Último",
				"sNext":     "Siguiente",
				"sPrevious": "Anterior"
				},
				"oAria": {
					"sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
					"sSortDescending": ": Activar para ordenar la columna de manera descendente"
				}

		}

	})
})


//Editar
$(".tablaCalendario").on("click",".btnEditarCalendario", function(){

	var _idEvento = $(this).attr("Id_Evento");
	var _idNegocio = $("#negocio").val();
	
	var datos = new FormData();
	datos.append("Id_Evento", _idEvento);
	datos.append("IdNegocio", _idNegocio);
	$.ajax({

		url:"ajax/calendarios.ajax.php",
		method: "POST",
		data: datos,
		cache: false,
		contentType: false,
		processData: false,
		dataType: "json",
		success: function(respuesta){
            $("#idEvento").val(respuesta["Id_Evento"]);
			$("#Nombre").val(respuesta["Nombre"]);
			$("#DiaEvento").val(respuesta["DiaEvento"]);
			$("#editarComentario").val(respuesta["Comentario"]);
		} 

	})

})

//Calendario
if (window.location.pathname.includes("calendarios")) {
	document.addEventListener('DOMContentLoaded', function () {
	  var calendarEl = document.getElementById('calendar');
	  var _negocio = $("#negocio").val(); // Obtener el valor del negocio
  
	  // Verificar si _negocio tiene un valor válido
	  if (!_negocio) {
		console.error("El valor de 'negocio' no está definido.");
		return;
	  }
  
	  var calendar = new FullCalendar.Calendar(calendarEl, {
		locale: 'es',
		initialView: 'dayGridMonth',
		headerToolbar: {
		  left: 'title',
		  center: 'prev,next today',
		  right: 'dayGridMonth,timeGridWeek,timeGridDay'
		},
		events: function (fetchInfo, successCallback, failureCallback) {
		  // Hacer la solicitud AJAX para obtener los eventos
		  $.ajax({
			url: 'ajax/datatable-calendarios.ajax.php', // Misma URL que en DataTable
			type: 'POST', // Cambiar a POST para coincidir con DataTable
			data: { 'idNegocio': _negocio }, // Enviar el ID del negocio
			dataType: 'json',
			success: function (data) {
				
			  // Verificar si la respuesta tiene la estructura esperada
			  if (data && data.data) {
				// Transformar la respuesta en el formato que FullCalendar espera
				const eventos = data.data.map(evento => ({
				  id: evento[0], // ID del evento
				  title: `${evento[1]} - ${evento[4] || 'Sin Comentarios'}`, // Título concatenado
				  start: evento[2], // Fecha de inicio
				  color: '#24AE28' // Color del evento
				}));
  
				// Pasar los eventos a FullCalendar
				successCallback(eventos);
			  } else {
				console.error("La respuesta no contiene datos válidos:", data);
				failureCallback("Datos no válidos");
			  }
			},
			error: function (xhr, status, error) {
			  console.error("Error en la solicitud AJAX:", status, error); // Registrar el error
			  console.error("Respuesta del servidor:", xhr.responseText); // Verificar la respuesta del servidor
			  failureCallback(error);
			}
		  });
		},
		eventClick: function (info) {
		  // Función para formatear la fecha en español
		  const formatFecha = (fecha) => {
			return new Intl.DateTimeFormat('es', {
			  year: 'numeric',
			  month: 'long',
			  day: 'numeric',
			  hour: 'numeric',
			  minute: 'numeric',
			  second: 'numeric',
			  hour12: true // Usar formato de 12 horas (AM/PM)
			}).format(new Date(fecha));
		  };
  
		  // Formatear la fecha de inicio
		  const fechaFormateada = formatFecha(info.event.start);
  
		  // Mostrar la alerta con la fecha en español
		  Swal.fire({
			icon: "success",
			title: "Sistema PosDit",
			html: `<h3>Eventos</h3>
				   <p>Evento: <strong>${info.event.title}</strong></p>
				   <p>Fecha: <strong>${fechaFormateada}</strong></p>`,
			showConfirmButton: true,
			confirmButtonText: "Cerrar"
		  });
		}
	  });
  
	  // Renderizar el calendario
	  calendar.render();
	});
}

//Limpiar Días
$(document).ready(function () {
    $('#diasEvento').select2({
      placeholder: 'Selecciona los días de la semana', // Texto inicial
      allowClear: true // Permitir limpiar selección
    });
});

if (window.location.pathname.includes("calendarios")){
	
	document.getElementById("horaNotificacion").addEventListener("input", function () {
		const valorHora = this.value;
		const [horas, minutos] = valorHora.split(":").map(Number);
	  
		// Si los minutos no son 00 o 30, forzar el valor a 00 o 30
		if (minutos !== 0 && minutos !== 30) {
		  // Si los minutos son menores a 30, cambiar a 00
		  if (minutos < 30) {
			this.value = `${horas.toString().padStart(2, "0")}:00`;
		  } 
		  // Si son mayores a 30, cambiar a 30
		  else {
			this.value = `${horas.toString().padStart(2, "0")}:30`;
		  }
		}
	});

}

  
