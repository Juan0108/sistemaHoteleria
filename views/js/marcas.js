/*=============================================
 Edita Marca
 =============================================*/

 $(".tablaMarcas").on("click",".btnEditarMarca", function(){

 	var _IdMarca = $(this).attr("id_marca");

 	var datos = new FormData();
 	datos.append("id_marca",_IdMarca);

 	$.ajax({

 		url:"ajax/marcas.ajax.php",
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

 			$("#editarMarca").val(respuesta["Nombre"]);
 			$("#editarDescripcion").val(respuesta["Descripcion"]);
 			$("#editaridCategoria").val(respuesta["Id_Categoria"]);
 			$("#editarcbcategoria").val(respuesta["Id_Categoria"]);
 			$("#idmarca").val(respuesta["Id_Marca"]);

 			
 		} 

 	})

 })

/*=============================================
 Obtener Marcas
 =============================================*/
$('.tablaMarcas').DataTable( {
    "ajax": "ajax/datatable-marcas.ajax.php",
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

/*=============================================
 Obtener categorias Combobox Agregar
 =============================================*/
$(document).ready(function(){

	 $.ajax({

		url: "ajax/combobox-categorias.ajax.php",
		dataType: "text",
		success: function(data){
		var jsonData = JSON.parse(data);
		for (var i = 0; i < jsonData.data.length; i++) {
		    var counter = jsonData.data[i];

		    $("#cbcategoria").append(new Option(counter.Nombre,counter.Id_Categoria));
		}

		}
	})
})

/*=============================================
 Obtener categorias Combobox Editar
 =============================================*/
$(document).ready(function(){

	 $.ajax({

		url: "ajax/combobox-categorias.ajax.php",
		dataType: "text",
		success: function(data){
		var jsonData = JSON.parse(data);
		for (var i = 0; i < jsonData.data.length; i++) {
		    var counter = jsonData.data[i];

		    $("#editarcbcategoria").append(new Option(counter.Nombre,counter.Id_Categoria));
		}

		}
	})
})

/*=============================================
 Obtener IdCategoria
 =============================================*/
function Getvalue(){

	$("#NuevaidCategoria").val($('#cbcategoria').val());
}
