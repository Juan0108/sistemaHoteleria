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
      <div class="box-body">
        <?php if (($_SESSION["Perfil"] ?? "") !== "Administrador"): ?>
          <button type="button" class="btn sv-btn-realizar btnAbrirRealizarTarea">
            <img src="views/img/Iconos/limpieza.png" alt="" class="sv-icono-boton"> Iniciar limpieza
          </button>
        <?php endif; ?>

        <h5 class="serv-seccion-titulo">Historial de limpiezas</h5>

        <?php if (($_SESSION["Perfil"] ?? "") !== "Limpieza"): ?>
        <div class="serv-filtros">
          <div class="serv-filtro-campo">
            <i class="fa fa-bed"></i>
            <select id="servFiltroHabitacion" class="serv-filtro-select">
              <option value="">-- Selecciona una habitación --</option>
              <?php foreach ($HabitacionesServ as $hab): ?>
                <option value="<?php echo (int) $hab["Id_Habitacion"]; ?>"><?php echo htmlspecialchars($hab["TipoHabitacion"] ?: $hab["NumeroHabitacion"]); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="serv-filtro-campo">
            <i class="fa fa-user"></i>
            <select id="servFiltroUsuario" class="serv-filtro-select">
              <option value="">-- Selecciona un usuario --</option>
            </select>
          </div>
          <div class="serv-filtro-campo">
            <i class="fa fa-calendar"></i>
            <input type="text" id="servFiltroFechaDesde" class="serv-filtro-fecha" placeholder="Desde" autocomplete="off" readonly>
          </div>
          <div class="serv-filtro-campo">
            <i class="fa fa-calendar"></i>
            <input type="text" id="servFiltroFechaHasta" class="serv-filtro-fecha" placeholder="Hasta" autocomplete="off" readonly>
          </div>
          <button type="button" class="btn serv-btn-limpiar" id="servLimpiarFiltros">
            <i class="fa fa-times"></i> Limpiar fechas
          </button>
        </div>
        <?php endif; ?>

        <div class="serv-mostrar">
          <span>Mostrar</span>
          <select id="servFiltroCantidad" class="serv-filtro-select">
            <option value="10">10</option>
            <option value="20">20</option>
            <option value="25">25</option>
            <option value="45">45</option>
            <option value="50">50</option>
            <option value="100">100</option>
          </select>
          <span>registros</span>
          <?php if (($_SESSION["Perfil"] ?? "") === "Administrador"): ?>
          <button type="button" class="btn serv-btn-reporte-corte" id="servBtnReporteCorte" title="Manda el corte diario de hoy por WhatsApp a tu teléfono">
            <i class="fa fa-whatsapp"></i> Generar reporte
          </button>
          <?php endif; ?>
        </div>

        <div class="serv-tabla-wrap">
          <table class="serv-tabla-historial">
            <thead>
              <tr>
                <th>#</th>
                <th>Habitación</th>
                <th>Usuario</th>
                <th>Fecha inicio</th>
                <th>Foto inicio</th>
                <th>Fecha fin</th>
                <th>Foto resultado</th>
                <th>Tareas realizadas</th>
                <?php if (($_SESSION["Perfil"] ?? "") !== "Administrador"): ?>
                <th>Acción</th>
                <?php endif; ?>
              </tr>
            </thead>
            <tbody id="servHistorialCuerpo">
              <tr><td colspan="<?php echo (($_SESSION["Perfil"] ?? "") !== "Administrador") ? 9 : 8; ?>" class="text-center text-muted" style="padding:15px;">Cargando…</td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>
</div>

<?php if (($_SESSION["Perfil"] ?? "") !== "Administrador"): ?>
<!-- Modal Realizar Tarea -->
<div id="modalRealizarTarea" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" style="background:#3f342e; color:white">
        <button type="button" class="close" data-dismiss="modal" style="color:white; opacity:1;">&times;</button>
        <h4 class="modal-title serv-modal-title">
          <img src="views/img/Iconos/limpieza.png" alt="" class="sv-icono-boton">
          <span>Limpieza — <span id="servModalHabitacion"></span></span>
        </h4>
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

          <h5 class="serv-seccion-titulo">Foto inicial (obligatoria)</h5>
          <input type="file" id="servFotoInicio" accept="image/*">
          <img id="servFotoInicioPreview" style="display:none; max-width:100%; max-height:180px; margin-top:10px; border-radius:8px;">

          <button type="button" class="btn btn-success serv-btn-comenzar" id="servComenzar" style="margin-top:16px;">
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
<?php endif; ?>

