  <?php 

  $objNegocio = new ControladorNegocios();

  ?>
  <div class="content-wrapper">

    <section class="content-header">
      <h1>
        Administrar Negocio
      </h1>
      <ol class="breadcrumb">
        <li><a href="inicio"><i class="fa fa-dashboard"></i>Inicio</a></li>
        <li class="active">Administar Negocios</li>
      </ol>
    </section>

    <section class="content">

      <div class="box">
        <div class="box-header with-border">
          <button class="btn btn-primary" data-toggle="modal" data-target="#modalAgregarNegocio">
            Agregar Negocio 
          </button>
        </div>

        <?php
        if($_SESSION["Perfil"] =="Soporte Tecnico"){

          echo '
          <div class="table-responsive">
          <table class="table table-bordered table-striped tablaNegocios style=text-align:center">
            <thead>
              <tr>
               <th>Id Negocio</th>
               <th>Razon Social</th>
               <th>Responsable</th>
               <th>Telefono</th>
               <th>Estado</th>
               <th>Municipio</th>
               <th>Colonia</th>
               <th>Calle</th>
               <th>Correo</th>
               <th>Giro</th>
               <th>Tipo Pago</th>
               <th>Fecha alta</th>
               <th>Fecha baja</th>
               <th>Tiempo Aire</th>
               <th>Servicios</th>
               <th>Estatus</th>
               <th>Acciones</th>
              </tr>
            </thead>
          </table> 
         </div>
          ';
        }
        
        ?>

      </div>
    </section>
  </div>


<!-- Modal agregar Negocio-->
<div id="modalAgregarNegocio" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <form role="form" method="post" enctype="multipart/form-data">

      <div class="modal-header" style="background:#00a65a; color:white">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Agregar Negocio</h4>
      </div>
      <div class="modal-body">
        <div class="box-body" >

          <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-suitcase"></i> </span>
            <input type="text" class="form-control input-sm" name="nuevoRazonsocial" placeholder="Razon Social" required>
          </div>

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
                    <span class="input-group-addon"><i class="fa fa-user"></i> </span>
                    <input type="text" class="form-control input-sm" name="nuevoResponsable" placeholder="Responsable" required>
                  </div>
                </td>
                <td>
                 <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-phone"></i> </span>
                    <input type="text" class="form-control" name="telefono" data-inputmask='"mask": "(999) 999-9999"' data-mask required>
                 </div>
                </td>
               </tr>
               <tr>
                <td>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-etsy"></i> </span>
                    <select class="form-control input-sm" name="NuevoEstado" id="cbestado" required>
                      <option value="" selected hidden>--Seleccionar Estado--</option>
                    </select>
                  </div>
                </td>
                <td>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-map-o"></i> </span>
                    <select class="form-control input-sm" name="NuevoMunicipio" id="cbmunicipio" required>
                      <option value="" selected hidden>--Seleccionar Municipio--</option>
                    </select>
                  </div>
                </td>
               </tr>
                <tr>
                <td>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-map-marker"></i> </span>
                    <select class="form-control input-sm" name="NuevaColonia" id="cbcolonia" required>
                      <option value="" selected hidden>--Seleccionar Colonia--</option>
                    </select>
                  </div>
                </td>
                <td>
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-street-view"></i> </span>
                      <input type="text" class="form-control input-sm" name="Calle" placeholder="Calle y Número" required>
                    </div>
                </td>
               </tr>
                <tr>
                <td>
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-envelope-open"></i> </span>
                      <input type="number" class="form-control input-sm" name="CodigoPostal" id="CodigoPostal" placeholder="Codigo Postal" readonly>
                    </div>
                </td>
                <td>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-calendar-check-o"></i> </span>
                    <select class="form-control input-sm" name="NuevoTipoPago" id="cbtipopago" required>
                      <option value="" selected hidden>--Seleccionar Tipo de Pago--</option>
                    </select>
                  </div>
                </td>
               </tr>
                <tr>
                <td>
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-envelope-o"></i> </span>
                      <input type="text" class="form-control input-sm" name="NuevoCorreo" placeholder="Correo" required>
                    </div>
                </td>
                <td>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-university"></i> </span>
                    <select class="form-control input-sm" name="NuevoGiro" id="cbgiro" required>
                      <option value="" selected hidden>--Seleccionar Giro--</option>
                    </select>
                  </div>
                </td>
               </tr>
              <tr>
                <td>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-check-circle"></i> </span>
                    <select class="form-control input-sm" name="NuevoEstatus" id="cbestatus" required>
                      <option value="" selected hidden>--Seleccionar Estatus--</option>
                    </select>
                  </div>
                </td>
               </tr>
             </tbody>
           </table> 
            <div class="form-group">
              <div class="panel">Cargar Responsiva</div>
              <input type="file" class="nuevaCartaResponsiva" name="nuevaCartaResponsiva">
              <p class="help-block"><i class="fa fa-exclamation-triangle"></i> Peso maximo de la foto 3MB </p>
            </div>
        </div> 
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-primary pull-left" data-dismiss="modal">Salir</button>
        <button type="submit" class="btn btn-success">Guardar Negocio</button>
      </div>

      <?php

      $objNegocio -> ctrInsertarNegocio();

      ?>
    </form>
    </div>
  </div>
