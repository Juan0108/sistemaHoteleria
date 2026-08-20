<?php

$fechaHoy = date("Y-m-d");
$fechaMinima = $fechaHoy;

$ctlRecepcion = new ControladorHabitaciones();
$HabitacionesRecepcion = $ctlRecepcion->crtObtenerHabitacionesRecepcion($fechaHoy);

?>
<div class="content-wrapper">

  <section class="content-header">
    <h1>
      Recepción
    </h1>
    <ol class="breadcrumb">
      <li><a href="inicio"><i class="fa fa-dashboard"></i>Inicio</a></li>
      <li class="active">Recepción</li>
    </ol>
  </section>

  <section class="content">

    <div class="box recepcion-box">
      <div class="overlay" id="recepcionOverlay" style="display:none;">
        <i class="fa fa-refresh fa-spin"></i>
      </div>
      <div class="box-header with-border">
        <div class="input-group input-group-sm recepcion-fecha-group" style="width:200px;">
          <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
          <input type="text" class="form-control recepcion-datepicker" id="recepcionFecha"
                 value="<?php echo $fechaHoy; ?>" data-min-date="<?php echo $fechaMinima; ?>" readonly>
        </div>
        <div class="input-group input-group-sm recepcion-busqueda-group" style="width:280px; margin-left:10px;">
          <span class="input-group-addon"><i class="fa fa-search"></i></span>
          <input type="text" class="form-control" id="recepcionBusqueda"
                 placeholder="Buscar folio o cliente">
        </div>
        <span class="recepcion-actualizado text-muted small" style="margin-left:10px;"></span>
      </div>
      <div class="box-body">

        <div class="recepcion-legend">
          <span><i class="dot dot-ocupada"></i> Ocupada</span>
          <span><i class="dot dot-reservada"></i> Reservada</span>
          <span><i class="dot dot-disponible"></i> Disponible</span>
        </div>

        <div class="recepcion-contenido">
          <?php if (count($HabitacionesRecepcion) === 0): ?>

            <div class="callout callout-info recepcion-empty">
              <p>No hay habitaciones disponibles para este día. Las habitaciones inhabilitadas en el módulo de Habitaciones no se muestran aquí.</p>
            </div>

          <?php else: ?>

            <div class="recepcion-grid">
              <?php foreach ($HabitacionesRecepcion as $hab):
                $horasExtras = (int) $hab["HorasExtras"];
                $horaAnticipada = (int) $hab["HoraAnticipada"];
                $tsSalidaReal = $hab["FechaSalida"] ? strtotime($hab["FechaSalida"]) + ($horasExtras * 3600) : null;
                $tsEntradaReal = $hab["FechaEntrada"] ? strtotime($hab["FechaEntrada"]) - ($horaAnticipada * 3600) : null;

                $tituloPill = "";
                if ($hab["FechaEntrada"] && $hab["FechaSalida"]) {
                  $tituloPill = "Entrada: " . date("d/m/Y g:i a", $tsEntradaReal)
                              . " · Salida: " . date("d/m/Y g:i a", $tsSalidaReal);
                }
              ?>
                <div class="habitacion-card">
                  <div class="hc-top">
                    <span class="hc-status-pill estado-<?php echo $hab["EstadoClase"]; ?>" title="<?php echo htmlspecialchars($tituloPill); ?>">
                      <i class="fa <?php echo $hab["EstadoIcono"]; ?>"></i> <?php echo $hab["EstadoTexto"]; ?>
                    </span>
                    <div class="hc-badges">
                      <?php if ($horasExtras > 0): ?>
                        <span class="hc-horas-extra" title="Incluye <?php echo $horasExtras; ?>h extra">
                          <i class="fa fa-clock-o"></i> +<?php echo $horasExtras; ?>h
                        </span>
                      <?php endif; ?>
                      <?php if ($horaAnticipada > 0): ?>
                        <span class="hc-hora-anticipada" title="Llegada anticipada">
                          <i class="fa fa-history"></i> Anticipada <?php echo $horaAnticipada; ?>h
                        </span>
                      <?php endif; ?>
                    </div>
                  </div>
                  <?php if ((int) $hab["ReservasProximas"] > 0): ?>
                    <div class="hc-reservas-proximas" title="Reservaciones próximas para esta habitación">
                      <i class="fa fa-calendar"></i> <?php echo (int) $hab["ReservasProximas"]; ?> <?php echo (int) $hab["ReservasProximas"] === 1 ? "reserva próxima" : "reservas próximas"; ?>
                    </div>
                  <?php endif; ?>
                  <?php if ($hab["EstadoClase"] !== "disponible" || (int) $hab["ReservasProximas"] > 0): ?>
                    <button type="button" class="hc-ver-reservas" title="Ver todas las reservas de esta habitación"
                            data-id-habitacion="<?php echo (int) $hab["Id_Habitacion"]; ?>"
                            data-tipo-habitacion="<?php echo htmlspecialchars($hab["TipoHabitacion"]); ?>">
                      <i class="fa fa-list-alt"></i> Ver reservas
                    </button>
                  <?php endif; ?>
                  <div class="hc-bed"><i class="fa fa-bed"></i></div>
                  <?php if ($hab["NombreCliente"]): ?>
                    <div class="hc-cliente"><i class="fa fa-user-o"></i> <?php echo htmlspecialchars($hab["NombreCliente"]); ?></div>
                    <?php if ($hab["FechaEntrada"] && $hab["FechaSalida"]): ?>
                      <div class="hc-stay">
                        <div class="hc-stay-pair">
                          <span class="hc-stay-lbl">Entrada</span>
                          <span class="hc-stay-val<?php echo $horaAnticipada > 0 ? ' hc-stay-val-anticipada' : ''; ?>"
                                <?php if ($horaAnticipada > 0): ?>title="Incluye <?php echo $horaAnticipada; ?>h anticipada"<?php endif; ?>>
                            <?php echo date("d/m g:i a", $tsEntradaReal); ?>
                          </span>
                        </div>
                        <span class="hc-stay-arrow"><i class="fa fa-long-arrow-right"></i></span>
                        <div class="hc-stay-pair">
                          <span class="hc-stay-lbl">Salida</span>
                          <span class="hc-stay-val<?php echo $horasExtras > 0 ? ' hc-stay-val-extra' : ''; ?>"
                                <?php if ($horasExtras > 0): ?>title="Incluye <?php echo $horasExtras; ?>h extra"<?php endif; ?>>
                            <?php echo date("d/m g:i a", $tsSalidaReal); ?>
                          </span>
                        </div>
                      </div>
                    <?php endif; ?>
                  <?php elseif ($hab["ProximaReservacion"]): ?>
                    <div class="hc-proxima" title="Horas de margen: <?php echo (int) $hab["ProximaReservacion"]["horasDisponibles"]; ?>h">
                      <i class="fa fa-clock-o"></i> Disponible hasta el <?php echo date("d/m/Y g:i a", strtotime($hab["ProximaReservacion"]["fechaEntrada"])); ?>
                    </div>
                  <?php endif; ?>
                  <div class="hc-footer">
                    <span class="hc-info-icon"
                          data-nombre="<?php echo htmlspecialchars($hab["TipoHabitacion"]); ?>"
                          data-descripcion="<?php echo htmlspecialchars($hab["Descripcion"]); ?>"
                          data-capacidad="<?php echo htmlspecialchars($hab["Capacidad"]); ?>"
                          data-precio="<?php echo htmlspecialchars(number_format((float) $hab["PrecioNoche"], 2)); ?>"
                          data-foto="<?php echo htmlspecialchars($hab["Foto"]); ?>"
                          title="Ver detalles">
                      <i class="fa fa-question"></i>
                    </span>
                    <div class="hc-nombre"><?php echo htmlspecialchars($hab["TipoHabitacion"]); ?></div>
                    <?php if ($hab["PuedeCheckin"]): ?>
                      <button type="button" class="hc-icon-btn checkin" title="Confirmar check-in"
                              data-id-reservacion="<?php echo htmlspecialchars($hab["Id_Reservacion"]); ?>"
                              data-tipo-habitacion="<?php echo htmlspecialchars($hab["TipoHabitacion"]); ?>">
                        <i class="fa fa-sign-in"></i>
                      </button>
                    <?php endif; ?>
                    <?php if ($hab["EstadoClase"] === "ocupada" || $hab["EstadoClase"] === "reservada"): ?>
                      <button type="button" class="hc-icon-btn cancelar"
                              title="<?php echo $hab["EstadoClase"] === "ocupada" ? "Cancelar estadía" : "Cancelar reservación"; ?>"
                              data-id-reservacion="<?php echo htmlspecialchars($hab["Id_Reservacion"]); ?>"
                              data-tipo-habitacion="<?php echo htmlspecialchars($hab["TipoHabitacion"]); ?>"
                              data-estado="<?php echo $hab["EstadoClase"]; ?>">
                        <i class="fa fa-times"></i>
                      </button>
                    <?php endif; ?>
                    <span class="hc-arrow" title="Nueva reservación"
                          data-id-habitacion="<?php echo (int) $hab["Id_Habitacion"]; ?>"
                          data-tipo-habitacion="<?php echo htmlspecialchars($hab["TipoHabitacion"]); ?>"
                          data-precio-noche="<?php echo htmlspecialchars((float) $hab["PrecioNoche"]); ?>">
                      <i class="fa fa-arrow-circle-right"></i>
                    </span>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>

          <?php endif; ?>
        </div>

      </div>
    </div>
  </section>
