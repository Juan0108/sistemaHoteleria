<style>
  /* Ocultar el contenido de las pesta���as deshabilitadas */
  .disabled-tab-content {
    display: none;
  }

  /* Deshabilitar la apariencia de las pesta���as deshabilitadas */
  .disabled-tab a {
    pointer-events: none;
    cursor: not-allowed;
    color: #ccc; /* Opcional: color de texto deshabilitado */
  }
</style>

<div class="content-wrapper">

  <section class="content-header">
    
    <h1>
      
      Crear venta
    
    </h1>

    <ol class="breadcrumb">
      
      <li><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
      
      <li class="active">Crear venta</li>
    
    </ol>

  </section>

  <section class="content">

    <div class="row">
      
      <div class="col-lg-5 col-xs-12">
        
        <div class="box box-success">
          
          <div class="box-header with-border"></div>

          <form role="form" method="post" class="formularioVenta">

            <div class="box-body">
  
              <div class="box">
                
                <div class="form-group">
                
                  <div class="input-group">
                    
                    <span class="input-group-addon"><i class="fa fa-shopping-cart"></i></span> 

                    <input type="text" class="form-control" value="<?php echo $_SESSION["RazonSocial"]; ?>" readonly>

                  </div>

                </div>
            
                <div class="form-group">
                
                  <div class="input-group">
                    
                    <span class="input-group-addon"><i class="fa fa-users"></i></span> 

                    <input type="text" class="form-control" id="Perfil" value="<?php echo $_SESSION["Perfil"]; ?>" readonly>

                    <input type="hidden" class="form-control" id="usuario" value="<?php echo $_SESSION["IdUsuario"]; ?>" readonly>
                    <input type="hidden" class="form-control" id="telefono" value="<?php echo $_SESSION["Telefono"]; ?>" readonly>

                  </div>

                </div>

                <div class="form-group">
                
                  <div class="input-group">
                    
                    <span class="input-group-addon"><i class="fa fa-user"></i></span> 

                    <input type="text" class="form-control" id="Nuser" value="<?php echo $_SESSION["NombreUser"]; ?>" readonly>

                  </div>
                  
                  <div class="box-footer">
                    <label for="cliente">Seleccionar cliente</label>
                    <select class="form-control select2" id="cliente" name="cliente" style="width:100%">
                      <option value= '0'>-- Selecciona --</option>
                      <?php
                        $IdNegocio = $_SESSION["IdNegocio"];
                        $clientes = ControladorClientes::ctrMostrarClientes("Negocio", $IdNegocio);
                        foreach ($clientes as $cliente) {
                          echo '<option value="'.$cliente["id_Cliente"].'">'.$cliente["Nombre"].' '.$cliente["APaterno"].'</option>';
                        }
                      ?>
                    </select>
                  </div>
                  
                  <button type="button" class="btn btn-warning btnGeneraCierre pull-right">Generar Corte</button>

                  <table class="table table-striped">
                    <thead>
                       <tr>
                        <th></th>
                        <th></th>
                       </tr>
                     </thead>
                     <tbody>
                       <tr>
                        <td>
                          <div class="input-group">
                            
                              <span class="input-group-addon"><i class="fa fa-barcode"></i> </span>

                              <input type="text" class="form-control input-sm" name="Qbarra" id="Qbarra"  placeholder="Codigo Barras" onchange="CargaProducto();" >

                          </div>
                        </td>
                       </tr>
                     </tbody>
                </table> 

                </div>
         
                <div class="form-group row nuevoProducto">
                </div>

                <input type="hidden" id="listaProductos" name="listaProductos">

                <hr>  

                <div class="row">
                  
                  <div class="col-xs-11 pull-right">
                    
                    <table class="table">

                      <thead>

                        <tr>
                          <th>Paga:</th>
                          <th>Cambio:</th>
                          <th>Total</th> 

                        </tr>

                      </thead>

                      <tbody>
                      
                        <tr>

                          <td style="width: 30%">
                            
                            <div class="input-group">
                           
                              <span class="input-group-addon"><i class="ion ion-social-usd"></i></span>

                              <input type="number" class="form-control " id="paga" name="paga" placeholder="00000" required>
                              
                        
                            </div>

                          </td>

                          <td style="width: 30%">
                            
                            <div class="input-group">
                           
                              <span class="input-group-addon"><i class="ion ion-social-usd"></i></span>

                              <input type="text" class="form-control " id="cambio" name="cambio" placeholder="00000" readonly required>
                              
                        
                            </div>

                          </td>

                          <td style="width: 30%">
                            
                            <div class="input-group">
                           
                              <span class="input-group-addon"><i class="ion ion-social-usd"></i></span>

                              <input type="text" class="form-control " id="nuevoTotalVenta" name="nuevoTotalVenta" total="" placeholder="00000" readonly required>

                              <input type="hidden" name="totalVenta" id="totalVenta">
                              
                        
                            </div>

                          </td>

                        </tr>

                      </tbody>

                    </table>

                  </div>

                </div>

                <hr>
      
              </div>
              
              
          </div>

          <div class="box-footer">
              
              <div class="box box-danger">

          <div class="box-header with-border align-middle">
            <p class="text-center">Saldos Recargas</p>
          </div>

          <?php
          $datos = controladorWebservice::ctrConsultarSaldos();
          $tiempo = $datos->{'tiempoaire'};
          $servicios = $datos->{'pagoservicios'};
          $sms = $datos->{'sms'};
          $comision = $tiempo * 0.05;
          $pago = $tiempo - $comision;
          
          $IdNegocio = $_SESSION["IdNegocio"];
          $SNegocio = ModeloNegocios::MdlObtenerNegocio($IdNegocio);
          $tiempo = $SNegocio['SAire'];
          $servicios = $SNegocio['SServicios'];

          ?>
          <div class="box-body">
            <div class="container">
              <div class="row">
                <div class="col-sm-1">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">T. Aire</span>
                    </div>
                    <span class="input-group-text">$<?= number_format($tiempo,2) ?></span>
                  </div>
                </div>
                <div class="col-sm-1">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">Servicios</span>
                    </div>
                    <span class="input-group-text">$<?= number_format($servicios,2) ?></span>
                  </div>
                </div>
                <div class="col-sm-1">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">SMS</span>
                    </div>
                    <span class="input-group-text">$<?= number_format($pago,2) ?></span>
                  </div>
                </div>
                <div class="col-sm-1">
                  <span><button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalRecargas" data-dismiss="modal">Recargar</button></span>
                </div>
                <div class="col-sm-1">
                  <span><button type="button" class="btn btn-success" data-toggle="modal" data-target="#modalVerificar" data-dismiss="modal">Verificar</button></span>
                </div>
              </div>
            </div>

          </div>

        </div>
            <button type="button" class="btn btn-success btnGuardarVenta pull-right">Guardar venta</button>
          </div>

        </form>

        </div>
        
            
      </div>


    </div>

    

   
  </section>

