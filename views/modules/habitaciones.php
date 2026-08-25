<?php

$ctlHabitacion = new ControladorHabitaciones();

?>
<div class="content-wrapper">

  <section class="content-header">
    <h1>
      Administrar Habitaciones
    </h1>
    <ol class="breadcrumb">
      <li><a href="inicio"><i class="fa fa-dashboard"></i>Inicio</a></li>
      <li class="active">Administrar Habitaciones</li>
    </ol>
  </section>

  <section class="content">

    <div class="box hab-box">
      <div class="box-header with-border">
        <button class="btn hab-btn-primary" data-toggle="modal" data-target="#modalAgregarHabitacion">
          <i class="fa fa-hotel"></i> Agregar Habitación
        </button>
      </div>

      <div class="box-body">
        <div class="hab-tabla-wrap">
         <table class="table table-bordered table-striped tablaHabitaciones">
           <thead>
             <tr>
              <th>#</th>
              <th>Número de habitación</th>
              <th>Tipo</th>
              <th>Capacidad</th>
              <th>Precio/Noche</th>
              <th>Estatus</th>
              <th>Acciones</th>
             </tr>
           </thead>
         </table>
        </div>
      </div>
    </div>
  </section>
</div>

<style>
  .content-wrapper{ background:#f2ece0; }
  .content-header h1{
    color:#3f342e;
    font-weight:800;
    letter-spacing:1px;
    text-transform:uppercase;
  }

  .hab-btn-primary{
    background:#81412d;
    border-color:#81412d;
    color:#fff;
  }
  .hab-btn-primary:hover, .hab-btn-primary:focus{
    background:#6e3625;
    border-color:#6e3625;
    color:#fff;
  }

  .tablaHabitaciones thead th{
    background:#3f342e;
    color:#fff;
    border-color:#3f342e;
  }

  .hab-badge{
    display:inline-block;
    padding:4px 14px;
    border-radius:999px;
    font-size:11px;
    font-weight:700;
    color:#fff;
    text-decoration:none;
  }
  .hab-badge-activa{ background:#81412d; }
  .hab-badge-inhabilitada{ background:#9c9088; }

  .btnEditarHabitacion{
    background:#3f342e;
    border-color:#3f342e;
    color:#fff;
  }
  .btnEditarHabitacion:hover, .btnEditarHabitacion:focus{
    background:#2c2420;
    border-color:#2c2420;
    color:#fff;
  }
  .btnSuspenderHabitacion{
    background:#b96a37;
    border-color:#b96a37;
    color:#fff;
  }
  .btnSuspenderHabitacion:hover, .btnSuspenderHabitacion:focus{
    background:#9c5a2e;
    border-color:#9c5a2e;
    color:#fff;
  }

  #modalAgregarHabitacion .btn-success,
  #modalEditarHabitacion .btn-success{
    background:#81412d;
    border-color:#81412d;
  }
  #modalAgregarHabitacion .btn-success:hover,
  #modalEditarHabitacion .btn-success:hover{
    background:#6e3625;
    border-color:#6e3625;
  }

  #modalEditarHabitacion .toggle-on.btn-success{
    background:#81412d !important;
    border-color:#81412d !important;
    color:#fff !important;
  }
  #modalEditarHabitacion .toggle-off.btn-danger{
    background:#9c9088 !important;
    border-color:#9c9088 !important;
    color:#fff !important;
  }

  /* Bordes redondeados: tarjeta, modales, campos, botones */
  .hab-box{ border-radius:16px; overflow:hidden; }
  .hab-btn-primary{ border-radius:8px; }

  .hab-btn-secondary{
    background:#fff;
    border:1px solid #3f342e;
    color:#3f342e;
    border-radius:8px;
  }
  .hab-btn-secondary:hover, .hab-btn-secondary:focus{
    background:#f2ede6;
    border-color:#3f342e;
    color:#3f342e;
  }

  #modalAgregarHabitacion .modal-content,
  #modalEditarHabitacion .modal-content{
    border-radius:16px;
    overflow:hidden;
  }
  #modalAgregarHabitacion .btn,
  #modalEditarHabitacion .btn{
    border-radius:8px;
  }
  #modalAgregarHabitacion .form-control,
  #modalEditarHabitacion .form-control,
  #modalAgregarHabitacion .input-group-addon,
  #modalEditarHabitacion .input-group-addon{
    border-radius:8px;
    border-color:#e4d9c8;
  }
  #modalAgregarHabitacion .input-group .form-control,
  #modalEditarHabitacion .input-group .form-control{
    border-top-left-radius:0;
    border-bottom-left-radius:0;
  }
  #modalAgregarHabitacion .input-group .input-group-addon,
  #modalEditarHabitacion .input-group .input-group-addon{
    border-top-right-radius:0;
    border-bottom-right-radius:0;
    background:#f2ede6;
    color:#81412d;
  }

  .tablaHabitaciones .btn-group{ display:inline-flex; gap:6px; }
  .tablaHabitaciones .btn-group .btn{ border-radius:8px !important; }

  .hab-tabla-wrap{
    overflow-x:auto;
    border:1px solid #eee3d2;
    border-radius:10px;
  }
  .hab-tabla-wrap .tablaHabitaciones{ margin-bottom:0; }

  .dataTables_wrapper select,
  .dataTables_wrapper input[type="search"]{
    border-radius:8px;
    border-color:#e4d9c8;
  }
  .dataTables_wrapper .pagination > li > a,
  .dataTables_wrapper .pagination > li > span{
    border-radius:8px !important;
    margin:0 2px;
    color:#3f342e;
    border-color:#e4d9c8;
  }
  .dataTables_wrapper .pagination > li > a:hover{
    background:#f2ede6;
    border-color:#dfc6a2;
    color:#3f342e;
  }
  .dataTables_wrapper .pagination > li.active > a,
  .dataTables_wrapper .pagination > li.active > a:hover,
  .dataTables_wrapper .pagination > li.active > a:focus{
    background:#81412d !important;
    border-color:#81412d !important;
    color:#fff !important;
  }
  .dataTables_wrapper .pagination > li.disabled > a{
    color:#c9beae;
  }