</div>

<!-- Modal Detalle de Habitación  -->
<div id="modalDetalleHabitacion" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" style="background:#3f342e; color:white">
        <button type="button" class="close" data-dismiss="modal" style="color:white; opacity:1;">&times;</button>
        <h4 class="modal-title" id="detalleHabNombre"><i class="fa fa-bed"></i></h4>
      </div>
      <div class="modal-body">
        <img id="detalleHabFoto" src="" alt="Foto de la habitación" style="width:100%; max-height:320px; object-fit:cover; border-radius:6px; margin-bottom:15px; display:none;" onerror="mostrarErrorFotoRecepcion()">
        <p id="detalleHabSinFoto" class="text-muted" style="display:none;"><i class="fa fa-exclamation-triangle"></i> No se encontró la foto de esta habitación.</p>

        <p><strong>Descripción:</strong> <span id="detalleHabDescripcion"></span></p>
        <p><strong>Capacidad:</strong> <span id="detalleHabCapacidad"></span> personas</p>
        <p><strong>Precio por noche:</strong> $<span id="detalleHabPrecio"></span></p>
      </div>
    </div>
  </div>
</div>

<!-- Modal Historial de Reservas -->
<div id="modalHistorialReservas" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" style="background:#3f342e; color:white">
        <button type="button" class="close" data-dismiss="modal" style="color:white; opacity:1;">&times;</button>
        <h4 class="modal-title"><i class="fa fa-list-alt"></i> Próximas reservas — <span id="hrHabitacionNombre"></span></h4>
      </div>
      <div class="modal-body">
        <div id="hrContenido">
          <p class="text-muted text-center" style="padding:20px;">Cargando…</p>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Nueva Reservación -->
