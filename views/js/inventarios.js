/*=============================================
 Obtener Inventarios
 =============================================*/
$(document).ready(function(){

    var _IdUsuario = $("#usuario").val();

    $('.tablaInventario').DataTable({
        destroy: true,
        'ajax' : {
            'url' : 'ajax/datatable-inventarios.ajax.php',
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


/*=============================================
 Obtener Id´s 
 =============================================*/
function GetValuesInventario(){

	 var _Id = $('#Qbarra').val();
	 var _IdUsuario = $("#usuario").val();

 	var datos = new FormData();
 	datos.append("id",_Id);
 	datos.append("idN",_IdUsuario);

 	$.ajax({

 		url:"ajax/Inventario.ajax.php",
 		method: "POST",
 		data: datos,
 		cache: false,
 		contentType: false,
 		processData: false,
 		dataType: "json",
 		success: function(respuesta){

 			if(respuesta){

 				//Cargar Información si existe en tabla de inventarios
 				$("#Categoria").val(respuesta["Categoria"]);
 				$("#Marca").val(respuesta["Marca"]);
 				$("#SuMarca").val(respuesta["SubMarca"]);
 				$("#clasificacion").val(respuesta["Clasificacion"]);
 				$("#Producto").val(respuesta["Producto"]);
 				$("#Gramaje").val(respuesta["Gramaje"]);
 				$("#stockActual").val(respuesta["Stock"]);
 				$("#PrecioCompra").val(respuesta["PrecioCompra"]);
 				$("#PrecioVenta").val(respuesta["PrecioVenta"]);
 				$("#idInventario").val(respuesta["Id_Inventario"]); 				 				
 				$("#btnSave span").text("Actualizar Producto");
	
 			}else{
				$.ajax({

				 		url:"ajax/InventarioProductos.ajax.php",
				 		method: "POST",
				 		data: datos,
				 		cache: false,
				 		contentType: false,
				 		processData: false,
				 		dataType: "json",
				 		success: function(inventariosProductos){

				 		if(inventariosProductos){

					 		//Cargar Información si existe en tabla de Productos
			 				$("#Categoria").val(inventariosProductos["Categoria"]);
			 				$("#Marca").val(inventariosProductos["Marca"]);
			 				$("#SuMarca").val(inventariosProductos["SubMarca"]);
			 				$("#clasificacion").val(inventariosProductos["Clasificacion"]);
			 				$("#Producto").val(inventariosProductos["Producto"]);
			 				$("#Gramaje").val(inventariosProductos["Gramaje"]);
			 				$("#stockActual").val(0);
			 				$("#PrecioCompra").val("");
			 				$("#PrecioVenta").val("");
			 				$("#idInventario").val("");
			 				$("#btnSave span").text("Insertar Producto");

				 		}else{

				 			$("#Categoria").val("");
			 				$("#Marca").val("");
			 				$("#SuMarca").val("");
			 				$("#clasificacion").val("");
			 				$("#Producto").val("");
			 				$("#Gramaje").val("");
			 				$("#stockActual").val(0);
			 				$("#stockNuevo").val(0);
			 				$("#PrecioCompra").val("");
			 				$("#PrecioVenta").val("");
			 				$("#Qbarra").val("");
			 				$("#idInventario").val("");			 				

				 			Swal.fire({
							  title: "Sistema PosDit",
						      text: "¡Favor de comunicarse con Soporte Tecnico para dar de alta el producto!",
						      icon: "error",
						      confirmButtonText: "¡Cerrar!"
						    });
				 		}
				 	} 

				 })
 			}

 		} 

 	})

}

function ObtenerReporte(){

var _IdUsuario = $("#usuario").val();	
window.open("extensions/tcpdf/Reportes/ReporteInventario.php?Cu="+_IdUsuario,"_blank");

}