<!-- Modal para ver una foto del historial en grande -->
<div id="modalFotoServicio" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" style="background:#3f342e; color:white">
        <button type="button" class="close" data-dismiss="modal" style="color:white; opacity:1;">&times;</button>
        <h4 class="modal-title" id="servFotoModalTitulo"><i class="fa fa-camera"></i> Foto</h4>
      </div>
      <div class="modal-body text-center">
        <img id="servFotoModalImg" style="max-width:100%; border-radius:8px;">
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

  .sv-icono-boton{ width:15px; height:15px; object-fit:contain; margin-right:6px; filter:brightness(0) invert(1); }

  .serv-modal-title{ display:flex; align-items:center; }
  .serv-modal-title img{ margin:0 8px 0 0; }

  .sv-btn-realizar{
    display:inline-flex;
    margin-bottom:10px;
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
  .serv-tabla-tareas{ margin-bottom:0; table-layout:fixed; width:100%; }
  .serv-tabla-tareas td{ vertical-align:middle; border-top:1px solid #f4efe4; }
  .serv-tabla-tareas tr:first-child td{ border-top:none; }
  .serv-tarea-num{ width:26px; color:#9c8a76; font-weight:700; white-space:nowrap; }
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
  #modalFotoServicio .modal-content{ border-radius:16px; overflow:hidden; }

  .serv-filtros{ display:flex; flex-wrap:wrap; gap:10px; margin-bottom:14px; }
  .serv-filtro-campo{
    display:flex;
    align-items:center;
    gap:8px;
    background:#fdfaf5;
    border:1px solid #eee3d2;
    border-radius:10px;
    padding:8px 14px;
  }
  .serv-filtro-campo i{ color:#81412d; }
  .serv-filtro-select, .serv-filtro-fecha{
    border:none;
    background:transparent;
    cursor:pointer;
    color:#3f342e;
    font-weight:600;
    font-size:13px;
    outline:none;
  }
  .serv-filtro-select{ min-width:170px; }
  .serv-mostrar{
    display:flex;
    align-items:center;
    gap:8px;
    margin-bottom:12px;
    font-size:13px;
    color:#5c4a3a;
    font-weight:600;
  }
  .serv-mostrar #servFiltroCantidad{
    min-width:0;
    border:1px solid #eee3d2;
    border-radius:8px;
    padding:6px 10px;
    background:#fdfaf5;
    color:#3f342e;
    font-weight:600;
    cursor:pointer;
  }
  .serv-btn-reporte-corte{
    margin-left:auto;
    display:inline-flex;
    align-items:center;
    gap:6px;
    background:#4c8c5a;
    border:1px solid #4c8c5a;
    color:#fff;
    border-radius:8px;
    font-size:13px;
    font-weight:600;
    padding:7px 14px;
  }
  .serv-btn-reporte-corte:hover, .serv-btn-reporte-corte:focus{ background:#3f7649; border-color:#3f7649; color:#fff; }
  .serv-btn-limpiar{
    display:inline-flex;
    align-items:center;
    gap:6px;
    background:#fff;
    border:1px solid #eee3d2;
    border-radius:10px;
    color:#3f342e;
    font-weight:600;
    font-size:13px;
    padding:8px 14px;
  }
  .serv-btn-limpiar:hover, .serv-btn-limpiar:focus{ background:#f4efe4; color:#3f342e; }

  .serv-tabla-wrap{ border:1px solid #eee3d2; border-radius:10px; overflow-x:auto; overflow-y:hidden; }
  .serv-tabla-historial{ width:100%; border-collapse:collapse; background:#fff; margin-bottom:0; }
  .serv-tabla-historial thead th{ background:#3f342e; color:#fff; text-align:left; font-size:11px; text-transform:uppercase; letter-spacing:.3px; padding:10px 12px; white-space:nowrap; }
  .serv-tabla-historial td{ padding:10px 12px; font-size:13px; color:#3f342e; border-top:1px solid #eee3d2; }
  .serv-tabla-historial tbody tr:nth-child(even){ background:#f8f4ea; }
  .serv-ver-foto{ background:none; border:none; color:#81412d; font-size:12px; font-weight:700; padding:0; text-decoration:underline; }
  .serv-ver-foto:hover{ color:#6e3625; }
  .serv-sin-dato{ color:#8d7a68; font-style:italic; }
</style>