<div id="modalNuevaReservacion" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" style="background:#3f342e; color:white">
        <button type="button" class="close" data-dismiss="modal" style="color:white; opacity:1;">&times;</button>
        <h4 class="modal-title"><i class="fa fa-calendar-plus-o"></i> Nueva reservación — <span id="nrHabitacionNombre"></span></h4>
      </div>
      <div class="modal-body">
        <form id="formNuevaReservacion">
          <input type="hidden" id="nrIdHabitacion">
          <input type="hidden" id="nrPrecioNoche">
          <input type="hidden" id="nrIdClienteSeleccionado" value="">

          <div class="form-group">
            <label>Cliente</label>
            <div class="input-group">
              <span class="input-group-addon"><i class="fa fa-search"></i></span>
              <input type="text" class="form-control" id="nrBuscarCliente" placeholder="Buscar cliente por nombre o teléfono" autocomplete="off">
            </div>
            <div id="nrResultadosCliente" class="nr-resultados-cliente" style="display:none;"></div>
            <div id="nrClienteSeleccionadoTexto" class="nr-cliente-chip" style="display:none;">
              <i class="fa fa-user"></i> <span id="nrClienteSeleccionadoNombre"></span>
              <a href="#" id="nrQuitarCliente" title="Quitar"><i class="fa fa-times"></i></a>
            </div>
            <a href="#" id="nrToggleClienteNuevo" class="nr-toggle-nuevo"><i class="fa fa-plus"></i> Cliente nuevo</a>
          </div>

          <div id="nrClienteNuevoCampos" style="display:none;">
            <div class="row">
              <div class="col-xs-6">
                <div class="form-group">
                  <label>Nombre</label>
                  <input type="text" class="form-control" id="nrNombre">
                </div>
              </div>
              <div class="col-xs-6">
                <div class="form-group">
                  <label>Apellido paterno</label>
                  <input type="text" class="form-control" id="nrApaterno">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-xs-6">
                <div class="form-group">
                  <label>Apellido materno</label>
                  <input type="text" class="form-control" id="nrAmaterno">
                </div>
              </div>
              <div class="col-xs-6">
                <div class="form-group">
                  <label>Teléfono</label>
                  <input type="tel" class="form-control" id="nrTelefono" maxlength="10" inputmode="numeric" autocomplete="off">
                  <span id="nrTelefonoAdvertencia" class="nr-telefono-advertencia" style="display:none;"></span>
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-xs-6">
              <div class="form-group">
                <label>Entrada</label>
                <div class="nr-datetime-row">
                  <input type="date" class="form-control" id="nrFechaEntradaDia">
                  <select class="form-control" id="nrFechaEntradaHora"></select>
                  <span class="nr-datetime-sep">:</span>
                  <select class="form-control" id="nrFechaEntradaMin"></select>
                </div>
                <input type="hidden" id="nrFechaEntrada">
              </div>
            </div>
            <div class="col-xs-6">
              <div class="form-group">
                <label>Salida</label>
                <div class="nr-datetime-row">
                  <input type="date" class="form-control" id="nrFechaSalidaDia">
                  <select class="form-control" id="nrFechaSalidaHora"></select>
                  <span class="nr-datetime-sep">:</span>
                  <select class="form-control" id="nrFechaSalidaMin"></select>
                </div>
                <input type="hidden" id="nrFechaSalida">
              </div>
            </div>
          </div>

          <div class="form-group">
            <label>Precio</label>
            <div class="input-group">
              <span class="input-group-addon">$</span>
              <input type="number" step="0.01" min="0" class="form-control" id="nrPrecio" readonly>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn" id="nrGuardar" style="background:#81412d; border-color:#81412d; color:#fff;">Guardar reservación</button>
      </div>
    </div>
  </div>
