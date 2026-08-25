<?php
?>
<div class="content-wrapper">

  <section class="content-header">
    <h1>
      Tareas
    </h1>
    <ol class="breadcrumb">
      <li><a href="inicio"><i class="fa fa-dashboard"></i>Inicio</a></li>
      <li class="active">Tareas</li>
    </ol>
  </section>

  <section class="content">

    <div class="box tar-box">
      <div class="overlay" id="tarOverlay" style="display:none;">
        <i class="fa fa-refresh fa-spin"></i>
      </div>
      <div class="box-header with-border">
        <button type="button" class="btn tar-btn-primary" data-toggle="modal" data-target="#modalAgregarTarea">
          <i class="fa fa-plus"></i> Agregar Tarea
        </button>
        <span class="tar-contador" id="tarContador">0 tareas cargadas</span>
      </div>

      <div class="box-body">
        <table class="table table-bordered table-striped tablaTareas">
          <thead>
            <tr>
              <th style="width:60px;">#</th>
              <th>Tarea</th>
              <th style="width:140px;">Estatus</th>
              <th style="width:220px;">Acciones</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </section>
</div>

<!-- Modal Agregar Tarea -->
<div id="modalAgregarTarea" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" style="background:#3f342e; color:white">
        <button type="button" class="close" data-dismiss="modal" style="color:white; opacity:1;">&times;</button>
        <h4 class="modal-title"><i class="fa fa-tasks"></i> Agregar Tarea</h4>
      </div>
      <div class="modal-body">
        <form id="formAgregarTarea" autocomplete="off">
          <div class="form-group">
            <label>Tarea</label>
            <textarea class="form-control" id="tarTexto" name="tarea" rows="3" maxlength="255" placeholder="Escribe la tarea a validar en servicios" style="resize:none;" required></textarea>
            <span class="help-block small text-muted" id="tarContadorCaracteres">0/255 caracteres</span>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn tar-btn-secondary pull-left" data-dismiss="modal">Salir</button>
        <button type="button" class="btn btn-success" id="tarGuardar">Guardar Tarea</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Editar Tarea -->
<div id="modalEditarTarea" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" style="background:#3f342e; color:white">
        <button type="button" class="close" data-dismiss="modal" style="color:white; opacity:1;">&times;</button>
        <h4 class="modal-title"><i class="fa fa-tasks"></i> Editar Tarea</h4>
      </div>
      <div class="modal-body">
        <form id="formEditarTarea" autocomplete="off">
          <input type="hidden" id="tarEditarId">
          <div class="form-group">
            <label>Tarea</label>
            <textarea class="form-control" id="tarEditarTexto" rows="3" maxlength="255" style="resize:none;" required></textarea>
            <span class="help-block small text-muted" id="tarEditarContadorCaracteres">0/255 caracteres</span>
          </div>
          <div class="form-group">
            <label style="margin-right:10px;">Estatus de la tarea</label>
            <style>
              .toggle.ios, .toggle-on.ios, .toggle-off.ios { border-radius: 20rem; }
              .toggle.ios .toggle-handle { border-radius: 20rem; }
            </style>
            <input type="checkbox" id="tarEditarEstatus" data-style="ios" data-toggle="toggle" data-onstyle="success" data-offstyle="danger" data-on="Activa" data-off="Inhabilitada" data-width="100">
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn tar-btn-secondary pull-left" data-dismiss="modal">Salir</button>
        <button type="button" class="btn btn-success" id="tarGuardarEdicion">Modificar Tarea</button>
      </div>
    </div>
  </div>
</div>

<style>
  .content-wrapper{ background:#f2ece0; }
  .content-header h1{
    color:#3f342e;
    font-weight:800;
    letter-spacing:1px;
    text-transform:uppercase;
  }

  .tar-box{ border-radius:16px; overflow:hidden; }
  .tar-box .box-header{
    display:flex;
    align-items:center;
    gap:16px;
    flex-wrap:wrap;
  }

  .tar-btn-primary{
    background:#81412d;
    border-color:#81412d;
    color:#fff;
    border-radius:8px;
  }
  .tar-btn-primary:hover, .tar-btn-primary:focus{
    background:#6e3625;
    border-color:#6e3625;
    color:#fff;
  }
  .tar-btn-secondary{
    background:#fff;
    border:1px solid #3f342e;
    color:#3f342e;
    border-radius:8px;
  }
  .tar-btn-secondary:hover, .tar-btn-secondary:focus{
    background:#f2ede6;
    border-color:#3f342e;
    color:#3f342e;
  }

  .tar-contador{
    font-weight:700;
    font-size:13px;
    color:#8d7a68;
    background:#f4efe4;
    border:1px solid #eee3d2;
    border-radius:999px;
    padding:5px 14px;
  }

  .tablaTareas thead th{
    background:#3f342e;
    color:#fff;
    border-color:#3f342e;
  }

  .tar-badge{
    display:inline-block;
    padding:4px 14px;
    border-radius:999px;
    font-size:11px;
    font-weight:700;
    color:#fff;
  }
  .tar-badge-activa{ background:#81412d; }
  .tar-badge-inhabilitada{ background:#9c9088; }

  .btnCambiarEstatusTarea{
    border-radius:8px !important;
    color:#fff;
    border:none;
  }
  .btnCambiarEstatusTarea.deshabilitar{
    background:#b96a37;
    border-color:#b96a37;
  }
  .btnCambiarEstatusTarea.deshabilitar:hover{ background:#9c5a2e; }
  .btnCambiarEstatusTarea.habilitar{
    background:#4c8c5a;
    border-color:#4c8c5a;
  }
  .btnCambiarEstatusTarea.habilitar:hover{ background:#3f7549; }

  #modalAgregarTarea .modal-content{
    border-radius:16px;
    overflow:hidden;
  }
  #modalAgregarTarea .btn{ border-radius:8px; }
  #modalAgregarTarea .form-control{
    border-radius:8px;
    border-color:#e4d9c8;
  }
  #modalAgregarTarea .btn-success,
  #modalEditarTarea .btn-success{
    background:#81412d;
    border-color:#81412d;
  }
  #modalAgregarTarea .btn-success:hover,
  #modalEditarTarea .btn-success:hover{
    background:#6e3625;
    border-color:#6e3625;
  }

  #modalEditarTarea .modal-content{
    border-radius:16px;
    overflow:hidden;
  }
  #modalEditarTarea .btn{ border-radius:8px; }
  #modalEditarTarea .form-control{
    border-radius:8px;
    border-color:#e4d9c8;
  }
  #modalEditarTarea .toggle-on.btn-success{
    background:#81412d !important;
    border-color:#81412d !important;
    color:#fff !important;
  }
  #modalEditarTarea .toggle-off.btn-danger{
    background:#9c9088 !important;
    border-color:#9c9088 !important;
    color:#fff !important;
  }

  .btnEditarTarea{
    background:#3f342e;
    border-color:#3f342e;
    color:#fff;
    border-radius:8px !important;
  }
  .btnEditarTarea:hover, .btnEditarTarea:focus{
    background:#2c2420;
    border-color:#2c2420;
    color:#fff;
  }

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