</style>

<!-- Modal Agregar Habitación -->
<div id="modalAgregarHabitacion" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <form role="form" method="post" enctype="multipart/form-data" autocomplete="off">

      <div class="modal-header" style="background:#3f342e; color:white">
        <button type="button" class="close" data-dismiss="modal" style="color:white; opacity:1;">&times;</button>
        <h4 class="modal-title"><i class="fa fa-hotel"></i> Agregar Habitación</h4>
      </div>
      <div class="modal-body">
        <div class="box-body">

           <table class="table table-striped">
             <tbody>
               <tr>
                <td>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-hashtag"></i></span>
                    <input type="text" class="form-control input-sm" name="nuevoNumero" placeholder="Número de Habitación" autocomplete="off" required>
                  </div>
                </td>
                <td>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-bed"></i></span>
                    <input type="text" class="form-control input-sm" name="nuevoTipo" id="nuevoTipo" maxlength="100" placeholder="Tipo de Habitación" required>
                  </div>
                  <span id="contadorNuevoTipo" class="help-block small text-muted">0/100 caracteres</span>
                </td>
               </tr>
               <tr>
                <td>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-users"></i></span>
                    <input type="number" class="form-control input-sm" name="nuevaCapacidad" placeholder="Capacidad" min="1" required>
                  </div>
                </td>
                <td>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-dollar"></i></span>
                    <input type="text" inputmode="decimal" class="form-control input-sm" name="nuevoPrecio" id="nuevoPrecio" placeholder="Precio por Noche" required>
                  </div>
                </td>
               </tr>
             </tbody>
           </table>
           <div class="form-group" style="margin-top:20px;">
             <label><i class="fa fa-align-left"></i> Descripción</label>
             <textarea class="form-control" name="nuevaDescripcion" id="nuevaDescripcion" rows="4" maxlength="255" placeholder="Descripción" style="resize:none;" required></textarea>
             <span id="contadorNuevaDescripcion" class="help-block small text-muted">0/255 caracteres</span>
           </div>
           <div class="form-group">
             <div class="panel">CARGAR FOTO</div>
             <input type="file" class="nuevaFoto" name="nuevaFoto">
             <p class="help-block"><i class="fa fa-exclamation-triangle"></i> Peso maximo de la foto 3MB </p>
           </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn hab-btn-secondary pull-left" data-dismiss="modal">Salir</button>
        <button type="submit" name="nuevaHabitacion" class="btn btn-success">Guardar Habitación</button>
      </div>

      <?php

      $ctlHabitacion -> crtInsertarHabitacion();

      ?>
    </form>
    </div>
  </div>
