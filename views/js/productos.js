
/*=============================================
 Agregar Producto
 =========================================================================================================================*/
/*=============================================
 Obtener Productos
 =============================================*/
$('.tablaProductos').DataTable( {
    "ajax": "ajax/datatable-productos.ajax.php",
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
 Refresca Combos
 =============================================*/
$(document).ready(function(){

	 $.ajax({

		url: "ajax/combobox-categorias.ajax.php",
		dataType: "text",
		success: function(data){
		var jsonData = JSON.parse(data);
			for (var i = 0; i < jsonData.data.length; i++) {
			    var counter = jsonData.data[i];

			    $("#cbcategoriaproductos").append(new Option(counter.Nombre,counter.Id_Categoria));
			}
		}
	})	 

//Combobox Categorias 
 $('#cbcategoriaproductos').on('change', function(){
 	
 	$("#cbMarcasActivas").empty().append('<option value="" selected hidden>--Seleccionar Marca--</option>');
 	$("#cbsubMarcasActivas").empty().append('<option value="" selected hidden>--Seleccionar Sub-Marca--</option>');
	$("#cbclasificaciones").empty().append('<option value="" selected hidden>--Seleccionar Clasificación--</option>');

    var id = $('#cbcategoriaproductos').val()
    $.ajax({
      type: 'POST',
      url: 'ajax/combobox-filtro-marcas.ajax.php',
      data: {'id': id}
    })
    .done(function(Marcas){
      
		var jsonData = JSON.parse(Marcas);
		for (var i = 0; i < jsonData.Marcas.length; i++) {
		    var counter = jsonData.Marcas[i];

		    $("#cbMarcasActivas").append(new Option(counter.Nombre,counter.Id_Marca));
		}

    })
    .fail(function(){
      alert('Hubo un errror al cargar las categorias')
    })

    $.ajax({
      type: 'POST',
      url: 'ajax/combobox-filtro-clasificaciones.ajax.php',
      data: {'id': id}
    })
    .done(function(Clasificaciones){
		var jsonData = JSON.parse(Clasificaciones);
		for (var i = 0; i < jsonData.Clasificaciones.length; i++) {
		    var counter = jsonData.Clasificaciones[i];

		    $("#cbclasificaciones").append(new Option(counter.Nombre,counter.id_clasificacion));
		}

    })
    .fail(function(){
      alert('Hubo un errror al cargar las clasificaciones')
    })

  })

//Combobox Marcas
$('#cbMarcasActivas').on('change', function(){
 	
	$("#cbsubMarcasActivas").empty().append('<option value="" selected hidden>--Seleccionar Sub-Marca--</option>');

    var id = $('#cbMarcasActivas').val()
    $.ajax({
      type: 'POST',
      url: 'ajax/combobox-filtro-submarcas.ajax.php',
      data: {'id': id}
    })
    .done(function(SubMarcas){

		var jsonData = JSON.parse(SubMarcas);
		for (var i = 0; i < jsonData.SubMarcas.length; i++) {
		    var counter = jsonData.SubMarcas[i];

		    $("#cbsubMarcasActivas").append(new Option(counter.SubMarca,counter.Id_SubMarca));
		}

    })
    .fail(function(){
      alert('Hubo un errror al cargar las SubMarcas')
    })
  })

})

/*=============================================
 Obtener Id´s 
 =============================================*/
function GetvalueCategoria(){

	$("#NidCategoria").val($('#cbcategoriaproductos').val());

}

function GetvalueMarca(){

	$("#NuevaidMarca").val($('#cbMarcasActivas').val());

}

function GetvalueSubMarca(){

	$("#NuevaidSubMarca").val($('#cbsubMarcasActivas').val());

}

function GetvalueClasificacion(){

	$("#NuevaidClasificacion").val($('#cbclasificaciones').val());

}


/*=============================================
 Editar Producto
 =============================================*/
$(document).ready(function(){

GetCategoriasEditar();

$(".tablaProductos").on("click",".btnEditarProducto", function(){

 	var _idproducto = $(this).attr("id_producto");
 	
 	var datos = new FormData();
 	datos.append("id_producto",_idproducto);

 	$.ajax({

 		url:"ajax/productos.ajax.php",
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


 			//Categorias Hidden 
 			$("#cbeditarcategoriaproductos").val(respuesta["Id_Categoria"]);
 			$("#editaridCategoria").val(respuesta["Id_Categoria"]);

 			//Marcas Hidden 
			$("#editaridMarca").val(respuesta["Id_Marca"]);

 			//Marcas Hidden 
			$("#editaridSubMarca").val(respuesta["Id_SubMarca"]);
			
			//Marcas Hidden 
			$("#editaridClasificacion").val(respuesta["Id_Clasificacion"]);

 			var IdCategoria = respuesta["Id_Categoria"];
 			var IdMarca = respuesta["Id_Marca"];
 			var IdSubMarca = respuesta["Id_SubMarca"];
 			var IdClasificacion = respuesta["Id_Clasificacion"];

 			GetMarcasEditar(IdCategoria,IdMarca);
 			GetSubMarcasEditar(IdMarca,IdSubMarca);
 			GetClasificacionesEditar(IdCategoria,IdClasificacion);

 			$("#editarProducto").val(respuesta["Producto"]);
 			$("#editarGramaje").val(respuesta["Gramaje"]);
 			$("#ProductoId").val(respuesta["Id_Producto"]);
 			
 		} 
 	})
 })
})


/*=============================================
 Carga Combobox (Editar)
 =============================================*/
function GetCategoriasEditar(){

	$.ajax({

		url: "ajax/combobox-categorias.ajax.php",
		dataType: "text",
		success: function(data){
		var jsonData = JSON.parse(data);
			for (var i = 0; i < jsonData.data.length; i++) {
			    var counter = jsonData.data[i];

			    $("#cbeditarcategoriaproductos").append(new Option(counter.Nombre,counter.Id_Categoria));
			}
		}		
	})
}

function GetMarcasEditar(IdCategoria,IdMarca){

	var id = IdCategoria;
	var marca = IdMarca

 	$("#cbeditarMarcasActivas").empty().append('<option value="" selected hidden>--Seleccionar Marca--</option>');

    $.ajax({
      type: 'POST',
      url: 'ajax/combobox-filtro-marcas.ajax.php',
      data: {'id': id}
    })
    .done(function(Marcas){
      
		var jsonData = JSON.parse(Marcas);
		for (var i = 0; i < jsonData.Marcas.length; i++) {
		    var counter = jsonData.Marcas[i];

		    $("#cbeditarMarcasActivas").append(new Option(counter.Nombre,counter.Id_Marca));
		}

		$("#cbeditarMarcasActivas").val(marca);

    })
    .fail(function(){
      alert('Hubo un errror al cargar las categorias')
    })
}

function GetSubMarcasEditar(IdMarca,IdSubMarca){

	var id = IdMarca;
	var submarca = IdSubMarca;

 	$("#cbeditarsubMarcasActivas").empty().append('<option value="" selected hidden>--Seleccionar Sub-Marca--</option>');

    $.ajax({
      type: 'POST',
      url: 'ajax/combobox-filtro-submarcas.ajax.php',
      data: {'id': id}
    })
    .done(function(SubMarcas){

		var jsonData = JSON.parse(SubMarcas);
		for (var i = 0; i < jsonData.SubMarcas.length; i++) {
		    var counter = jsonData.SubMarcas[i];

		    $("#cbeditarsubMarcasActivas").append(new Option(counter.SubMarca,counter.Id_SubMarca));
		}

		$("#cbeditarsubMarcasActivas").val(submarca);

    })
    .fail(function(){
      alert('Hubo un errror al cargar las SubMarcas')
    })
}

function GetClasificacionesEditar(IdCategoria,IdClasificacion){

	var id = IdCategoria;
	var Clasificacion = IdClasificacion;

	$("#cbeditarclasificaciones").empty().append('<option value="" selected hidden>--Seleccionar Clasificación--</option>');

 	$.ajax({
      type: 'POST',
      url: 'ajax/combobox-filtro-clasificaciones.ajax.php',
      data: {'id': id}
    })
    .done(function(Clasificaciones){
		var jsonData = JSON.parse(Clasificaciones);
		for (var i = 0; i < jsonData.Clasificaciones.length; i++) {
		    var counter = jsonData.Clasificaciones[i];

		    $("#cbeditarclasificaciones").append(new Option(counter.Nombre,counter.id_clasificacion));
		}

		$("#cbeditarclasificaciones").val(Clasificacion);

    })
    .fail(function(){
      alert('Hubo un errror al cargar las clasificaciones')
    })
}

/*=============================================
 Obtener Id´s (Editar)
 =============================================*/
function GetvalueEditarCategorias(){

	$("#editaridCategoria").val($('#cbeditarcategoriaproductos').val());

}

function GetvalueEditarMarca(){

	$("#editaridMarca").val($('#cbeditarMarcasActivas').val());

}

function GetvalueEditarSubMarca(){

	$("#editaridSubMarca").val($('#cbeditarsubMarcasActivas').val());

}

function GetvalueEditarClasificacion(){

	$("#editaridClasificacion").val($('#cbeditarclasificaciones').val());

}


/*=============================================
 Cargar Combos Change (Editar)
 =============================================*/
$(document).ready(function(){

//Combobox Categorias 
 $('#cbeditarcategoriaproductos').on('change', function(){
 	
 	$("#cbeditarMarcasActivas").empty().append('<option value="" selected hidden>--Seleccionar Marca--</option>');
 	$("#cbeditarsubMarcasActivas").empty().append('<option value="" selected hidden>--Seleccionar Sub-Marca--</option>');
	$("#cbeditarclasificaciones").empty().append('<option value="" selected hidden>--Seleccionar Clasificación--</option>');

    var id = $('#cbeditarcategoriaproductos').val()
    $.ajax({
      type: 'POST',
      url: 'ajax/combobox-filtro-marcas.ajax.php',
      data: {'id': id}
    })
    .done(function(Marcas){
      
		var jsonData = JSON.parse(Marcas);
		for (var i = 0; i < jsonData.Marcas.length; i++) {
		    var counter = jsonData.Marcas[i];

		    $("#cbeditarMarcasActivas").append(new Option(counter.Nombre,counter.Id_Marca));
		}

    })
    .fail(function(){
      alert('Hubo un errror al cargar las categorias')
    })

    $.ajax({
      type: 'POST',
      url: 'ajax/combobox-filtro-clasificaciones.ajax.php',
      data: {'id': id}
    })
    .done(function(Clasificaciones){
		var jsonData = JSON.parse(Clasificaciones);
		for (var i = 0; i < jsonData.Clasificaciones.length; i++) {
		    var counter = jsonData.Clasificaciones[i];

		    $("#cbeditarclasificaciones").append(new Option(counter.Nombre,counter.id_clasificacion));
		}

    })
    .fail(function(){
      alert('Hubo un errror al cargar las clasificaciones')
    })

  })

//Combobox Marcas
$('#cbeditarMarcasActivas').on('change', function(){
 	
	$("#cbeditarsubMarcasActivas").empty().append('<option value="" selected hidden>--Seleccionar Sub-Marca--</option>');

    var id = $('#cbeditarMarcasActivas').val()
    $.ajax({
      type: 'POST',
      url: 'ajax/combobox-filtro-submarcas.ajax.php',
      data: {'id': id}
    })
    .done(function(SubMarcas){

		var jsonData = JSON.parse(SubMarcas);
		for (var i = 0; i < jsonData.SubMarcas.length; i++) {
		    var counter = jsonData.SubMarcas[i];

		    $("#cbeditarsubMarcasActivas").append(new Option(counter.SubMarca,counter.Id_SubMarca));
		}

    })
    .fail(function(){
      alert('Hubo un errror al cargar las SubMarcas')
    })
  })

})