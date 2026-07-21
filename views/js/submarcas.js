/*=============================================
 Obtener SubMarcas
 =============================================*/
$('.tablaSubMarcas').DataTable( {
    "ajax": "ajax/datatable-submarcas.ajax.php",
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
 Obtener Marcas Combobox Agregar
 =============================================*/
$(document).ready(function(){

	 $.ajax({

		url: "ajax/combobox-marcas.ajax.php",
		dataType: "text",
		success: function(data){
		var jsonData = JSON.parse(data);
		for (var i = 0; i < jsonData.data.length; i++) {
		    var counter = jsonData.data[i];

		    $("#cbMarcas").append(new Option(counter.Nombre,counter.Id_Marca));
		}

		}
	})
})

/*=============================================
 Obtener categorias Combobox Editar
 =============================================*/
$(document).ready(function(){

	 $.ajax({

		url: "ajax/combobox-marcas.ajax.php",
		dataType: "text",
		success: function(data){
		var jsonData = JSON.parse(data);
		for (var i = 0; i < jsonData.data.length; i++) {
		    var counter = jsonData.data[i];

		    $("#editarcbMarcas").append(new Option(counter.Nombre,counter.Id_Marca));
		}

		}
	})
})

/*=============================================
 Obtener IdCategoria
 =============================================*/
function GetvalueMarcas(){

	$("#NuevaidMarca").val($('#cbMarcas').val());
}

/*=============================================
 Edita subMarca
 =============================================*/

 $(".tablaSubMarcas").on("click",".btnEditarSubMarca", function(){

 	var _IdSubMarca = $(this).attr("id_submarca");

 	var datos = new FormData();
 	datos.append("id_submarca",_IdSubMarca);

 	$.ajax({

 		url:"ajax/submarcas.ajax.php",
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

 			$("#editarsubMarca").val(respuesta["SubMarca"]);
 			$("#EditaridMarca").val(respuesta["Id_Marca"]);
 			$("#editarcbMarcas").val(respuesta["Id_Marca"]);
 			$("#idsubmarca").val(respuesta["Id_SubMarca"]);

 			
 		} 

 	})

 })