/*=============================================
 Edita Categoria
 =============================================*/

 $(".tablaCategorias").on("click",".btnEditarCategoria", function(){

 	var _IdCategoria = $(this).attr("id_categoria");

 	var datos = new FormData();
 	datos.append("id_categoria",_IdCategoria);

 	$.ajax({

 		url:"ajax/categorias.ajax.php",
 		method: "POST",
 		data: datos,
 		cache: false,
 		contentType: false,
 		processData: false,
 		dataType: "json",
 		success: function(respuesta){

 			var _estatus = respuesta["Estatus"];

 			if(_estatus == 'checked'){
				$('#editarEstatus').bootstrapToggle('on');
 			}else{
				$('#editarEstatus').bootstrapToggle('off');
 			}

 			$("#editarCategoria").val(respuesta["Nombre"]);
 			$("#editarDescripcion").val(respuesta["Descripcion"]);
 			$("#idCategoria").val(respuesta["Id_Categoria"]);
 			

 		} 

 	})

 })


$('.tablaCategorias').DataTable( {
    "ajax": "ajax/datatable-categorias.ajax.php",
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

} )

 