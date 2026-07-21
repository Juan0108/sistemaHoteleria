/*=============================================
 Obtener Clientes
 =============================================*/
$('.tablaClientes').DataTable({
    "ajax": "ajax/datatable-clientes.ajax.php",
    "deferRender": true,
    "retrieve": true,
    "processing": true,
    "language": {
        "sProcessing":     "Procesando...",
        "sLengthMenu":     "Mostrar _MENU_ registros",
        "sZeroRecords":    "No se encontraron resultados",
        "sEmptyTable":     "Ningún dato disponible en esta tabla",
        "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_",
        "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0",
        "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
        "sSearch":         "Buscar:",
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

/*=============================================
 Editar Cliente
 =============================================*/
$(document).on("click", ".btnEditarCliente", function(){

    var _idCliente = $(this).attr("data-id-cliente");
    var datos = new FormData();
    datos.append("idCliente", _idCliente);

    $.ajax({
        url: "ajax/clientes.ajax.php",
        method: "POST",
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function(respuesta){
            $("#editarIdCliente").val(respuesta["id_Cliente"]);
            $("#editarNombre").val(respuesta["Nombre"]);
            $("#editarAPaterno").val(respuesta["APaterno"]);
            $("#editarAMaterno").val(respuesta["AMaterno"]);
            $("#editarTelefono").val(respuesta["Telefono"]);
        }
    });

});