</div>


<!--=====================================
MODAL RECARGAS
======================================-->

<div id="modalRecargas" class="modal fade" role="dialog">

  <div class="modal-dialog">

    <div class="modal-content">

      <form role="form" method="post">

        <!--=====================================
        CABEZA DEL MODAL
        ======================================-->

        <div class="modal-header" style="background:#3c8dbc; color:white">

          <button type="button" class="close" data-dismiss="modal">&times;</button>

          <h4 class="modal-title">Pago de Servicios</h4>

        </div>

        <!--=====================================
        CUERPO DEL MODAL
        ======================================-->

        <div class="modal-body">

          <div class="box-body">

            <!-- Custom Tabs -->
            <div class="nav-tabs-custom">
              <ul class="nav nav-tabs">
                <li class="active"><a href="#productos" data-toggle="tab">Productos</a></li>
                <li class="disabled-tab"><a href="#servicios" data-toggle="tab">Servicios</a></li>
                <li class="disabled-tab"><a href="#pines" data-toggle="tab">Pines</a></li>
              </ul>
              <div class="tab-content">
                <div class="tab-pane active" id="productos">
                  <?php
                  $productos = controladorWebservice::ctrObtenerProductos();
                  foreach ($productos as $fila) {
                    $nombrespro[] = $fila->nombre;
                  }
                  $nombrespro = array_unique($nombrespro);
                  ?>

                  <div class="row">
                    <div class="col-md-4">
                      <label> Nombre:</label>
                      <select id="cbxnombre" class="form-control select2" name="cbxnombre">
                        <option value="0">Seleccionar Empresa</option>
                        <?php foreach ($nombrespro as $row) { ?>
                          <option value="<?php echo $row; ?>"><?php echo $row; ?></option>
                        <?php } ?>
                      </select>
                    </div>
                    <div class="col-md-4">
                      <label>Codigo:</label>
                      <select id="cbxcodigo" class="form-control select2" name="cbxcodigo">
                      </select>
                    </div>
                  </div>
                 
                  
                  <div class="row">
                    <div class="col-md-4">
                      <label>Numero:</label>
                      <input type="password" class="form-control select2" id="numero" name="numero" maxlength="10">
                    </div>
                    <div class="col-md-4">
                      <label>Confirmar Numero:</label>
                      <input type="password" class="form-control select2" id="numero2" name="numero2" maxlength="10">
                    </div>
                  </div>
                  
                  <div class="row">
                    <div class="col-md-8">
                      <label>Descripcion:</label>
                      <select id="cbxdescripcion" class="form-control select2" name="cbxdescripcion">
                      </select>
                    </div>
                    <div class="col-md-4">
                      <label> Monto:</label>
                      <select id="cbxmonto" class="form-control select2" name="cbxmonto">
                      </select>
                    </div>
                  </div>
                  <div class="row hidden" id="row_request">
                    <div class="col-md-12">
                      <label>Request Id:</label>
                      <input type="text" class="form-control select2" id="requestid" name="requestid" readonly>
                    </div>

                  </div>
                  <div class="row hidden" id="row_res_1">
                    <div class="col-md-8">
                      <label>Request id:</label>
                      <input type="text" class="form-control select2" id="res_requestid" name="res_requestid" readonly>
                    </div>
                    <div class="col-md-4">
                      <label>Fecha:</label>
                      <input type="text" class="form-control select2" id="res_fecha" name="res_fecha" readonly>
                    </div>
                  </div>
                  <div class="row hidden" id="row_res_2">
                    <div class="col-md-3">
                      <label>Folio:</label>
                      <input type="text" class="form-control select2" id="res_folio" name="res_folio" readonly>
                    </div>
                    <div class="col-md-3">
                      <label>Monto:</label>
                      <input type="text" class="form-control select2" id="res_monto" name="res_monto" readonly>
                    </div>
                    <div class="col-md-3">
                      <label>Cargo:</label>
                      <input type="text" class="form-control select2" id="res_cargo" name="res_cargo" readonly>
                    </div>
                    <div class="col-md-3">
                      <label>Abono:</label>
                      <input type="text" class="form-control select2" id="res_abono" name="res_abono" readonly>
                    </div>
                  </div>
                  <div class="row hidden" id="row_res_3">
                    <div class="col-md-3">
                      <label>Referencia:</label>
                      <input type="text" class="form-control select2" id="res_referencia" name="res_referencia" readonly>
                    </div>
                    <div class="col-md-4">
                      <label>Producto:</label>
                      <input type="text" class="form-control select2" id="res_producto" name="res_producto" readonly>
                    </div>
                    <div class="col-md-5">
                      <label>Saldo Final:</label>
                      <input type="text" class="form-control select2" id="res_saldofinal" name="res_saldofinal" readonly>
                    </div>

                  </div>
                  <br>
                  <button type="button" class="btn btn-primary mb-3" onclick="reseRecarProd(event);">Reservar</button>
                  <input type="hidden" id="tiempo" value="<?php echo $tiempo; ?>">
                  <button type="button" class="btn btn-primary mb-3" onclick="proceRecarProd(event);">Recargar</button>
                </div>
                <!-- /.tab-pane -->
                <div class="tab-pane" id="servicios">

                  <?php
                  $servicios = controladorWebservice::ctrObtenerServicios();
                  foreach ($servicios as $fila) {
                    $nombresser[] = $fila->nombre;
                  }
                  $nombresser = array_unique($nombresser);
                  ?>

                  <div class="row">
                    <div class="col-md-4">
                      <label> Nombre:</label>
                      <select id="cbxnombre_serv" class="form-control select2" name="cbxnombre_serv">
                        <option value="0">Seleccionar Empresa</option>
                        <?php foreach ($nombresser as $row) { ?>
                          <option value="<?php echo $row; ?>"><?php echo $row; ?></option>
                        <?php } ?>
                      </select>
                    </div>
                    <div class="col-md-4">
                      <label>Codigo:</label>
                      <select id="cbxcodigo_serv" class="form-control select2" name="cbxcodigo_serv">
                      </select>
                    </div>
                    <div class="col-md-4">
                      <label> Monto:</label>
                      <select id="cbxmonto_serv" class="form-control select2" name="cbxmonto_serv">
                      </select>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-12">
                      <label>Descripcion:</label>
                      <select id="cbxdescripcion_serv" class="form-control select2" name="cbxdescripcion_serv">
                      </select>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-8">
                      <label>Referencia:</label>
                      <input type="text" class="form-control select2" id="referencia_serv" name="referencia_serv">
                    </div>
                    <div class="col-md-4">
                      <label>Monto Pago:</label>
                      <input type="text" class="form-control select2" id="monto_serv" name="monto_serv">
                    </div>
                  </div>
                  <div class="row hidden" id="row_request_serv">
                    <div class="col-md-12">
                      <label>Request Id:</label>
                      <input type="text" class="form-control select2" id="requestid_serv" name="requestid_serv" readonly>
                    </div>

                  </div>
                  <div class="row hidden" id="row_res_1_serv">
                    <div class="col-md-8">
                      <label>Request id:</label>
                      <input type="text" class="form-control select2" id="res_requestid_serv" name="res_requestid_serv" readonly>
                    </div>
                    <div class="col-md-4">
                      <label>Fecha:</label>
                      <input type="text" class="form-control select2" id="res_fecha_serv" name="res_fecha_serv" readonly>
                    </div>
                  </div>
                  <div class="row hidden" id="row_res_2_serv">
                    <div class="col-md-3">
                      <label>Folio:</label>
                      <input type="text" class="form-control select2" id="res_folio_serv" name="res_folio_serv" readonly>
                    </div>
                    <div class="col-md-3">
                      <label>Monto:</label>
                      <input type="text" class="form-control select2" id="res_monto_serv" name="res_monto_serv" readonly>
                    </div>
                    <div class="col-md-3">
                      <label>Cargo:</label>
                      <input type="text" class="form-control select2" id="res_cargo_serv" name="res_cargo_serv" readonly>
                    </div>
                    <div class="col-md-3">
                      <label>Abono:</label>
                      <input type="text" class="form-control select2" id="res_abono_serv" name="res_abono_serv" readonly>
                    </div>
                  </div>
                  <div class="row hidden" id="row_res_3_serv">
                    <div class="col-md-3">
                      <label>Referencia:</label>
                      <input type="text" class="form-control select2" id="res_referencia_serv" name="res_referencia_serv" readonly>
                    </div>
                    <div class="col-md-4">
                      <label>Producto:</label>
                      <input type="text" class="form-control select2" id="res_producto_serv" name="res_producto_serv" readonly>
                    </div>
                    <div class="col-md-5">
                      <label>Saldo Final:</label>
                      <input type="text" class="form-control select2" id="res_saldofinal_serv" name="res_saldofinal_serv" readonly>
                    </div>
                  </div>
                  <br>
                  <button type="button" class="btn btn-primary mb-3" onclick="reseRecarServ(event);">Reservar</button>
                  <button type="button" class="btn btn-primary mb-3" onclick="proceRecarServ(event);">Recargar</button>


                </div>
                <!-- /.tab-pane -->
                <div class="tab-pane" id="pines">

                  <?php
                  $pines = controladorWebservice::ctrObtenerPines();
                  foreach ($pines as $fila) {
                    $nombrespin[] = $fila->nombre;
                  }
                  $nombrespin = array_unique($nombrespin);
                  ?>

                  <div class="row">
                    <div class="col-md-4">
                      <label> Nombre:</label>
                      <select id="cbxnombre_pin" class="form-control select2" name="cbxnombre_pin">
                        <option value="0">Seleccionar Empresa</option>
                        <?php foreach ($nombrespin as $row) { ?>
                          <option value="<?php echo $row; ?>"><?php echo $row; ?></option>
                        <?php } ?>
                      </select>
                    </div>
                    <div class="col-md-4">
                      <label>Codigo:</label>
                      <select id="cbxcodigo_pin" class="form-control select2" name="cbxcodigo_pin">
                      </select>
                    </div>
                    <div class="col-md-4">
                      <label>Numero:</label>
                      <input type="text" class="form-control select2" id="numero_pin" name="numero_pin">
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-9">
                      <label>Descripcion:</label>
                      <select id="cbxdescripcion_pin" class="form-control select2" name="cbxdescripcion_pin">
                      </select>
                    </div>
                    <div class="col-md-3">
                      <label> Monto:</label>
                      <select id="cbxmonto_pin" class="form-control select2" name="cbxmonto_pin">
                      </select>
                    </div>
                  </div>
                  <div class="row hidden" id="row_request_pin">
                    <div class="col-md-12">
                      <label>Request Id:</label>
                      <input type="text" class="form-control select2" id="requestid_pin" name="requestid_pin" readonly>
                    </div>

                  </div>
                  <div class="row hidden" id="row_res_1_pin">
                    <div class="col-md-8">
                      <label>Request id:</label>
                      <input type="text" class="form-control select2" id="res_requestid_pin" name="res_requestid_pin" readonly>
                    </div>
                    <div class="col-md-4">
                      <label>Fecha:</label>
                      <input type="text" class="form-control select2" id="res_fecha_pin" name="res_fecha_pin" readonly>
                    </div>
                  </div>
                  <div class="row hidden" id="row_res_2_pin">
                    <div class="col-md-3">
                      <label>Folio:</label>
                      <input type="text" class="form-control select2" id="res_folio_pin" name="res_folio_pin" readonly>
                    </div>
                    <div class="col-md-3">
                      <label>Monto:</label>
                      <input type="text" class="form-control select2" id="res_monto_pin" name="res_monto_pin" readonly>
                    </div>
                    <div class="col-md-3">
                      <label>Cargo:</label>
                      <input type="text" class="form-control select2" id="res_cargo_pin" name="res_cargo_pin" readonly>
                    </div>
                    <div class="col-md-3">
                      <label>Abono:</label>
                      <input type="text" class="form-control select2" id="res_abono_pin" name="res_abono_pin" readonly>
                    </div>
                  </div>
                  <div class="row hidden" id="row_res_3_pin">
                    <div class="col-md-3">
                      <label>Referencia:</label>
                      <input type="text" class="form-control select2" id="res_referencia_pin" name="res_referencia_pin" readonly>
                    </div>
                    <div class="col-md-4">
                      <label>Producto:</label>
                      <input type="text" class="form-control select2" id="res_producto_pin" name="res_producto_pin" readonly>
                    </div>
                    <div class="col-md-5">
                      <label>Saldo Final:</label>
                      <input type="text" class="form-control select2" id="res_saldofinal_pin" name="res_saldofinal_pin" readonly>
                    </div>
                  </div>
                  <br>
                  <button type="button" class="btn btn-primary mb-3" onclick="reseRecarPin(event);">Reservar</button>
                  <button type="button" class="btn btn-primary mb-3" onclick="proceRecarPin(event);">Recargar</button>


                </div>
                <!-- /.tab-pane -->
              </div>
              <!-- /.tab-content -->
              <p class="active-tab"><strong>Activo</strong>: <span id="activo" name="activo">Productos</span></p>
              <p class="previous-tab"><strong>Anterior</strong>: <span>Productos</span></p>
            </div>
            <!-- nav-tabs-custom -->

          </div>

        </div>

        <!--=====================================
        PIE DEL MODAL
        ======================================-->

        <div class="modal-footer">

          <button type="submit" class="btn btn-primary">Salir</button>

        </div>

      </form>

    </div>

  </div>

