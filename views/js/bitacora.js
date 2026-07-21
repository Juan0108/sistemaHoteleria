/*=============================================
  DataTable Bitácora con filtro de rango de fechas
=============================================*/

// Inicializar DataTable
var tabla = $('.tablaBitacoraInventario').DataTable({
  "ajax": "ajax/datatable-bitacora.ajax.php",
  "deferRender": true,
  "retrieve": true,
  "processing": true,
  "language": {
    "sProcessing": "Procesando...",
    "sLengthMenu": "Mostrar _MENU_ registros",
    "sZeroRecords": "No se encontraron resultados",
    "sEmptyTable": "Ningún dato disponible en esta tabla",
    "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_",
    "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0",
    "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
    "sSearch": "Buscar:",
    "oPaginate": {
      "sFirst": "Primero",
      "sLast": "Último",
      "sNext": "Siguiente",
      "sPrevious": "Anterior"
    }
  }
});

// Parser robusto para formato 'YYYY-MM-DD HH:mm:ss'
function parseFechaTabla(str) {
  if (!str) return null;
  var parts = str.trim().split(' ');
  var fecha = parts[0];
  var hora = parts[1] || '00:00:00';

  var f = fecha.split('-').map(Number); // [yyyy, mm, dd]
  var h = hora.split(':').map(Number);  // [HH, MM, SS]

  return new Date(f[0], f[1] - 1, f[2], h[0] || 0, h[1] || 0, h[2] || 0);
}

// Filtro personalizado de DataTables
$.fn.dataTable.ext.search.push(function(settings, data) {
  var fechaStr = data[2]; // Columna Fecha (índice 2)
  if (!fechaStr) return true;

  var fechaTabla = parseFechaTabla(fechaStr);
  if (!fechaTabla) return true;

  // Si hay rango seleccionado, aplicar filtro
  if (window.minDate && fechaTabla < window.minDate) return false;
  if (window.maxDate && fechaTabla > window.maxDate) return false;

  return true;
});

/*=============================================
  Inicializar Date Range Picker en el botón
=============================================*/
$('#daterange-btn').daterangepicker(
  {
    ranges   : {
      'Hoy'       : [moment(), moment()],
      'Ayer'      : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
      'Últimos 7 días': [moment().subtract(6, 'days'), moment()],
      'Últimos 30 días': [moment().subtract(29, 'days'), moment()],
      'Este mes'  : [moment().startOf('month'), moment().endOf('month')],
      'Mes pasado': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
    },
    startDate: moment().subtract(29, 'days'),
    endDate  : moment()
  },
  function (start, end) {
    // Mostrar rango seleccionado en el botón
    $('#daterange-btn span').html(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));

    // Guardar fechas globales para el filtro
    window.minDate = new Date(start.format('YYYY-MM-DD') + " 00:00:00");
    window.maxDate = new Date(end.format('YYYY-MM-DD') + " 23:59:59");

    // Redibujar tabla con filtro aplicado
    tabla.draw();
  }
);