</div>


<!-- Modal editar Negocio-->
<div id="modalEditarNegocio" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <form role="form" method="post">

      <div class="modal-header" style="background:#00a65a; color:white">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Modificar Negocio</h4>
      </div>
      <div class="modal-body">
        <div class="box-body" >

          <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-suitcase"></i> </span>
            <input type="text" class="form-control input-sm" name="editarRazonsocial" id="editarRazonSocial" placeholder="Razon Social" required>
            <input type="hidden" name="editarIdNegocio" id="editarIdNegocio" required>
          </div>

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
                    <span class="input-group-addon"><i class="fa fa-user"></i> </span>
                    <input type="text" class="form-control input-sm" name="editarResponsable" id="editarResponsable" placeholder="Responsable" required>
                  </div>
                </td>
                <td>
                 <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-phone"></i> </span>
                    <input type="text" class="form-control" name="editarTelefono" id="editarTelefono" data-inputmask='"mask": "(999) 999-9999"' data-mask required>
                 </div>
                </td>
               </tr>
               <tr>
                <td>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-etsy"></i> </span>
                    <select class="form-control input-sm" name="editarEstado" id="editarCbestado" required>
                      <option value="" selected hidden>--Seleccionar Estado--</option>
                    </select>
                  </div>
                </td>
                <td>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-map-o"></i> </span>
                    <select class="form-control input-sm" name="editarMunicipio" id="editarCbmunicipio" required>
                      <option value="" selected hidden>--Seleccionar Municipio--</option>
                    </select>
                  </div>
                </td>
               </tr>
                <tr>
                <td>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-map-marker"></i> </span>
                    <select class="form-control input-sm" name="editarColonia" id="editarCbcolonia" required>
                      <option value="" selected hidden>--Seleccionar Colonia--</option>
                    </select>
                  </div>
                </td>
                <td>
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-street-view"></i> </span>
                      <input type="text" class="form-control input-sm" name="editarCalle" id="editarCalle" placeholder="Calle y Número" required>
                    </div>
                </td>
               </tr>
                <tr>
                <td>
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-envelope-open"></i> </span>
                      <input type="number" class="form-control input-sm" name="editarCodigoPostal" id="editarCodigoPostal" placeholder="Codigo Postal" readonly>
                    </div>
                </td>
                <td>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-calendar-check-o"></i> </span>
                    <select class="form-control input-sm" name="editarTipoPago" id="editarCbtipopago" required>
                      <option value="" selected hidden>--Seleccionar Tipo de Pago--</option>
                    </select>
                  </div>
                </td>
               </tr>
                <tr>
                <td>
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-envelope-o"></i> </span>
                      <input type="text" class="form-control input-sm" name="editarCorreo" id="editarCorreo" placeholder="Correo" required>
                    </div>
                </td>
                <td>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-university"></i> </span>
                    <select class="form-control input-sm" name="editarGiro" id="editarCbgiro" required>
                      <option value="" selected hidden>--Seleccionar Giro--</option>
                    </select>
                  </div>
                </td>
               </tr>
                <tr>
                <td>
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-usd"></i> </span>
                      <input type="text" class="form-control input-sm" name="editarSAire" id="editarSAire" placeholder="TiempoAire" required>
                    </div>
                </td>
                <td>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-cc-diners-club"></i> </span>
                      <input type="text" class="form-control input-sm" name="editarSServicios" id="editarSServicios" placeholder="Servicios" required>
                  </div>
                </td>
               </tr>
              <tr>
                <td>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-check-circle"></i> </span>
                    <select class="form-control input-sm" name="editarEstatus" id="editarCbestatus" required>
                      <option value="" selected hidden>--Seleccionar Estatus--</option>
                    </select>
                  </div>
                </td>
               </tr>
             </tbody>
           </table>
        </div> 
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-primary pull-left" data-dismiss="modal">Salir</button>
        <button type="submit" class="btn btn-success">Modificar Negocio</button>
      </div>

      <?php

      $objNegocio -> ctrActualizarNegocio();

      ?>
    </form>
    </div>
  </div>
</div>