</div>

<!--=====================================
MODAL VERIFICAR RECARGAS
======================================-->

<div id="modalVerificar" class="modal fade" role="dialog">

  <div class="modal-dialog">

    <div class="modal-content">

      <form role="form" method="post">


        <!--=====================================
        CABEZA DEL MODAL
        ======================================-->

        <div class="modal-header" style="background:#3c8dbc; color:white">

          <button type="button" class="close" data-dismiss="modal">&times;</button>

          <h4 class="modal-title">Verificar Recargas</h4>

        </div>

        <!--=====================================
        CUERPO DEL MODAL
        ======================================-->

        <div class="modal-body">

          <div class="box-body">

            <div class="row" id="row_request_ver">
              <div class="col-md-12">
                <label>Request Id:</label>
                <input type="text" class="form-control select2" id="requestid_ver" name="requestid_ver">
              </div>

            </div>
            <div class="row hidden" id="row_res_1_ver">
              <div class="col-md-8">
                <label>Request id:</label>
                <input type="text" class="form-control select2" id="res_requestid_ver" name="res_requestid_ver">
              </div>
              <div class="col-md-4">
                <label>Fecha:</label>
                <input type="text" class="form-control select2" id="res_fecha_ver" name="res_fecha_ver">
              </div>
            </div>
            <div class="row hidden" id="row_res_2_ver">
              <div class="col-md-3">
                <label>Folio:</label>
                <input type="text" class="form-control select2" id="res_folio_ver" name="res_folio_ver">
              </div>
              <div class="col-md-3">
                <label>Monto:</label>
                <input type="text" class="form-control select2" id="res_monto_ver" name="res_monto_ver">
              </div>
              <div class="col-md-3">
                <label>Cargo:</label>
                <input type="text" class="form-control select2" id="res_cargo_ver" name="res_cargo_ver">
              </div>
              <div class="col-md-3">
                <label>Abono:</label>
                <input type="text" class="form-control select2" id="res_abono_ver" name="res_abono_ver">
              </div>
            </div>
            <div class="row hidden" id="row_res_3_ver">
              <div class="col-md-3">
                <label>Referencia:</label>
                <input type="text" class="form-control select2" id="res_referencia_ver" name="res_referencia_ver">
              </div>
              <div class="col-md-4">
                <label>Producto:</label>
                <input type="text" class="form-control select2" id="res_producto_ver" name="res_producto_ver">
              </div>
              <div class="col-md-5">
                <label>Saldo Final:</label>
                <input type="text" class="form-control select2" id="res_saldofinal_ver" name="res_saldofinal_ver">
              </div>

            </div>

          </div>

        </div>

        <!--=====================================
        PIE DEL MODAL
        ======================================-->

        <div class="modal-footer">

          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Salir</button>
          <button type="button" class="btn btn-primary mb-3" onclick="verificarRecarga(event);">Verificar</button>
        </div>

      </form>

    </div>

  </div>

</div>
