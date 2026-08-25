<?php

$ctlServicio = new ControladorServicio();
$HabitacionesServ = $ctlServicio->crtObtenerHabitaciones();

?>
<div class="content-wrapper">

  <section class="content-header">
    <h1>
      Limpieza
    </h1>
    <ol class="breadcrumb">
      <li><a href="inicio"><i class="fa fa-dashboard"></i>Inicio</a></li>
      <li class="active">Limpieza</li>
    </ol>
  </section>

  <section class="content">

    <div class="box serv-box">
      <div class="overlay" id="servOverlay" style="display:none;">
        <i class="fa fa-refresh fa-spin"></i>
      </div>
      <div class="box-body serv-box-body">
        <img src="views/img/Iconos/limpieza.png" alt="" class="serv-icono-grande">
        <p class="serv-intro-texto">Registra el servicio de limpieza de una habitación: inicio, checklist de tareas y evidencia final.</p>
        <button type="button" class="btn sv-btn-realizar btnAbrirRealizarTarea">
          <img src="views/img/Iconos/limpieza.png" alt="" class="sv-icono-boton"> Realizar tarea
        </button>
      </div>
    </div>
  </section>
</div>

<!-- Modal Realizar Tarea -->
<div id="modalRealizarTarea" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" style="background:#3f342e; color:white">
        <button type="button" class="close" data-dismiss="modal" style="color:white; opacity:1;">&times;</button>
        <h4 class="modal-title"><img src="views/img/Iconos/limpieza.png" alt="" class="sv-icono-boton" style="width:16px;height:16px;"> Limpieza — <span id="servModalHabitacion"></span></h4>
      </div>
      <div class="modal-body">

        <input type="hidden" id="servIdHabitacion">
        <input type="hidden" id="servIdServicio">

        <!-- Paso 0: elegir habitación -->
        <div id="servPasoSeleccion">
          <div class="serv-campo-fecha">
            <label>Habitación</label>
            <select class="form-control" id="servSelectHabitacion">
              <option value="">-- Selecciona una habitación --</option>
              <?php foreach ($HabitacionesServ as $hab): ?>
                <option value="<?php echo (int) $hab["Id_Habitacion"]; ?>"><?php echo htmlspecialchars($hab["TipoHabitacion"] ?: $hab["NumeroHabitacion"]); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <!-- Paso 1: iniciar -->
        <div id="servPasoInicio" style="display:none;">
          <div class="serv-campo-fecha">
            <label>Fecha y hora de inicio</label>
            <div class="serv-fecha-valor" id="servFechaInicioValor"></div>
          </div>
          <button type="button" class="btn btn-success serv-btn-comenzar" id="servComenzar">
            <i class="fa fa-play"></i> Comenzar
          </button>
        </div>

        <!-- Paso 2: checklist + evidencia -->
        <div id="servPasoChecklist" style="display:none;">

          <h5 class="serv-seccion-titulo">Tareas</h5>
          <div class="serv-tareas-scroll">
            <table class="table serv-tabla-tareas">
              <tbody id="servTareasCuerpo">
                <tr><td class="text-center text-muted" style="padding:15px;">Cargando…</td></tr>
              </tbody>
            </table>
          </div>

          <h5 class="serv-seccion-titulo">Evidencia final (obligatoria)</h5>
          <input type="file" id="servEvidencia" accept="image/*">
          <img id="servEvidenciaPreview" style="display:none; max-width:100%; max-height:180px; margin-top:10px; border-radius:8px;">

        </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn tar-btn-secondary pull-left" data-dismiss="modal">Salir</button>
        <button type="button" class="btn btn-success" id="servFinalizar" style="display:none;">
          <i class="fa fa-check"></i> Finalizar
        </button>
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

  .serv-box{ border-radius:16px; overflow:hidden; }

  .serv-box-body{
    display:flex;
    flex-direction:column;
    align-items:center;
    text-align:center;
    padding:48px 24px;
  }
  .serv-icono-grande{
    width:56px;
    height:56px;
    object-fit:contain;
    margin-bottom:14px;
    opacity:.85;
  }
  .serv-intro-texto{
    color:#8d7a68;
    max-width:420px;
    margin-bottom:22px;
  }

  .sv-icono-boton{ width:15px; height:15px; object-fit:contain; margin-right:6px; filter:brightness(0) invert(1); }

  .sv-btn-realizar{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    border-radius:10px;
    border:none;
    background:#81412d;
    color:#fff;
    padding:12px 28px;
    font-weight:700;
    font-size:14px;
  }
  .sv-btn-realizar:hover, .sv-btn-realizar:focus{ background:#6e3625; color:#fff; }

  .serv-campo-fecha{ margin-bottom:18px; }
  .serv-campo-fecha label{ display:block; color:#8d7a68; font-size:13px; font-weight:600; margin-bottom:6px; }
  .serv-fecha-valor{
    background:#f4efe4;
    border:1px solid #eee3d2;
    border-radius:8px;
    padding:10px 14px;
    font-weight:700;
    color:#3f342e;
  }
  .serv-btn-comenzar{ width:100%; }

  .serv-seccion-titulo{
    color:#3f342e;
    font-weight:700;
    font-size:14px;
    margin:16px 0 8px;
    border-bottom:1px solid #eee3d2;
    padding-bottom:6px;
  }
  .serv-tareas-scroll{
    max-height:220px;
    overflow-y:auto;
    border:1px solid #eee3d2;
    border-radius:8px;
  }
  .serv-tabla-tareas{ margin-bottom:0; }
  .serv-tabla-tareas td{ vertical-align:middle; border-top:1px solid #f4efe4; }
  .serv-tabla-tareas tr:first-child td{ border-top:none; }
  .serv-tarea-toggle{ width:60px; text-align:right; }

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

  #modalRealizarTarea .modal-content{ border-radius:16px; overflow:hidden; }
  #modalRealizarTarea .btn{ border-radius:8px; }
</style>
