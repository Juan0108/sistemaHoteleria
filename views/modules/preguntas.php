<?php
?>
<div class="content-wrapper">

  <section class="content-header">
    <h1>
      Preguntas
    </h1>
    <ol class="breadcrumb">
      <li><a href="inicio"><i class="fa fa-dashboard"></i>Inicio</a></li>
      <li class="active">Preguntas</li>
    </ol>
  </section>

  <section class="content">

    <div class="box preg-box">
      <div class="overlay" id="pregOverlay" style="display:none;">
        <i class="fa fa-refresh fa-spin"></i>
      </div>
      <div class="box-header with-border">
        <button type="button" class="btn preg-btn-primary" data-toggle="modal" data-target="#modalAgregarPregunta">
          <i class="fa fa-plus"></i> Agregar Pregunta
        </button>
        <span class="preg-contador" id="pregContador">0 preguntas cargadas</span>
      </div>

      <div class="box-body">
        <table class="table table-bordered table-striped tablaPreguntas">
          <thead>
            <tr>
              <th style="width:60px;">#</th>
              <th>Pregunta</th>
              <th style="width:140px;">Estatus</th>
              <th style="width:220px;">Acciones</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </section>
</div>

<!-- Modal Agregar Pregunta -->
<div id="modalAgregarPregunta" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" style="background:#3f342e; color:white">
        <button type="button" class="close" data-dismiss="modal" style="color:white; opacity:1;">&times;</button>
        <h4 class="modal-title"><i class="fa fa-question-circle"></i> Agregar Pregunta</h4>
      </div>
      <div class="modal-body">
        <form id="formAgregarPregunta" autocomplete="off">
          <div class="form-group">
            <label>Pregunta</label>
            <textarea class="form-control" id="pregTexto" name="pregunta" rows="3" maxlength="255" placeholder="Escribe la pregunta de checkout" style="resize:none;" required></textarea>
            <span class="help-block small text-muted" id="pregContadorCaracteres">0/255 caracteres</span>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn preg-btn-secondary pull-left" data-dismiss="modal">Salir</button>
        <button type="button" class="btn btn-success" id="pregGuardar">Guardar Pregunta</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Editar Pregunta -->
<div id="modalEditarPregunta" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" style="background:#3f342e; color:white">
        <button type="button" class="close" data-dismiss="modal" style="color:white; opacity:1;">&times;</button>
        <h4 class="modal-title"><i class="fa fa-question-circle"></i> Editar Pregunta</h4>
      </div>
      <div class="modal-body">
        <form id="formEditarPregunta" autocomplete="off">
          <input type="hidden" id="pregEditarId">
          <div class="form-group">
            <label>Pregunta</label>
            <textarea class="form-control" id="pregEditarTexto" rows="3" maxlength="255" style="resize:none;" required></textarea>
            <span class="help-block small text-muted" id="pregEditarContadorCaracteres">0/255 caracteres</span>
          </div>
          <div class="form-group">
            <label style="margin-right:10px;">Estatus de la pregunta</label>
            <style>
              .toggle.ios, .toggle-on.ios, .toggle-off.ios { border-radius: 20rem; }
              .toggle.ios .toggle-handle { border-radius: 20rem; }
            </style>
            <input type="checkbox" id="pregEditarEstatus" data-style="ios" data-toggle="toggle" data-onstyle="success" data-offstyle="danger" data-on="Activa" data-off="Inhabilitada" data-width="100">
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn preg-btn-secondary pull-left" data-dismiss="modal">Salir</button>
        <button type="button" class="btn btn-success" id="pregGuardarEdicion">Modificar Pregunta</button>
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

  .preg-box{ border-radius:16px; overflow:hidden; }
  .preg-box .box-header{
    display:flex;
    align-items:center;
    gap:16px;
    flex-wrap:wrap;
  }

  .preg-btn-primary{
    background:#81412d;
    border-color:#81412d;
    color:#fff;
    border-radius:8px;
  }
  .preg-btn-primary:hover, .preg-btn-primary:focus{
    background:#6e3625;
    border-color:#6e3625;
    color:#fff;
  }
  .preg-btn-secondary{
    background:#fff;
    border:1px solid #3f342e;
    color:#3f342e;
    border-radius:8px;
  }
  .preg-btn-secondary:hover, .preg-btn-secondary:focus{
    background:#f2ede6;
    border-color:#3f342e;
    color:#3f342e;
  }

  .preg-contador{
    font-weight:700;
    font-size:13px;
    color:#8d7a68;
    background:#f4efe4;
    border:1px solid #eee3d2;
    border-radius:999px;
    padding:5px 14px;
  }

  .tablaPreguntas thead th{
    background:#3f342e;
    color:#fff;
    border-color:#3f342e;
  }

  .preg-badge{
    display:inline-block;
    padding:4px 14px;
    border-radius:999px;
    font-size:11px;
    font-weight:700;
    color:#fff;
  }
  .preg-badge-activa{ background:#81412d; }
  .preg-badge-inhabilitada{ background:#9c9088; }

  .btnCambiarEstatusPregunta{
    border-radius:8px !important;
    color:#fff;
    border:none;
  }
  .btnCambiarEstatusPregunta.deshabilitar{
    background:#b96a37;
    border-color:#b96a37;
  }
  .btnCambiarEstatusPregunta.deshabilitar:hover{ background:#9c5a2e; }
  .btnCambiarEstatusPregunta.habilitar{
    background:#4c8c5a;
    border-color:#4c8c5a;
  }
  .btnCambiarEstatusPregunta.habilitar:hover{ background:#3f7549; }

  #modalAgregarPregunta .modal-content{
    border-radius:16px;
    overflow:hidden;
  }
  #modalAgregarPregunta .btn{ border-radius:8px; }
  #modalAgregarPregunta .form-control{
    border-radius:8px;
    border-color:#e4d9c8;
  }
  #modalAgregarPregunta .btn-success,
  #modalEditarPregunta .btn-success{
    background:#81412d;
    border-color:#81412d;
  }
  #modalAgregarPregunta .btn-success:hover,
  #modalEditarPregunta .btn-success:hover{
    background:#6e3625;
    border-color:#6e3625;
  }

  #modalEditarPregunta .modal-content{
    border-radius:16px;
    overflow:hidden;
  }
  #modalEditarPregunta .btn{ border-radius:8px; }
  #modalEditarPregunta .form-control{
    border-radius:8px;
    border-color:#e4d9c8;
  }
  #modalEditarPregunta .toggle-on.btn-success{
    background:#81412d !important;
    border-color:#81412d !important;
    color:#fff !important;
  }
  #modalEditarPregunta .toggle-off.btn-danger{
    background:#9c9088 !important;
    border-color:#9c9088 !important;
    color:#fff !important;
  }

  .btnEditarPregunta{
    background:#3f342e;
    border-color:#3f342e;
    color:#fff;
    border-radius:8px !important;
  }
  .btnEditarPregunta:hover, .btnEditarPregunta:focus{
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
