//SideBar Menú

$('.sidebar-menu').tree()

/*=============================================
Data Table
=============================================*/

$(".tablas").DataTable({

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

});

$('#login-button').click(function(){
	$('#login-button').fadeOut("slow",function(){
	  $("#container").fadeIn();
	  TweenMax.from("#container", .4, { scale: 0, ease:Sine.easeInOut});
	  TweenMax.to("#container", .4, { scale: 1, ease:Sine.easeInOut});
	});
  });

/*=============================================
 Bloquea escribir directamente en cualquier input[type="date"] del sistema (solo se
 puede elegir la fecha con el calendario nativo). Delegado en document para que tambien
 aplique a campos que se agregan despues dinamicamente (ej. el modal de Reabrir de
 Mantenimiento).
 =============================================*/
$(document).on("keydown", 'input[type="date"]', function(e){
	e.preventDefault();
});
$(document).on("paste beforeinput", 'input[type="date"]', function(e){
	e.preventDefault();
});
