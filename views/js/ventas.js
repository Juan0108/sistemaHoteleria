/*=============================================
//ALIMENTAR TABLA//
 =============================================*/
$(document).ready(function(){

    var _IdUsuario = $("#usuario").val();

    $('.tablaVentas').DataTable({
        destroy: true,
        'ajax' : {
            'url' : 'ajax/datatable-ventas.ajax.php',
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
//Grafica Ventas //
 =============================================*/
$(document).ready(function(){

    var _IdUsuario = $("#usuario").val();
 	var datos = new FormData();
 	datos.append("id",_IdUsuario);

 	$.ajax({

 		url:"ajax/graficaventasmensuales.ajax.php",
 		method: "POST",
 		data: datos,
 		cache: false,
 		contentType: false,
 		processData: false,
 		dataType: "json",
 		success: function(respuesta){

 			var line = new Morris.Line({
		    element          : 'line-chart-ventas',
		    resize           : true,
		    data             : respuesta,
		    xkey             : 'y',
		    ykeys            : ['ventas'],
		    labels           : ['Venta Mensual'],
		    lineColors       : ['#efefef'],
		    lineWidth        : 2,
		    hideHover        : 'auto',
		    gridTextColor    : '#fff',
		    gridStrokeWidth  : 0.4,
		    pointSize        : 4,
		    pointStrokeColors: ['#efefef'],
		    gridLineColor    : '#efefef',
		    gridTextFamily   : 'Open Sans',
		    preUnits         : '$',
		    gridTextSize     : 10
		  }); 			
 		} 
 	})	
})

/*=============================================
//Grafica Ventas //
 =============================================*/
$(document).ready(function(){

	var _IdUsuario = $("#usuario").val();
 	var datos = new FormData();
 	datos.append("id",_IdUsuario);

 	$.ajax({

 		url:"ajax/graficabalance.ajax.php",
 		method: "POST",
 		data: datos,
 		cache: false,
 		contentType: false,
 		processData: false,
 		dataType: "json",
 		success: function(respuesta){

 			var bar = new Morris.Bar({
		      element: 'bar-chart',
		      resize: true,
		      data: respuesta,
		      barColors: ['#2fa600', '#a6009e'],
		      xkey: 'y',
		      ykeys: ['ventas', 'ganancia'],
		      labels: ['Ventas', 'Ganancias'],
		      preUnits : '$',
		      hideHover: 'auto'
		    }); 			
 		} 
 	})
})


/*=============================================
AGREGANDO PRODUCTOS
=============================================*/

function CargaProducto(){

AgregaProducto();

}


function AgregaProducto(){

	var _Id = $('#Qbarra').val();
	var _IdUsuario = $("#usuario").val(); 
	var _idInventario = $(".idInventario");
	var _SubMarcaAgregada = $(".nuevosubmarca");
	var _cantidadAgregada = $(".CantidadProducto");
	var _PrecioAgregada = $(".nuevoPrecioProducto");
	var _bandera = new Boolean(false);

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

 		    var SubMarca = respuesta["SubMarca"];
          	var stock = respuesta["Stock"];
          	var precio = respuesta["PrecioVenta"];
          	var precioCompra = respuesta["PrecioCompra"];
          	var precioVenta = "Precio Unitario: $" + respuesta["PrecioVenta"];
          	var StockActual = "Stock: " + respuesta["Stock"];
          	var idInventario = respuesta["Id_Inventario"];

          	if(stock == 0){

      			Swal.fire({
				  title : "Sistema PosDit",
			      text: "No hay stock disponible para el producto: " + respuesta["SubMarca"],
			      icon: "error",
			      confirmButtonText: "¡Cerrar!"
			    });

			    $("#Qbarra").val("");

			    return;

          	}

			 //Valida si el producto ya esta en la lista y le agrega 1 cantidad más
			  var i = 0;
			  while( i < _idInventario.length && _bandera == false){

				if($(_SubMarcaAgregada[i]).val() == SubMarca){

					if(parseInt($(_cantidadAgregada[i]).val()) >= parseInt(stock)){

						Swal.fire({
							title : "Sistema PosDit",
							text: "¡La cantidad supera el Stock, Sólo hay "+stock+" unidades!",
							icon: "error",
							confirmButtonText: "¡Cerrar!"
						  });

					}else{

						$(_cantidadAgregada[i]).val(parseInt($(_cantidadAgregada[i]).val()) + 1);
						$(_PrecioAgregada[i]).val(parseInt($(_cantidadAgregada[i]).val()) * precio);
					}
			  
					_bandera = true;
				}

				i++	
			  }

			  if(_bandera == false){

				$(".nuevoProducto").append(

					'<div class="row" style="padding:5px 15px">'+
	  
					'<!-- Descripción del producto -->'+
					
					'<div class="col-xs-6" style="padding-right:0px">'+
					
					  '<div class="input-group">'+
						
						'<span class="input-group-addon"><button type="button" class="btn btn-danger btn-xs quitarProducto" idProducto="'+_Id+'"><i class="fa fa-times"></i></button></span>'+
	  
						'<input type="text" class="form-control nuevosubmarca" idProducto="'+SubMarca+'" name="agregarProducto" value="'+SubMarca+'" readonly required>'+
						'<input type="text" class="form-control precioVenta" precioVenta="'+precioVenta+'" name="precioVenta" value="'+precioVenta+'" readonly required>'+
						'<input type="hidden" class="form-control idInventario " idInventario="'+idInventario+'" name="idInventario" value="'+idInventario+'" readonly required>'+
	  
					  '</div>'+
	  
					'</div>'+
	  
					'<!-- Cantidad del producto -->'+
	  
					'<div class="col-xs-3">'+
					  
					   '<input type="number" class="form-control CantidadProducto" name="CantidadProducto" min="1" value="1" stock="'+stock+'" nuevoStock="'+Number(stock-1)+'" required>'+
					   '<input type="text" class="form-control stockReal" stockReal="'+StockActual+'" name="stockReal" value="'+StockActual+'" readonly required>'+
	  
					'</div>' +
	  
					'<!-- Precio del producto -->'+
	  
					'<div class="col-xs-3 ingresoPrecio" style="padding-left:0px">'+
	  
					  '<div class="input-group">'+
	  
						'<span class="input-group-addon"><i class="ion ion-social-usd"></i></span>'+
						   
						'<input type="text" class="form-control nuevoPrecioProducto" precioReal="'+precio+'" name="nuevoPrecioProducto" value="'+precio+'" readonly required>'+
            '<input type="hidden" class="form-control precioCompra" precioCompra="'+precioCompra+'" name="precioCompra" value="'+precioCompra+'" readonly required>'+
					  '</div>'+
					   
					'</div>'+
          '<span><button type="button" class="btn btn-info btn-xs descuentos" idProducto="'+SubMarca+'"><i class="fa fa-minus-square-o" aria-hidden="true"></i> <i class="fa fa-usd"></i></button></span>'+
          '<span class="descuento-leyenda">Sin Descuento</span>'+
				  '</div>')
			  }

 			}else{
 				Swal.fire({
				    title : "Sistema PosDit",
					text: "¡Producto no encontrado, Favor de comunicarse con Soporte Tecnico para dar de alta el producto!",
					icon: "error",
					confirmButtonText: "¡Cerrar!"
					});
 			}

	        $("#Qbarra").val("");
	        sumarTotalPrecios();
 		} 
 	})

}


/*=============================================
Modificar Cantidad
=============================================*/

$(".formularioVenta").on("change", "input.CantidadProducto", function(){

	var precio = $(this).parent().parent().children(".ingresoPrecio").children().children(".nuevoPrecioProducto");

	var precioFinal = $(this).val() * precio.attr("precioReal");
	
	precio.val(precioFinal);

	var nuevoStock = Number($(this).attr("stock")) - $(this).val();

	$(this).attr("nuevoStock", nuevoStock);

	if(Number($(this).val()) > Number($(this).attr("stock"))){

		$(this).val(1);

		var precioFinal = $(this).val() * precio.attr("precioReal");

		precio.val(precioFinal);

		Swal.fire({
		  title : "Sistema PosDit",
	      text: "¡La cantidad supera el Stock, Sólo hay "+$(this).attr("stock")+" unidades!",
	      icon: "error",
	      confirmButtonText: "¡Cerrar!"
	    });

	    sumarTotalPrecios();
	    mostrarCambio();

	    return;

	}

	sumarTotalPrecios();
	mostrarCambio();

})

/*=============================================
SUMAR TODOS LOS PRECIOS
=============================================*/

function sumarTotalPrecios(){

	var precioItem = $(".nuevoPrecioProducto");
	var arraySumaPrecio = [];  

	for(var i = 0; i < precioItem.length; i++){

		 arraySumaPrecio.push(Number($(precioItem[i]).val()));
		 
	}

	function sumaArrayPrecios(total, numero){

		return total + numero;

	}

	var sumaTotalPrecio = arraySumaPrecio.reduce(sumaArrayPrecios);
	
	$("#nuevoTotalVenta").val(sumaTotalPrecio);
	$("#totalVenta").val(sumaTotalPrecio);
	$("#nuevoTotalVenta").attr("total",sumaTotalPrecio);

}


$('#paga').on('change', function() {

	mostrarCambio();
  
});


/*=============================================
SUMAR TODOS LOS PRECIOS
=============================================*/

function mostrarCambio(){

	var Pagar = $("#paga").val();
	var Precio = $("#nuevoTotalVenta").val();
	
	if(Precio != 0 && Pagar != 0){

	var Precio = $("#nuevoTotalVenta").val();
	var Resultado = Number(Pagar) - Number(Precio);
	
	$("#cambio").val(Resultado);

	}else{

		$("#cambio").val(0);
		$("#paga").val(0);
	}

}


/*=============================================
QUITAR PRODUCTOS DE LA VENTA Y RECUPERAR BOTÓN
=============================================*/

$(".formularioVenta").on("click", "button.quitarProducto", function(){

	$(this).parent().parent().parent().parent().remove();

	if($(".nuevoProducto").children().length == 0){

		$("#nuevoTotalVenta").val(0);
		$("#totalVenta").val(0);
		$("#nuevoTotalVenta").attr("total",0);

	}else{

		// SUMAR TOTAL DE PRECIOS
    	sumarTotalPrecios();

	}

	mostrarCambio();

})

/*=============================================
Boton Descuentos
=============================================*/
$(".formularioVenta").on("click", "button.descuentos", function(){

  let idProducto = $(this).attr("idProducto");
  var _SubMarcaAgregada = $(".nuevosubmarca");
  var precioItem = $(".nuevoPrecioProducto"); 
  var cantidadItem = $(".CantidadProducto");
  var leyendaDescuento = $(this).closest("div").find(".descuento-leyenda");

  Swal.fire({
    title: 'Seleccione un descuento',
    html: `
        <select id="descuento" class="swal2-select">
            <option value=0>Sin Descuento</option>
            <option value=10>10%</option>
            <option value=15>15%</option>
            <option value=20>20%</option>
            <option value=25>25%</option>
        </select>
    `,
    confirmButtonText: 'Aplicar',
    showCancelButton: true,
    preConfirm: () => {
        const descuento = document.getElementById('descuento').value;

        for (var i = 0; i < precioItem.length; i++) {

          let id = $(_SubMarcaAgregada[i]).val();
          let precioReal = parseFloat($(precioItem[i]).attr("precioReal"));
          let cantidad = parseFloat($(cantidadItem[i]).val());
          let precio = precioReal * cantidad;
          
          if(idProducto == id){

            if (descuento !== 0) {
              let descuentoCalculado = precio * (descuento / 100);
              precioConDescuento = precio - descuentoCalculado;
            }
            
            $(precioItem[i]).val(precioConDescuento.toFixed(2));
          }
      }

        let textoDescuento = descuento === "0" ? "Sin Descuento" : `${descuento}% Descuento`;
        leyendaDescuento.text(textoDescuento);

        Swal.fire(`Seleccionaste un ${descuento}% de descuento`);
        sumarTotalPrecios();
	      mostrarCambio();        
    }
  });

})

/*=============================================
Guardar Venta
=============================================*/

$(".formularioVenta").on("click", "button.btnGuardarVenta", function(){

var Pago = $("#paga").val()	


if(Pago == '' || Pago == 0){


	Swal.fire({
		icon: "error",
		title : "Sistema PosDit",
		text: "¡Favor de indicar monto a pagar!",
		showConfirmButton: true,
		confirmButtonText: "Cerrar"
		})

}else{

InsertarProductos();

}

})

/*=============================================
Generar Reporte de Cierre
=============================================*/

$(".formularioVenta").on("click", "button.btnGeneraCierre", function () {
  var IdUsuario = $("#usuario").val();
  let telefono = $("#telefono").val();
  var telefono1 = telefono.replace(/[^\d]/g, '');
  var telefonoConLada = "52" + telefono1;
  var Nombre = $("#Nuser").val();


  const today = new Date();
  const formattedDate = today.toISOString().split('T')[0];
  const filename = `ReporteCierreDia_${Nombre}_${formattedDate}.xlsx`;

  Swal.fire({
      title: "Sistema PosDit",
      html: `
          <label for="montoCierre">Monto Venta del día:</label>
          <input id="montoCierre" type="number" step="0.01" class="swal2-input" placeholder="Ej. 1458.00">
          <label for="montoCaja">Fondo de caja:</label>
          <input id="montoCaja" type="number" step="0.01" class="swal2-input" placeholder="Ej. 1200.00">
      `,
      showCancelButton: true,
      confirmButtonText: "Enviar Reporte Cierre",
      cancelButtonText: "Cancelar",
      preConfirm: () => {
          const montoCierre = document.getElementById("montoCierre").value;
          const montoCaja = document.getElementById("montoCaja").value;

          if (!montoCierre || !montoCaja) {
              Swal.showValidationMessage("Por favor, completa ambos campos.");
              return false;
          }

          return { montoCierre: parseFloat(montoCierre), montoCaja: parseFloat(montoCaja) };
      }
  }).then((result) => {
      if (result.isConfirmed) {
          const { montoCierre, montoCaja } = result.value;

          // Formatear los montos
          const formatter = new Intl.NumberFormat('es-MX', {
              style: 'decimal',
              minimumFractionDigits: 2,
              maximumFractionDigits: 2
          });

          const montoCierreFormateado = formatter.format(montoCierre);
          const montoCajaFormateado = formatter.format(montoCaja);

          Swal.fire({
              title: "Confirmación",
              html: `
                  ¿Desea generar el corte con los siguientes montos?<br><br>
                  <strong>Venta Día:</strong> $${montoCierreFormateado}<br>
                  <strong>Fondo Caja:</strong> $${montoCajaFormateado}`,
              icon: "question",
              showCancelButton: true,
              confirmButtonText: "Sí, generar",
              cancelButtonText: "No, cancelar"
          }).then((confirmation) => {
              if (confirmation.isConfirmed) {
                $.ajax({
                  url: "extensions/tcpdf/Reportes/ReporteCierreVenta.php",
                  method: "GET",
                  data: { Cu: IdUsuario, mCierre: montoCierre, mCaja: montoCaja },
                  success: function(response) {
                      try {
                          // Suponemos que la respuesta del servidor es un JSON que incluye la ruta del archivo
                          var data = JSON.parse(response);
                          const filePath = data.filePath;  // Obtener la ruta del archivo generado
                          
                          if (filePath) {
                              // Enviar el archivo a través de la API de WhatsApp
                              $.ajax({
                                  url: "https://apiwsp.factiliza.com/api/v1/message/sendMedia/NTI1NTI1MzI3MzA0",
                                  method: "POST",
                                  contentType: "application/json",
                                  headers: {
                                      'Authorization': 'Bearer ' + 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiI2MjciLCJuYW1lIjoiSnVhbiBEYXZpZCBBZ3VpbGFyIEJhcnJvbiAiLCJlbWFpbCI6ImFndWlsYXJiYXJyb25qdWFuZGF2aWRAZ21haWwuY29tIiwiaHR0cDovL3NjaGVtYXMubWljcm9zb2Z0LmNvbS93cy8yMDA4LzA2L2lkZW50aXR5L2NsYWltcy9yb2xlIjoiY29uc3VsdG9yIn0.r5cvSNgCntPbf4OCjqx1JlS885CxHSN7FyxCLlVBAus'  // Aquí va tu token de autenticación
                                  },
                                  data: JSON.stringify({
                                      number: telefonoConLada,  // Número de destino (con código de país)
                                      mediatype: "document",  // Tipo de medio (documento)
                                      media: filePath,  // Ruta del archivo en el servidor
                                      filename: filename,  // Nombre del archivo que se enviará
                                      caption: `Venta Reportada: $${montoCierre}, Caja: $${montoCaja}, Para más detalles favor de consultar el archivo adjunto.`
                                  }),
                                  success: function() {
                                      Swal.fire({
                                          icon: "success",
                                          title: "Reporte enviado",
                                          text: "El reporte se envió exitosamente por WhatsApp.",
                                          confirmButtonText: "Aceptar"
                                      });
                                  },
                                  error: function() {
                                      Swal.fire({
                                          icon: "error",
                                          title: "Error al enviar",
                                          text: "No se pudo enviar el reporte por WhatsApp. Por favor, intenta nuevamente.",
                                          confirmButtonText: "Aceptar"
                                      });
                                  }
                              });
                          } else {
                              Swal.fire({
                                  icon: "error",
                                  title: "Error",
                                  text: "No se pudo obtener la ruta del archivo generado. Intenta nuevamente.",
                                  confirmButtonText: "Aceptar"
                              });
                          }
                      } catch (error) {
                          Swal.fire({
                              icon: "error",
                              title: "Error",
                              text: "Hubo un problema al procesar la respuesta del servidor. Intenta nuevamente.",
                              confirmButtonText: "Aceptar"
                          });
                      }
                  },
                  error: function(error) {
                      Swal.fire({
                          icon: "error",
                          title: "Error",
                          text: "Hubo un problema al generar el reporte. Intenta nuevamente.",
                          confirmButtonText: "Aceptar"
                      });
                  }
              });
              
              } else {
                  Swal.fire({
                      icon: "info",
                      title: "Operación cancelada",
                      text: "No se generó el corte.",
                      confirmButtonText: "Aceptar"
                  });
              }
          });
      }
  });
});




/*=============================================
INSERTAR TODOS LOS PRODUCTOS
=============================================*/

function InsertarProductos(){

	var descripcion = $(".nuevosubmarca");

	var idInventario = $(".idInventario");

	var cantidad = $(".CantidadProducto");

  var cliente = $("#cliente").val();

  console.log("valor", cliente);

	var NTicket;

	var IdUsuario = $("#usuario").val();

	var pago = $("#paga").val();
	var cambio = $("#cambio").val();
	var venta = $("#totalVenta").val();

  //Datos Promoción
  var precioItem = $(".nuevoPrecioProducto");
  var leyendaDescuento = $(".descuento-leyenda"); // Leyenda del descuento aplicado


	var datosN = new FormData();
	datosN.append("idUsuario",IdUsuario);

	$.ajax({

	 		url:"ajax/obtenerTicket.ajax.php",
	 		method: "POST",
	 		data: datosN,
	 		cache: false,
	 		contentType: false,
	 		processData: false,
	 		dataType: "json",
	 		success: function(respuesta){
	 			
	 			for(var i = 0; i < descripcion.length; i++){

	 			    NTicket = respuesta["NuevoTicket"];

	 				var datos = new FormData();
	 			    datos.append("id",$(idInventario[i]).attr("idInventario"));
	 			    datos.append("cantidad",$(cantidad[i]).val());
	 			    datos.append("nTicket",NTicket);
	 			    datos.append("idUsuario",IdUsuario);
            datos.append("precioFinal",$(precioItem[i]).val());
            datos.append("cliente",cliente);

	 				$.ajax({
				 		url:"ajax/crearventa.ajax.php",
				 		method: "POST",
				 		data: datos,
				 		cache: false,
				 		contentType: false,
				 		processData: false,
				 		dataType: "json",
				 		success: function(respuesta){

				 			if(respuesta == 'ok'){

				 				Swal.fire({
									  icon: "success",
									  title : "Sistema PosDit",
									  html: `<h1>Venta realizada</h1>
									  <p>Monto de Compra: $<strong>`+ venta+ `</strong></p>
									  <p>Monto de Pago: $<strong>`+ pago+ `</strong></p>
									  <p>Monto de Cambio: $<strong>`+ cambio+ `</strong></p>
									  <p>Venta guardada con el Ticket <strong>`+ NTicket+ `</strong></p>
									    `,
									  showConfirmButton: true,
									  confirmButtonText: "Cerrar"
									  }).then(function(result){
												if (result.value) {
													Swal.fire({
														title: '¿Deseas Imprimir el Ticket?',
														showDenyButton: true,
														confirmButtonText: 'SI',
													  }).then((result) => {
														if (result.isConfirmed) {
														     Swal.fire({
                                                                title: 'Por favor, ingresa tu número de teléfono:',
                                                                input: 'number',
                                                                inputAttributes: {
                                                                    autocapitalize: 'off'
                                                                },
                                                                showCancelButton: false,
                                                                confirmButtonText: 'Enviar',
                                                                showLoaderOnConfirm: true,
                                                                preConfirm: (telefono) => {
                                                                    // Aquí podrías realizar validaciones adicionales si es necesario
                                                                    // Por ejemplo, validar que el número de teléfono tenga el formato correcto
                                                                    return telefono;
                                                                },
                                                                allowOutsideClick: () => !Swal.isLoading()
                                                            }).then((result) => {
                                                                if (result.isConfirmed) {
                                                                    const telefono = result.value;
                                                                    
                                                                    // Hacer una solicitud AJAX para procesar Ticket.php
                                                                    $.ajax({
                                                                        url: "extensions/tcpdf/Reportes/Ticket.php",
                                                                        method: "GET",
                                                                        data: {
                                                                            Cu: IdUsuario,
                                                                            NTicket: NTicket,
                                                                            Pago: pago,
                                                                            Telefono: telefono
                                                                        },
                                                                        success: function(response) {
                                                                            // Opcional: Manejar la respuesta del servidor aquí
                                                                            console.log("Ticket procesado exitosamente.");
                                                                            window.location = "crearventas"; // Redirigir a "crearventas" después de procesar el ticket
                                                                        },
                                                                        error: function(xhr, status, error) {
                                                                            // Manejar errores aquí
                                                                            console.error("Error al procesar el ticket:", error);
                                                                        }
                                                                    });
                                                                }
                                                            });
                                                            
															//window.open("extensions/tcpdf/Reportes/Ticket.php?Cu="+ IdUsuario +"&NTicket="+NTicket+"&Pago="+pago,"_blank");													
															//ImprimirTicket(NTicket,cambio,pago);
															//window.location = "crearventas";
															
														}else{
															window.location = "crearventas";
														}
													  })
												}
									   })	

				 			}else{

				 				Swal.fire({
									  icon: "error",
									  title : "Sistema PosDit",
									  text: "¡Hubo un error en la venta, favor de validar con Soporte Tecnico!",
									  showConfirmButton: true,
									  confirmButtonText: "Cerrar"
									  }).then(function(result){
												if (result.value) {

												window.location = "crearventas";

											}
										})
				 			}
				 		} 

				 	})
	 				
				}

	 		} 
	 	})

}

/*=============================================
RANGO DE FECHAS
=============================================*/

$('#daterange-btn').daterangepicker(
  {
    ranges   : {
      'Hoy'       : [moment(), moment()],
      'Ayer'   : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
      'Últimos 7 días' : [moment().subtract(6, 'days'), moment()],
      'Últimos 30 días': [moment().subtract(29, 'days'), moment()],
      'Este mes'  : [moment().startOf('month'), moment().endOf('month')],
      'Último mes'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
    },
    startDate: moment().subtract(29,'days'),
    endDate  : moment()
  },
  function (start, end) {
    $('#daterange-btn span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));

    $("#FInicio").val(start.format('YYYY-MM-DD'));
    $("#Ffin").val(end.format('YYYY-MM-DD'));

  }

)

function ObtenerReporteVentas(){

    var _IdUsuario = $("#usuario").val();
    
    // Obtenemos el texto del botón (ej. "2026-04-15 - 2026-04-15")
    var textoFechas = $("#daterange-btn span").text().trim();
    
    var FechaInicio = '';
    var FFin = '';

    // Si el texto es diferente al texto por defecto, separamos las fechas
    if(textoFechas !== "Rango de Fechas" && textoFechas !== ""){
        var fechas = textoFechas.split(' - ');
        FechaInicio = fechas[0];
        FFin = fechas[1];
    }

    if (FechaInicio === '') {
        Swal.fire({
            icon: "error",
            title : "Sistema PosDit",
            text: "¡Favor de seleccionar un rango de fechas!",
            showConfirmButton: true,
            confirmButtonText: "Cerrar"
        });
    } else {
        window.open("extensions/tcpdf/Reportes/ReporteVentas.php?Cu="+_IdUsuario+"&fi="+FechaInicio+"&ff="+FFin, "_blank");
    }
}

function ImprimirTicket(_Ticket,_Cambio,_Pago ){

var _idNegocio = $("#usuario").val();;
var Resultado = _Pago - _Cambio;

var datos = new FormData();
datos.append("idNegocio", _Ticket);

let nombreImpresora = "POS58 10.0.0.6";
const conector = new ConectorPlugin();

$.ajax({

 		url:"ajax/obtenerNegocioUsuario.ajax.php",
 		method: "POST",
 		data: datos,
 		cache: false,
 		contentType: false,
 		processData: false,
 		dataType: "json",
 		success: function(data){
 		    
			conector.establecerTamanioFuente(1, 1);
			conector.establecerEnfatizado(0);
			conector.establecerJustificacion(ConectorPlugin.Constantes.AlineacionCentro);
			conector.imagenDesdeUrl("https://www.sistema-posdit.posdit.com.mx/Reportes.png");
			conector.feed(1);
            conector.texto(data[0][8] + "\n");
			conector.texto(data[0][7] + "\n");
			conector.texto(data[0][6] + "\n");
			conector.texto(data[0][5] + "\n");
			conector.texto(data[0][9] + "\n");
			conector.texto(data[0][10] + "\n");
			conector.texto("N° Ticket: ");
			conector.texto(_Ticket + "\n");
			conector.texto("--------------------------------\n");
			
			for(var i = 0; i < data.length; i++){
			    conector.establecerJustificacion(ConectorPlugin.Constantes.AlineacionIzquierda);
			    conector.texto("ARTICULO: ");    
			    conector.texto(data[i][1] + "\n");
			    conector.texto("PRE. UNIT: $");    
			    conector.texto(data[i][2] + "\n");
			    conector.texto("CANTIDAD: ");    
			    conector.texto(data[i][0] + "\n");
			    conector.texto("SUBTOTAL: $");    
			    conector.texto(data[i][3] + "\n");
	        }
			
			
            conector.establecerJustificacion(ConectorPlugin.Constantes.AlineacionDerecha);
			conector.texto("--------------------------------\n");
			conector.texto("TOTAL: $" + Resultado + " \n");
			conector.texto("EFECTIVO: $" + _Pago + "\n");
			conector.texto("CAMBIO: $" + _Cambio + "\n");
			conector.texto("--------------------------------\n");
			conector.establecerJustificacion(ConectorPlugin.Constantes.AlineacionCentro);
			conector.texto("***Gracias por su compra***");
			conector.feed(4);
			conector.cortar();
			conector.cortarParcialmente();
			conector.imprimirEn(nombreImpresora);

            
 		} 

 	});


}


/*=============================================
RECARGAS
=============================================*/


$(document).ready(function () {
  $("#cbxnombre").change(function () {
    $("#cbxdescripcion")
      .find("option")
      .remove()
      .end()
      .append('<option value="whatever"></option>')
      .val("whatever");
    $("#cbxmonto")
      .find("option")
      .remove()
      .end()
      .append('<option value="whatever"></option>')
      .val("whatever");

    $("#cbxnombre option:selected").each(function () {
      var operacion = document.getElementById("activo").innerText;
      var codigo = "Productos";
      var cbx = "cbxnombre";
      var nombre = $("#cbxnombre").val();
      var datos = new FormData();
      datos.append("nombre", nombre);
      datos.append("operacion", operacion);
      datos.append("codigo", codigo);
      datos.append("cbx", cbx);
      
      $.ajax({
        url: "ajax/webservice.ajax.php",
        method: "POST",
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        dataType: "text",
        success: function (respuesta) {
          $("#cbxcodigo").html(respuesta);
        },
      });

    });
  });
});

$(document).ready(function () {
  $("#cbxcodigo").change(function () {
    $("#cbxcodigo option:selected").each(function () {
      var operacion = document.getElementById("activo").innerText;
      var codigo = $("#cbxcodigo").val();
      var cbx = "cbxdescripcion";
      var nombre = $("#cbxnombre").val();
      var datos = new FormData();
      datos.append("nombre", nombre);
      datos.append("operacion", operacion);
      datos.append("codigo", codigo);
      datos.append("cbx", cbx);

      $.ajax({
        url: "ajax/webservice.ajax.php",
        method: "POST",
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        dataType: "text",
        success: function (respuesta) {
          $("#cbxdescripcion").html(respuesta);
        },
      });

      var operacion2 = document.getElementById("activo").innerText;
      var codigo2 = $("#cbxcodigo").val();
      var cbx2 = "cbxmonto";
      var nombre2 = $("#cbxnombre").val();
      var datos2 = new FormData();
      datos2.append("nombre", nombre2);
      datos2.append("operacion", operacion2);
      datos2.append("codigo", codigo2);
      datos2.append("cbx", cbx2);
      $.ajax({
        url: "ajax/webservice.ajax.php",
        method: "POST",
        data: datos2,
        cache: false,
        contentType: false,
        processData: false,
        dataType: "text",
        success: function (respuesta2) {
          $("#cbxmonto").html(respuesta2);
        },
      });
    });
  });
});

$(document).ready(function () {
  $("#cbxnombre_serv").change(function () {
    $("#cbxdescripcion_serv")
      .find("option")
      .remove()
      .end()
      .append('<option value="whatever"></option>')
      .val("whatever");
    $("#cbxmonto_serv")
      .find("option")
      .remove()
      .end()
      .append('<option value="whatever"></option>')
      .val("whatever");

    $("#cbxnombre_serv option:selected").each(function () {
      var operacion = document.getElementById("activo").innerText;
      var codigo = "Servicios";
      var cbx = "cbxnombre";
      var nombre = $("#cbxnombre_serv").val();
      var datos = new FormData();
      datos.append("nombre", nombre);
      datos.append("operacion", operacion);
      datos.append("codigo", codigo);
      datos.append("cbx", cbx);

      $.ajax({
        url: "ajax/webservice.ajax.php",
        method: "POST",
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        dataType: "text",
        success: function (respuesta) {
          $("#cbxcodigo_serv").html(respuesta);
        },
      });
    });
  });
});

$(document).ready(function () {
  $("#cbxcodigo_serv").change(function () {
    $("#cbxcodigo_serv option:selected").each(function () {
      var operacion = document.getElementById("activo").innerText;
      var codigo = $("#cbxcodigo_serv").val();
      var cbx = "cbxdescripcion";
      var nombre = $("#cbxnombre_serv").val();
      var datos = new FormData();
      datos.append("nombre", nombre);
      datos.append("operacion", operacion);
      datos.append("codigo", codigo);
      datos.append("cbx", cbx);

      $.ajax({
        url: "ajax/webservice.ajax.php",
        method: "POST",
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        dataType: "text",
        success: function (respuesta) {
          $("#cbxdescripcion_serv").html(respuesta);
        },
      });

      var operacion2 = document.getElementById("activo").innerText;
      var codigo2 = $("#cbxcodigo_serv").val();
      var cbx2 = "cbxmonto";
      var nombre2 = $("#cbxnombre_serv").val();
      var datos2 = new FormData();
      datos2.append("nombre", nombre2);
      datos2.append("operacion", operacion2);
      datos2.append("codigo", codigo2);
      datos2.append("cbx", cbx2);
      $.ajax({
        url: "ajax/webservice.ajax.php",
        method: "POST",
        data: datos2,
        cache: false,
        contentType: false,
        processData: false,
        dataType: "text",
        success: function (respuesta2) {
          $("#cbxmonto_serv").html(respuesta2);
        },
      });
    });
  });
});

$(document).ready(function () {
  $("#cbxnombre_pin").change(function () {
    $("#cbxdescripcion_pin")
      .find("option")
      .remove()
      .end()
      .append('<option value="whatever"></option>')
      .val("whatever");
    $("#cbxmonto_pin")
      .find("option")
      .remove()
      .end()
      .append('<option value="whatever"></option>')
      .val("whatever");

    $("#cbxnombre_pin option:selected").each(function () {
      var operacion = document.getElementById("activo").innerText;
      var codigo = "Servicios";
      var cbx = "cbxnombre";
      var nombre = $("#cbxnombre_pin").val();
      var datos = new FormData();
      datos.append("nombre", nombre);
      datos.append("operacion", operacion);
      datos.append("codigo", codigo);
      datos.append("cbx", cbx);

      $.ajax({
        url: "ajax/webservice.ajax.php",
        method: "POST",
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        dataType: "text",
        success: function (respuesta) {
          $("#cbxcodigo_pin").html(respuesta);
        },
      });
    });
  });
});

$(document).ready(function () {
  $("#cbxcodigo_pin").change(function () {
    $("#cbxcodigo_pin option:selected").each(function () {
      var operacion = document.getElementById("activo").innerText;
      var codigo = $("#cbxcodigo_pin").val();
      var cbx = "cbxdescripcion";
      var nombre = $("#cbxnombre_pin").val();
      var datos = new FormData();
      datos.append("nombre", nombre);
      datos.append("operacion", operacion);
      datos.append("codigo", codigo);
      datos.append("cbx", cbx);

      $.ajax({
        url: "ajax/webservice.ajax.php",
        method: "POST",
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        dataType: "text",
        success: function (respuesta) {
          $("#cbxdescripcion_pin").html(respuesta);
        },
      });

      var operacion2 = document.getElementById("activo").innerText;
      var codigo2 = $("#cbxcodigo_pin").val();
      var cbx2 = "cbxmonto";
      var nombre2 = $("#cbxnombre_pin").val();
      var datos2 = new FormData();
      datos2.append("nombre", nombre2);
      datos2.append("operacion", operacion2);
      datos2.append("codigo", codigo2);
      datos2.append("cbx", cbx2);
      $.ajax({
        url: "ajax/webservice.ajax.php",
        method: "POST",
        data: datos2,
        cache: false,
        contentType: false,
        processData: false,
        dataType: "text",
        success: function (respuesta2) {
          $("#cbxmonto_pin").html(respuesta2);
        },
      });
    });
  });
});

$(document).ready(function () {
  $('a[data-toggle="tab"]').on("shown.bs.tab", function (e) {
    var activeTab = $(e.target).text(); // Get the name of active tab
    var previousTab = $(e.relatedTarget).text(); // Get the name of previous active tab

    $(".active-tab span").html(activeTab);
    $(".previous-tab span").html(previousTab);
  });
});

function reseRecarProd(e) {
  e.preventDefault();
  
  var numero = document.getElementById("numero").value;
  var numero2 = document.getElementById("numero2").value;
  var producto = document.getElementById("cbxcodigo").value;
  var monto = document.getElementById("cbxmonto").value;
  var tiempo = document.getElementById("tiempo").value; // Obtener el valor de tiempo

  var datos = new FormData();
  datos.append("numero", numero);
  datos.append("producto", producto);
  datos.append("monto", monto);

  if (numero == "" || producto == "" || monto == "") {
    Swal.fire({
      icon: "error",
      title: "Sistema PosDit",
      text: "¡Todos los campos son requeridos!",
      showConfirmButton: true,
      confirmButtonText: "Cerrar"
    });
  } else {
    if (parseFloat(monto) > parseFloat(tiempo)) {
      Swal.fire({
        icon: "error",
        title: "Sistema PosDit",
        text: "¡No cuentas con saldo suficiente para hacer la recarga, Favor de contactar a Soporte!",
        showConfirmButton: true,
        confirmButtonText: "Cerrar"
      });
    } else {
      if (numero == numero2) {
        document.getElementById("row_request").classList.remove("hidden");
        document.getElementById("row_request").classList.add("visible");
        $.ajax({
          url: "ajax/webservice.ajax.php",
          method: "POST",
          data: datos,
          cache: false,
          contentType: false,
          processData: false,
          dataType: "text",
          success: function (respuesta) {
            document.getElementById("requestid").value = respuesta;
          },
        });
      } else {
        $("#numero").val("");
        $("#numero2").val("");

        Swal.fire({
          icon: "error",
          title: "Sistema PosDit",
          text: "¡Los Números No Coinciden!",
          showConfirmButton: true,
          confirmButtonText: "Cerrar"
        });
      }
    }
  }
}

function proceRecarProd(e) {
  e.preventDefault();

  var requestid = document.getElementById("requestid").value;
  var datos = new FormData();
  datos.append("requestid", requestid);

  if (requestid === "") {
    Swal.fire({
      icon: "error",
      title: "Sistema PosDit",
      text: "¡Favor de reservar la recarga!",
      showConfirmButton: true,
      confirmButtonText: "Cerrar"
    });
  } else {
    document.getElementById("row_request").classList.remove("visible");
    document.getElementById("row_request").classList.add("hidden");
    document.getElementById("row_res_1").classList.remove("hidden");
    document.getElementById("row_res_1").classList.add("visible");
    document.getElementById("row_res_2").classList.remove("hidden");
    document.getElementById("row_res_2").classList.add("visible");
    document.getElementById("row_res_3").classList.remove("hidden");
    document.getElementById("row_res_3").classList.add("visible");

    $.ajax({
      url: "ajax/webservice.ajax.php",
      method: "POST",
      data: datos,
      cache: false,
      contentType: false,
      processData: false,
      dataType: "json",
      success: function (respuesta) {
        document.getElementById("res_requestid").value = respuesta["requestid"];
        document.getElementById("res_fecha").value = respuesta["fecha"];
        document.getElementById("res_referencia").value = respuesta["referencia"];
        document.getElementById("res_folio").value = respuesta["folio"];
        document.getElementById("res_monto").value = respuesta["monto"];
        document.getElementById("res_cargo").value = respuesta["cargo"];
        document.getElementById("res_abono").value = respuesta["abono"];
        document.getElementById("res_producto").value = respuesta["producto"];
        document.getElementById("res_saldofinal").value = respuesta["saldofinal"];

        // Llamar a la función para insertar la recarga en la base de datos
        insertarRecarga(respuesta);
      },
      error: function (xhr, status, error) {
        Swal.fire({
          icon: "error",
          title: "Error",
          text: "Hubo un error al procesar la recarga.",
          showConfirmButton: true,
          confirmButtonText: "Cerrar"
        });
      }
    });
  }
}

function insertarRecarga(respuesta) {
    
  var numero = document.getElementById("numero").value;
  
  var datos = new FormData();
  datos.append("NTicket", respuesta["requestid"]);
  datos.append("Id_Producto", "0000000000"); // Ajusta según cómo se obtiene la ID del producto
  datos.append("Cantidad", 1); // Ajusta según cómo se obtiene la cantidad
  datos.append("PrecioCompra", 0); // Ajusta según cómo se obtiene el precio de compra
  datos.append("PrecioVenta", respuesta["monto"]); // Ajusta según cómo se obtiene el precio de venta
  datos.append("Id_Usuario", $("#usuario").val()); // Usuario actual
  datos.append("Fecha_Compra", respuesta["fecha"]);
  datos.append("id_Nombre", respuesta["id_nombre"]);
  datos.append("id_Codigo", respuesta["producto"]);
  datos.append("Descripcion", respuesta["descripcion"]);
  datos.append("Numero", numero);
  datos.append("Folio", respuesta["folio"]);

  $.ajax({
    url: "ajax/insertarRecarga.ajax.php",
    method: "POST",
    data: datos,
    cache: false,
    contentType: false,
    processData: false,
    dataType: "json",
    success: function (respuesta) {
      if (respuesta === "ok") {
        Swal.fire({
          icon: "success",
          title: "Recarga realizada",
          text: "La recarga se realizó exitosamente.",
          showConfirmButton: true,
          confirmButtonText: "Cerrar"
        });
      } else {
        Swal.fire({
          icon: "error",
          title: "Error",
          text: "Hubo un problema al realizar la recarga.",
          showConfirmButton: true,
          confirmButtonText: "Cerrar"
        });
      }
    },
    error: function (xhr, status, error) {
      Swal.fire({
        icon: "error",
        title: "Error",
        text: "Hubo un error al procesar la recarga.",
        showConfirmButton: true,
        confirmButtonText: "Cerrar"
      });
    }
  });
}

function reseRecarPin(e) {
  e.preventDefault();

  var numero = document.getElementById("numero_pin").value;
  var producto = document.getElementById("cbxcodigo_pin").value;
  var monto = document.getElementById("cbxmonto_pin").value;
  var datos = new FormData();
  datos.append("numero_pin", numero);
  datos.append("producto_pin", producto);
  datos.append("monto_pin", monto);
  if (numero == "" || producto == "" || monto == "") {
        Swal.fire({
		icon: "error",
		title : "Sistema PosDit",
		text: "¡Todos los campos son requeridos!",
		showConfirmButton: true,
		confirmButtonText: "Cerrar"
		})
  } else {
    document.getElementById("row_request_pin").classList.remove("hidden");
    document.getElementById("row_request_pin").classList.add("visible");
    $.ajax({
      url: "ajax/webservice.ajax.php",
      method: "POST",
      data: datos,
      cache: false,
      contentType: false,
      processData: false,
      dataType: "text",
      success: function (respuesta) {
        document.getElementById("requestid_pin").value = respuesta;
      },
    });
  }
}
function proceRecarPin(e) {
  e.preventDefault();

  var requestid = document.getElementById("requestid_pin").value;
  var datos = new FormData();
  datos.append("requestid_pin", requestid);

  if (requestid == "") {
    alert("El requestid es obligatorio");
  } else {
    document.getElementById("row_request_pin").classList.remove("visible");
    document.getElementById("row_request_pin").classList.add("hidden");
    document.getElementById("row_res_1_pin").classList.remove("hidden");
    document.getElementById("row_res_1_pin").classList.add("visible");
    document.getElementById("row_res_2_pin").classList.remove("hidden");
    document.getElementById("row_res_2_pin").classList.add("visible");
    document.getElementById("row_res_3_pin").classList.remove("hidden");
    document.getElementById("row_res_3_pin").classList.add("visible");
    $.ajax({
      url: "ajax/webservice.ajax.php",
      method: "POST",
      data: datos,
      cache: false,
      contentType: false,
      processData: false,
      dataType: "json",
      success: function (respuesta) {
        document.getElementById("res_requestid_pin").value =
          respuesta["requestid"];
        document.getElementById("res_fecha_pin").value = respuesta["fecha"];
        document.getElementById("res_referencia_pin").value =
          respuesta["referencia"];
        document.getElementById("res_folio_pin").value = respuesta["folio"];
        document.getElementById("res_monto_pin").value = respuesta["monto"];
        document.getElementById("res_cargo_pin").value = respuesta["cargo"];
        document.getElementById("res_abono_pin").value = respuesta["abono"];
        document.getElementById("res_producto_pin").value =
          respuesta["producto"];
        document.getElementById("res_saldofinal_pin").value =
          respuesta["saldofinal"];
      },
    });
  }
}

function reseRecarServ(e) {
  e.preventDefault();

  var referencia = document.getElementById("referencia_serv").value;
  var producto = document.getElementById("cbxcodigo_serv").value;
  var monto = document.getElementById("monto_serv").value;
  var datos = new FormData();
  datos.append("referencia_serv", referencia);
  datos.append("producto_serv", producto);
  datos.append("monto_serv", monto);
  if (referencia == "" || producto == "" || monto == "") {
        Swal.fire({
		icon: "error",
		title : "Sistema PosDit",
		text: "¡Todos los campos son requeridos!",
		showConfirmButton: true,
		confirmButtonText: "Cerrar"
		})
  } else {
    document.getElementById("row_request_serv").classList.remove("hidden");
    document.getElementById("row_request_serv").classList.add("visible");
    $.ajax({
      url: "ajax/webservice.ajax.php",
      method: "POST",
      data: datos,
      cache: false,
      contentType: false,
      processData: false,
      dataType: "text",
      success: function (respuesta) {
        document.getElementById("requestid_serv").value = respuesta;
      },
    });
  }
}
function proceRecarServ(e) {
  e.preventDefault();

  var requestid_serv = document.getElementById("requestid_serv").value;
  var datos = new FormData();
  datos.append("requestid_serv", requestid_serv);

  if (requestid_serv == "") {
    alert("El requestid es obligatorio");
  } else {
    document.getElementById("row_request_serv").classList.remove("visible");
    document.getElementById("row_request_serv").classList.add("hidden");
    document.getElementById("row_res_1_serv").classList.remove("hidden");
    document.getElementById("row_res_1_serv").classList.add("visible");
    document.getElementById("row_res_2_serv").classList.remove("hidden");
    document.getElementById("row_res_2_serv").classList.add("visible");
    document.getElementById("row_res_3_serv").classList.remove("hidden");
    document.getElementById("row_res_3_serv").classList.add("visible");
    $.ajax({
      url: "ajax/webservice.ajax.php",
      method: "POST",
      data: datos,
      cache: false,
      contentType: false,
      processData: false,
      dataType: "json",
      success: function (respuesta) {
        document.getElementById("res_requestid_serv").value =
          respuesta["requestid"];
        document.getElementById("res_fecha_serv").value = respuesta["fecha"];
        document.getElementById("res_referencia_serv").value =
          respuesta["referencia"];
        document.getElementById("res_folio_serv").value = respuesta["folio"];
        document.getElementById("res_monto_serv").value = respuesta["monto"];
        document.getElementById("res_cargo_serv").value = respuesta["cargo"];
        document.getElementById("res_abono_serv").value = respuesta["abono"];
        document.getElementById("res_producto_serv").value =
          respuesta["producto"];
        document.getElementById("res_saldofinal_serv").value =
          respuesta["saldofinal"];
      },
    });
  }
}

function verificarRecarga(e) {
	e.preventDefault();
  
	var requestid_ver = document.getElementById("requestid_ver").value;
	var datos = new FormData();
	datos.append("requestid_ver", requestid_ver);
  
	if (requestid_ver == "") {
	  alert("El requestid es obligatorio");
	} else {
	  document.getElementById("row_res_1_ver").classList.remove("hidden");
	  document.getElementById("row_res_1_ver").classList.add("visible");
	  document.getElementById("row_res_2_ver").classList.remove("hidden");
	  document.getElementById("row_res_2_ver").classList.add("visible");
	  document.getElementById("row_res_3_ver").classList.remove("hidden");
	  document.getElementById("row_res_3_ver").classList.add("visible");
	  $.ajax({
		url: "ajax/webservice.ajax.php",
		method: "POST",
		data: datos,
		cache: false,
		contentType: false,
		processData: false,
		dataType: "json",
		success: function (respuesta) {
		  document.getElementById("res_requestid_ver").value =
			respuesta["requestid"];
		  document.getElementById("res_fecha_ver").value = respuesta["fecha"];
		  document.getElementById("res_referencia_ver").value =
			respuesta["referencia"];
		  document.getElementById("res_folio_ver").value = respuesta["folio"];
		  document.getElementById("res_monto_ver").value = respuesta["monto"];
		  document.getElementById("res_cargo_ver").value = respuesta["cargo"];
		  document.getElementById("res_abono_ver").value = respuesta["abono"];
		  document.getElementById("res_producto_ver").value =
			respuesta["producto"];
		  document.getElementById("res_saldofinal_ver").value =
			respuesta["saldofinal"];
		},
	  });
	}
  }


