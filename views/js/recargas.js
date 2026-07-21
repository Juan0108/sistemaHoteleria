/*=============================================
//ALIMENTAR TABLA//
 =============================================*/
$(document).ready(function(){

    var _IdUsuario = $("#usuario").val();

    $('.tablaRecargas').DataTable({
        destroy: true,
        'ajax' : {
            'url' : 'ajax/datatable-recargas.ajax.php',
            'data' : { 'id' : _IdUsuario },
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
    });
})



function ObtenerReporteGanancias(){

var _IdUsuario = $("#usuario").val();
window.open("extensions/tcpdf/Reportes/ReporteGanancias.php?Cu="+_IdUsuario,"_blank");

}