</div>

<!-- Modal Editar Habitación -->
<div id="modalEditarHabitacion" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <form role="form" method="post" enctype="multipart/form-data">

      <div class="modal-header" style="background:#3f342e; color:white">
        <button type="button" class="close" data-dismiss="modal" style="color:white; opacity:1;">&times;</button>
        <h4 class="modal-title"><i class="fa fa-hotel"></i> Editar Habitación</h4>
      </div>
      <div class="modal-body">
        <div class="box-body">

           <input type="hidden" name="editarIdHabitacion" id="editarIdHabitacion">
           <input type="hidden" name="editarFotoActual" id="editarFotoActual">

           <table class="table table-striped">
             <tbody>
               <tr>
                <td>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-hashtag"></i></span>
                    <input type="text" class="form-control input-sm" name="editarNumero" id="editarNumero" placeholder="Número de Habitación" required>
                  </div>
                </td>
                <td>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-bed"></i></span>
                    <input type="text" class="form-control input-sm" name="editarTipo" id="editarTipo" maxlength="100" placeholder="Tipo de Habitación" required>
                  </div>
                  <span id="contadorEditarTipo" class="help-block small text-muted">0/100 caracteres</span>
                </td>
               </tr>
               <tr>
                <td>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-users"></i></span>
                    <input type="number" class="form-control input-sm" name="editarCapacidad" id="editarCapacidad" min="1" required>
                  </div>
                </td>
                <td>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-dollar"></i></span>
                    <input type="text" inputmode="decimal" class="form-control input-sm" name="editarPrecio" id="editarPrecio" required>
                  </div>
                </td>
               </tr>
               <tr>
                <td colspan="2">
                  <div class="input-group" style="border:none;">
                    <label style="margin-right:10px;">Estatus de la habitación</label>

                    <style>
                      .toggle.ios, .toggle-on.ios, .toggle-off.ios { border-radius: 20rem; }
                      .toggle.ios .toggle-handle { border-radius: 20rem; }
                    </style>

                    <input type="checkbox" name="editarEstatus" id="editarEstatus" data-style="ios" data-toggle="toggle" data-onstyle="success" data-offstyle="danger" data-on="Activa" data-off="Inhabilitada" data-width="100">
                  </div>
                </td>
               </tr>
             </tbody>
           </table>
           <div class="form-group" style="margin-top:20px;">
             <label><i class="fa fa-align-left"></i> Descripción</label>
             <textarea class="form-control" name="editarDescripcion" id="editarDescripcion" rows="4" maxlength="255" style="resize:none;" required></textarea>
             <span id="contadorEditarDescripcion" class="help-block small text-muted">0/255 caracteres</span>
           </div>
           <div class="form-group">
             <div class="panel">CARGAR FOTO</div>
             <div>
               <img id="editarFotoPreview" src="" alt="Foto actual" style="width:80px;height:80px;object-fit:cover;border-radius:4px;margin-bottom:8px;display:none;" onerror="mostrarErrorFotoHabitacion()">
               <p id="editarFotoAdvertencia" class="help-block text-danger" style="display:none;"><i class="fa fa-exclamation-triangle"></i> No se encontró la foto actual de esta habitación.</p>
             </div>
             <input type="file" class="editarFoto" name="editarFoto">
             <p class="help-block"><i class="fa fa-exclamation-triangle"></i> Peso maximo de la foto 3MB. Deja este campo vacío para conservar la foto actual.</p>
           </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn hab-btn-secondary pull-left" data-dismiss="modal">Salir</button>
        <button type="submit" name="editarHabitacion" class="btn btn-success">Modificar Habitación</button>
      </div>

      <?php

      $ctlHabitacion -> crtActualizarHabitacion();

      ?>
    </form>
    </div>
  </div>
</div>