</div>

<style>
  .content-wrapper{ background:#f2ece0; }

  .datepicker.datepicker-dropdown{
    border:1px solid #e4d9c8;
    border-radius:10px;
    box-shadow:0 8px 24px rgba(63,52,46,.14);
    padding:6px;
  }
  .datepicker table tr td, .datepicker table tr th{
    font-size:12.5px;
  }
  .datepicker th.dow{
    color:#8a7c6d;
    font-weight:700;
    text-transform:uppercase;
    font-size:11px;
  }
  .datepicker .datepicker-switch,
  .datepicker .prev,
  .datepicker .next{
    color:#3f342e;
    font-weight:700;
  }
  .datepicker .prev:hover,
  .datepicker .next:hover,
  .datepicker .datepicker-switch:hover{
    background:#f2ede6;
  }
  .datepicker table tr td.day{
    color:#3f342e;
  }
  .datepicker table tr td.day:hover{
    background:#f2ede6;
    border-radius:6px;
    cursor:pointer;
  }
  .datepicker table tr td.old,
  .datepicker table tr td.new{
    color:#dcd2c2;
  }
  .datepicker table tr td.disabled,
  .datepicker table tr td.disabled:hover{
    color:#dcd2c2 !important;
    background:none !important;
    cursor:not-allowed;
    text-decoration:line-through;
  }
  .datepicker table tr td.today,
  .datepicker table tr td.today:hover,
  .datepicker table tr td.today.disabled,
  .datepicker table tr td.today.disabled:hover{
    background:#dfc6a2 !important;
    background-image:none !important;
    color:#3f342e !important;
    border-radius:6px;
    text-shadow:none;
  }
  .datepicker table tr td.active,
  .datepicker table tr td.active:hover,
  .datepicker table tr td.active.disabled,
  .datepicker table tr td.active.disabled:hover{
    background:#81412d !important;
    background-image:none !important;
    color:#fff !important;
    border-radius:6px;
    text-shadow:none;
  }
  .content-header h1{
    color:#3f342e;
    font-weight:800;
    letter-spacing:1px;
    text-transform:uppercase;
  }

  .recepcion-box{ border-radius:16px; overflow:hidden; }

  .recepcion-box .box-header{
    display:flex;
    align-items:center;
    flex-wrap:wrap;
  }

  .recepcion-busqueda-group .input-group-addon{
    border-top-left-radius:8px;
    border-bottom-left-radius:8px;
    background:#f2ede6;
    color:#81412d;
    border-color:#e4d9c8;
  }
  .recepcion-busqueda-group .form-control{
    border-top-right-radius:8px;
    border-bottom-right-radius:8px;
    border-color:#e4d9c8;
  }

  .recepcion-fecha-group .input-group-addon{
    border-top-left-radius:8px;
    border-bottom-left-radius:8px;
    background:#f2ede6;
    color:#81412d;
    border-color:#e4d9c8;
  }
  .recepcion-fecha-group .form-control{
    border-top-right-radius:8px;
    border-bottom-right-radius:8px;
    border-color:#e4d9c8;
  }

  #modalDetalleHabitacion .modal-content{
    border-radius:16px;
    overflow:hidden;
  }

  #modalNuevaReservacion .modal-content{
    border-radius:16px;
    overflow:hidden;
  }

  #modalHistorialReservas .modal-content{
    border-radius:16px;
    overflow:hidden;
  }
  .hr-tabla{
    width:100%;
    border-collapse:collapse;
    font-size:13px;
  }
  .hr-tabla th{
    text-align:left;
    padding:8px 10px;
    background:#f2ede6;
    color:#3f342e;
    font-size:11px;
    font-weight:700;
    letter-spacing:.04em;
    text-transform:uppercase;
  }
  .hr-tabla td{
    padding:9px 10px;
    border-top:1px solid #eee2d3;
    color:#3f342e;
  }
  .hr-estado{
    display:inline-flex;
    align-items:center;
    padding:2px 10px;
    border-radius:999px;
    font-size:11px;
    font-weight:700;
    color:#fff;
    white-space:nowrap;
  }
  .hr-estado.estado-ocupada{ background:#81412d; }
  .hr-estado.estado-reservada{ background:#b96a37; }
  .hr-estado.estado-cancelada{ background:#9a3b2c; }
  .hr-estado.estado-otro{ background:#a8987f; }
  .nr-resultados-cliente{
    border:1px solid #e4d9c8;
    border-top:none;
    border-radius:0 0 6px 6px;
    max-height:160px;
    overflow-y:auto;
    background:#fff;
  }
  .nr-resultados-cliente .nr-resultado-item{
    padding:8px 12px;
    cursor:pointer;
    font-size:13px;
  }
  .nr-resultados-cliente .nr-resultado-item:hover{
    background:#f2ede6;
  }
  .nr-cliente-chip{
    display:inline-flex;
    align-items:center;
    gap:8px;
    background:#f2ede6;
    color:#3f342e;
    padding:6px 12px;
    border-radius:999px;
    font-size:13px;
    font-weight:600;
    margin-top:8px;
  }
  .nr-cliente-chip a{
    color:#81412d;
  }
  .nr-toggle-nuevo{
    display:inline-block;
    margin-top:8px;
    font-size:12px;
    color:#81412d;
    font-weight:600;
  }
  .nr-telefono-advertencia{
    display:block;
    margin-top:4px;
    font-size:12px;
    font-weight:600;
    color:#a94442;
  }
  .nr-datetime-row{
    display:flex;
    align-items:center;
    gap:4px;
  }
  .nr-datetime-row input[type="date"]{
    flex:1 1 auto;
    min-width:0;
    padding-left:6px;
    padding-right:6px;
  }
  .nr-datetime-row select{
    flex:0 0 52px;
    padding-left:4px;
    padding-right:4px;
    text-align:center;
  }
  .nr-datetime-sep{
    color:#8a7c6d;
    font-weight:700;
  }

  .recepcion-legend{
    display:flex;
    gap:24px;
    align-items:center;
    margin-bottom:20px;
  }
  .recepcion-legend span{
    display:inline-flex;
    align-items:center;
    gap:8px;
    font-weight:600;
    font-size:13px;
    color:#555;
  }
  .recepcion-legend .dot{
    width:14px;
    height:14px;
    border-radius:4px;
    display:inline-block;
  }
  .dot-ocupada{ background:#81412d; }
  .dot-reservada{ background:#b96a37; }
  .dot-disponible{ background:#f2ede6; border:1px solid #cdbfa9; }

  .recepcion-grid{
    display:grid;
    grid-template-columns:repeat(auto-fill, minmax(280px, 1fr));
    gap:20px;
  }
  .habitacion-card{
    background:#fff;
    border:1px solid #eee2d3;
    border-radius:20px;
    padding:18px 18px 16px;
    min-height:180px;
    display:flex;
    flex-direction:column;
    justify-content:space-between;
    color:#3f342e;
    box-shadow:0 1px 2px rgba(63,52,46,.06), 0 8px 24px rgba(63,52,46,.07);
    overflow:hidden;
  }

  .hc-top{
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:8px;
  }
  .hc-badges{
    display:flex;
    align-items:center;
    gap:6px;
    flex-wrap:wrap;
    justify-content:flex-end;
  }
  .hc-horas-extra{
    display:inline-flex;
    align-items:center;
    gap:4px;
    padding:3px 10px;
    border-radius:999px;
    font-size:11px;
    font-weight:700;
    background:#fbe7c6;
    color:#8a5a00;
    white-space:nowrap;
  }
  .hc-hora-anticipada{
    display:inline-flex;
    align-items:center;
    gap:4px;
    padding:3px 10px;
    border-radius:999px;
    font-size:11px;
    font-weight:700;
    background:#dcebf1;
    color:#2f5f7a;
    white-space:nowrap;
  }
  .hc-reservas-proximas{
    display:inline-flex;
    align-items:center;
    gap:4px;
    align-self:flex-start;
    margin-top:6px;
    padding:3px 10px;
    border-radius:999px;
    font-size:11px;
    font-weight:700;
    background:#e6e0d3;
    color:#5c4d3d;
    white-space:nowrap;
  }
  .hc-ver-reservas{
    display:flex;
    align-items:center;
    justify-content:center;
    gap:5px;
    margin-top:6px;
    font-size:11px;
    font-weight:700;
    color:#a0906f;
    cursor:pointer;
    background:none;
    border:none;
    padding:0;
  }
  .hc-ver-reservas:hover{ color:#81412d; }
  .hc-ver-reservas i{ font-size:11px; }
  .hc-status-pill{
    display:inline-flex;
    align-items:center;
    gap:6px;
    padding:4px 14px;
    border-radius:999px;
    font-size:11px;
    font-weight:700;
    color:#fff;
  }
  .hc-status-pill.estado-ocupada{ background:#81412d; }
  .hc-status-pill.estado-reservada{ background:#b96a37; }
  .hc-status-pill.estado-disponible{ background:#f2ede6; color:#3f342e; border:1px solid #cdbfa9; }

  .hc-bed{
    text-align:center;
    font-size:37px;
    opacity:.8;
    margin:8px 0 4px;
    color:#3f342e;
  }
  .hc-cliente{
    display:flex;
    align-items:center;
    justify-content:center;
    gap:6px;
    text-align:center;
    font-size:12.5px;
    font-weight:600;
    color:#b96a37;
    margin-bottom:8px;
    white-space:nowrap;
    overflow:hidden;
    text-overflow:ellipsis;
  }
  .hc-cliente i{
    color:#b96a37;
    font-size:11px;
  }
  .hc-stay{
    display:flex;
    align-items:center;
    justify-content:center;
    flex-wrap:wrap;
    gap:6px 10px;
    margin-bottom:14px;
  }
  .hc-stay-pair{ text-align:center; }
  .hc-stay-lbl{
    display:block;
    font-size:10px;
    font-weight:700;
    letter-spacing:.07em;
    text-transform:uppercase;
    color:#b8ab99;
  }
  .hc-stay-val{
    display:block;
    font-size:12.5px;
    font-weight:600;
    color:#3f342e;
  }
  .hc-stay-val-extra{
    display:inline-block;
    background:#fbe7c6;
    color:#8a5a00;
    padding:1px 8px;
    border-radius:6px;
  }
  .hc-stay-val-anticipada{
    display:inline-block;
    background:#dcebf1;
    color:#2f5f7a;
    padding:1px 8px;
    border-radius:6px;
  }
  .hc-stay-arrow{ color:#b8ab99; font-size:12px; }
  .hc-proxima{
    display:flex;
    align-items:center;
    justify-content:center;
    gap:6px;
    text-align:center;
    font-size:11.5px;
    font-weight:600;
    color:#a89a86;
    margin-bottom:14px;
    line-height:1.35;
    padding:0 4px;
  }
  .hc-proxima i{ flex-shrink:0; margin-top:2px; }
  .hc-footer{
    display:flex;
    align-items:center;
    gap:6px;
    margin-top:4px;
    padding-top:14px;
    border-top:1px solid #eee2d3;
  }
  .hc-info-icon{
    flex-shrink:0;
    width:26px;
    height:26px;
    border-radius:50%;
    background:#f2ede6;
    color:#81412d;
    display:flex;
    align-items:center;
    justify-content:center;
    cursor:pointer;
    font-size:12px;
  }
  .hc-nombre{
    flex-grow:1;
    min-width:0;
    font-size:17px;
    font-weight:700;
    color:#3f342e;
    white-space:nowrap;
    overflow:hidden;
    text-overflow:ellipsis;
  }
  .hc-arrow{
    flex-shrink:0;
    width:30px;
    height:30px;
    border-radius:50%;
    background:#f2ede6;
    color:#81412d;
    display:flex;
    align-items:center;
    justify-content:center;
    cursor:pointer;
    font-size:13px;
  }
  .hc-icon-btn{
    flex-shrink:0;
    width:30px;
    height:30px;
    border-radius:50%;
    border:none;
    display:flex;
    align-items:center;
    justify-content:center;
    cursor:pointer;
    font-size:13px;
    transition:transform .12s ease;
  }
  .hc-icon-btn:hover{ transform:translateY(-1px); }
  .hc-icon-btn:active{ transform:translateY(0); }
  .hc-icon-btn.checkin{ background:#e1ece2; color:#3f6b4a; }
  .hc-icon-btn.cancelar{ background:#f7e2dc; color:#9a3b2c; }
</style>
