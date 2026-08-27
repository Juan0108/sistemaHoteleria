  <?php 

  $objHotel = new ControladorHoteles();

  ?>
  <div class="content-wrapper administrar-hotel">

    <section class="content-header">
      <h1>
        Administrar Hotel
      </h1>
      <ol class="breadcrumb">
        <li><a href="inicio"><i class="fa fa-dashboard"></i>Inicio</a></li>
        <li class="active">Administar Hoteles</li>
      </ol>
    </section>

    <section class="content">

      <div class="box">
        <div class="box-header with-border">
          <button class="btn btn-primary" data-toggle="modal" data-target="#modalAgregarHotel">
            Agregar Hotel 
          </button>
        </div>

        <?php
        if($_SESSION["Perfil"] =="Soporte Tecnico"){

          echo '
          <div class="table-responsive">
          <table class="table table-bordered table-striped tablaHoteles style=text-align:center">
            <thead>
              <tr>
               <th>Id Hotel</th>
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


<!-- Modal agregar Hotel-->
<div id="modalAgregarHotel" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <form role="form" method="post" enctype="multipart/form-data">

      <div class="modal-header" style="background:#3f342e; color:#f6ecdb">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Agregar Hotel</h4>
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
                    <select class="form-control input-sm" name="NuevaColonia" id="cbcolonia">
                      <option value="" selected hidden>--Seleccionar Colonia--</option>
                    </select>
                    <input type="text" class="form-control input-sm" name="NuevaColoniaTexto" id="cbcoloniaTexto" placeholder="Escribe la colonia (no está en el catálogo)" style="display:none;">
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
              <div class="panel">Cargar Contrato</div>
              <input type="file" class="nuevoContrato" name="nuevoContrato">
              <p class="help-block"><i class="fa fa-exclamation-triangle"></i> Peso maximo de la foto 3MB </p>
            </div>
        </div> 
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-primary pull-left" data-dismiss="modal">Salir</button>
        <button type="submit" class="btn btn-success">Guardar Hotel</button>
      </div>

      <?php

      $objHotel -> ctrInsertarHotel();

      ?>
    </form>
    </div>
  </div>
</div>


<!-- Modal editar Hotel-->
<div id="modalEditarHotel" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <form role="form" method="post">

      <div class="modal-header" style="background:#3f342e; color:#f6ecdb">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Modificar Hotel</h4>
      </div>
      <div class="modal-body">
        <div class="box-body" >

          <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-suitcase"></i> </span>
            <input type="text" class="form-control input-sm" name="editarRazonsocial" id="editarRazonSocial" placeholder="Razon Social" required>
            <input type="hidden" name="editarIdHotel" id="editarIdHotel" required>
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
                    <select class="form-control input-sm" name="editarColonia" id="editarCbcolonia">
                      <option value="" selected hidden>--Seleccionar Colonia--</option>
                    </select>
                    <input type="text" class="form-control input-sm" name="editarColoniaTexto" id="editarCbcoloniaTexto" placeholder="Escribe la colonia (no está en el catálogo)" style="display:none;">
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
        <button type="submit" class="btn btn-success">Modificar Hotel</button>
      </div>

      <?php

      $objHotel -> ctrActualizarHotel();

      ?>
    </form>
    </div>
  </div>
</div>

<style>
/* Restyle visual únicamente: misma paleta que Gestión de Usuarios (sand,
   espresso, rust, sienna) para que ambos módulos combinen entre sí. No se
   tocó ningún archivo de lógica, JS o ajax de este módulo. */
.content-wrapper.administrar-hotel{
  --sand:#dfc6a2;
  --espresso:#3f342e;
  --rust:#b96a37;
  --sienna:#81412d;
  --accent:#81412d;
  --accent-hover:#b96a37;
  background-color:#f4efe4;
}

.administrar-hotel .content-header h1{
  font-weight:800; text-transform:uppercase; letter-spacing:.5px; font-size:28px;
  color:var(--espresso); display:inline-block; position:relative; margin-bottom:20px;
}
.administrar-hotel .content-header h1::after{
  content:""; display:block; width:60px; height:4px; border-radius:2px;
  background:var(--accent); margin-top:8px;
}

.administrar-hotel .box{
  background:#fff; border:1px solid #eee3d2; border-radius:12px;
  box-shadow:0 1px 3px rgba(63,52,46,.06);
}
.administrar-hotel .box-header.with-border{ border-bottom:none; padding:20px 20px 0; }

.administrar-hotel .box-header .btn-primary{
  background:var(--accent); border:none; border-radius:8px; padding:9px 16px; font-weight:600;
}
.administrar-hotel .box-header .btn-primary:hover{ background:var(--accent-hover); }

.administrar-hotel .table-responsive{ padding:20px; }

.administrar-hotel table.tablaHoteles thead th{
  text-align:left; color:#ffffff; font-size:11px; text-transform:uppercase;
  letter-spacing:.6px; border-bottom:none !important; background:var(--espresso) !important;
  font-weight:700;
}
.administrar-hotel table.tablaHoteles tbody td{
  padding:12px !important; border-bottom:1px solid #f2ece0; vertical-align:middle;
}

.administrar-hotel .buttonActivo,
.administrar-hotel .buttonPeriodo,
.administrar-hotel .button{
  display:inline-block; padding:4px 13px; border-radius:20px; font-size:11.5px;
  font-weight:700; letter-spacing:.2px; border:none; text-align:center; vertical-align:middle;
}
.administrar-hotel .buttonActivo{ background:var(--sienna); background-image:none; color:#fff; }
.administrar-hotel .buttonPeriodo{ background:var(--rust); background-image:none; color:#fff; }
.administrar-hotel .button{ background:#a99884; background-image:none; color:#3f342e; }

.administrar-hotel .btnEditarHotel,
.administrar-hotel .btnEliminarHotel{
  border:none; border-radius:6px; padding:6px 10px; font-weight:600; color:#fff;
}
.administrar-hotel .btnEditarHotel{ background:var(--accent); }
.administrar-hotel .btnEditarHotel:hover{ background:var(--accent-hover); }
.administrar-hotel .btnEliminarHotel{ background:var(--rust); }
.administrar-hotel .btnEliminarHotel:hover{ background:var(--espresso); }

.administrar-hotel .dataTables_filter input,
.administrar-hotel .dataTables_length select{
  border:1px solid #e5d8bf; border-radius:8px; padding:6px 10px;
}
.administrar-hotel .dataTables_info{ color:#8d7a68; font-size:13px; }
.administrar-hotel .dataTables_wrapper .col-sm-5,
.administrar-hotel .dataTables_wrapper .col-sm-7{ float:none; width:100%; }
.administrar-hotel .dataTables_wrapper .dataTables_paginate{ margin-top:12px; text-align:center; }

.administrar-hotel .pagination > li > a{ color:var(--espresso); }
.administrar-hotel .pagination > li > a:hover{ color:#fff; background:var(--accent-hover); border-color:var(--accent-hover); }
.administrar-hotel .pagination > .active > a,
.administrar-hotel .pagination > .active > a:hover,
.administrar-hotel .pagination > .active > a:focus{ background:var(--accent); border-color:var(--accent); color:#fff; }

/* Modales Agregar/Editar Hotel: mismo lenguaje de color que Gestión de Usuarios */
#modalAgregarHotel .modal-header .close,
#modalEditarHotel .modal-header .close{ color:#f6ecdb; opacity:.75; text-shadow:none; }
#modalAgregarHotel .modal-header .close:hover,
#modalEditarHotel .modal-header .close:hover{ color:#fff; opacity:1; }

#modalAgregarHotel .modal-footer .btn-primary,
#modalEditarHotel .modal-footer .btn-primary{ background:#fff; border:1px solid #81412d; color:#81412d; }
#modalAgregarHotel .modal-footer .btn-primary:hover,
#modalEditarHotel .modal-footer .btn-primary:hover{ background:#81412d; color:#fff; }

#modalAgregarHotel .modal-footer .btn-success,
#modalEditarHotel .modal-footer .btn-success{ background:#81412d; border-color:#81412d; }
#modalAgregarHotel .modal-footer .btn-success:hover,
#modalEditarHotel .modal-footer .btn-success:hover{ background:#b96a37; border-color:#b96a37; }
</style>