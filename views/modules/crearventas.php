<style>
  /* Ocultar el contenido de las pestañas deshabilitadas */
  .disabled-tab-content {
    display: none;
  }

  /* Deshabilitar la apariencia de las pestañas deshabilitadas */
  .disabled-tab a {
    pointer-events: none;
    cursor: not-allowed;
    color: #ccc; /* Opcional: color de texto deshabilitado */
  }

.content-wrapper.crear-venta{
  --sand:#dfc6a2;
  --espresso:#3f342e;
  --rust:#b96a37;
  --sienna:#81412d;
  --accent:#81412d;
  --accent-hover:#b96a37;
  background-color:#f4efe4;
}

.crear-venta .content-header h1{ display:none; }
.cv-titulo{ font-weight:800; text-transform:uppercase; letter-spacing:.5px; margin:0 0 6px; font-size:28px; color:var(--espresso); }
.cv-rule{ width:60px; height:4px; border-radius:2px; background:var(--accent); margin-bottom:22px; }

/* Panel único y angosto que agrupa todo el flujo de la venta */
.cv-panel{ max-width:760px; background:#fff; border:1px solid #eee3d2; border-radius:12px; padding:22px 24px; box-shadow:0 1px 3px rgba(63,52,46,.06); }

/* Datos de negocio / perfil / usuario en sesión, apilados */
.cv-negocio-item{ background:#f4efe4; border:1px solid #eee3d2; border-radius:8px; padding:10px 14px; display:flex; align-items:center; gap:12px; margin-bottom:12px; }
.cv-negocio-icono{ width:32px; height:32px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:14px; flex-shrink:0; }
.cv-negocio-valor{ border:none; background:transparent; color:var(--espresso); font-weight:600; padding:0; width:100%; }
.cv-negocio-valor:focus{ outline:none; box-shadow:none; }

.cv-icono-sand{ background:var(--sand); color:var(--espresso); }
.cv-icono-rust{ background:var(--rust); color:#fff; }
.cv-icono-sienna{ background:var(--sienna); color:#fff; }

.cv-campo{ margin:18px 0 12px; }
.cv-campo label{ display:block; color:#8d7a68; font-size:13px; font-weight:600; margin-bottom:6px; }

/* Folio de reservación / Seleccionar habitación: estilo con la paleta del hotel */
.cv-input-icon{ position:relative; }
.cv-input-icon > i{ position:absolute; left:14px; top:50%; transform:translateY(-50%); color:var(--rust); z-index:2; pointer-events:none; font-size:14px; }

.cv-campo-folio input#folioReservacion{
  width:100%;
  padding:10px 14px 10px 38px;
  border:1px solid var(--sand);
  border-radius:8px;
  background:#fdfaf4;
  color:var(--espresso);
  font-weight:600;
  transition:border-color .15s, box-shadow .15s, background .15s;
}
.cv-campo-folio input#folioReservacion::placeholder{ color:#b3a284; font-weight:400; }
.cv-campo-folio input#folioReservacion:focus{ outline:none; border-color:var(--accent); box-shadow:0 0 0 3px rgba(129,65,45,.12); background:#fff; }

/* Lista de sugerencias cuando la búsqueda por nombre coincide con más de un huésped */
.cv-folio-sugerencias{
  display:none;
  position:absolute;
  top:calc(100% + 6px);
  left:0;
  right:0;
  z-index:20;
  background:#fff;
  border:1px solid var(--sand);
  border-radius:8px;
  box-shadow:0 6px 18px rgba(63,52,46,.15);
  max-height:220px;
  overflow-y:auto;
}
.cv-folio-sugerencia{ padding:10px 14px; cursor:pointer; border-bottom:1px solid #f0e6d2; }
.cv-folio-sugerencia:last-child{ border-bottom:none; }
.cv-folio-sugerencia:hover{ background:#faf3e6; }
.cv-folio-sugerencia-hab{ font-weight:700; color:var(--espresso); font-size:13px; }
.cv-folio-sugerencia-hab i{ color:var(--rust); margin-right:4px; }
.cv-folio-sugerencia-detalle{ color:#8d7a68; font-size:12px; margin-top:2px; }

.cv-campo-habitacion select#habitacion.form-control{
  padding:9px 14px 9px 38px;
  border:1px solid var(--sand);
  border-radius:8px;
  background:#fdfaf4;
  color:var(--espresso);
  font-weight:600;
  height:auto;
  transition:border-color .15s, box-shadow .15s, background .15s;
}
.cv-campo-habitacion select#habitacion.form-control:focus{
  outline:none;
  border-color:var(--accent);
  box-shadow:0 0 0 3px rgba(129,65,45,.12);
  background:#fff;
}

.cv-campo-habitacion .select2-container--default .select2-selection--single{
  border:1px solid var(--sand);
  border-radius:8px;
  background:#fdfaf4;
  height:auto;
  padding:9px 0;
  transition:border-color .15s, box-shadow .15s, background .15s;
}
.cv-campo-habitacion .select2-container--default .select2-selection--single .select2-selection__rendered{
  color:var(--espresso);
  font-weight:600;
  line-height:normal;
  padding-left:38px;
}
.cv-campo-habitacion .select2-container--default .select2-selection--single .select2-selection__arrow{ height:38px; right:10px; }
.cv-campo-habitacion .select2-container--default .select2-selection--single .select2-selection__arrow b{ border-color:var(--rust) transparent transparent transparent; }
.cv-campo-habitacion .select2-container--default.select2-container--open .select2-selection--single .select2-selection__arrow b{ border-color:transparent transparent var(--rust) transparent; }
.cv-campo-habitacion .select2-container--default.select2-container--focus .select2-selection--single,
.cv-campo-habitacion .select2-container--default.select2-container--open .select2-selection--single{
  border-color:var(--accent);
  box-shadow:0 0 0 3px rgba(129,65,45,.12);
  background:#fff;
}

.cv-info-row{ display:flex; flex-wrap:wrap; gap:10px; margin-top:10px; }
.cv-huesped-box{ flex:1; min-width:180px; background:#f4efe4; border:1px solid #eee3d2; border-radius:8px; padding:10px 14px; display:flex; align-items:center; gap:8px; }
.cv-huesped-box i{ color:var(--rust); }
.cv-huesped-label{ color:#8d7a68; font-size:13px; font-weight:600; }
.cv-huesped-nombre{ color:var(--espresso); font-weight:700; }

.cv-huesped-box-split{ flex-direction:column; align-items:stretch; gap:10px; }
.cv-huesped-mitad{ display:flex; align-items:center; gap:8px; }
.cv-huesped-mitad-extra{ padding-top:10px; border-top:1px solid #e2d5be; }

.cv-btn-corte{ background:var(--rust); color:#fff; border:none; border-radius:8px; padding:9px 18px; font-weight:600; transition:background .15s; margin-top:12px; }
.cv-btn-corte:hover{ background:var(--accent-hover); color:#fff; }

.cv-scan{ position:relative; margin:18px 0 6px; clear:both; }
.cv-scan i{ position:absolute; left:14px; top:50%; transform:translateY(-50%); color:#9c8a76; }
.cv-scan input{ width:100%; padding:10px 14px 10px 38px; border:1px solid #e5d8bf; border-radius:8px; background:#fdfaf4; }
.cv-scan input:focus{ outline:none; border-color:var(--accent); box-shadow:0 0 0 2px rgba(129,65,45,.1); }

/* Productos agregados dinámicamente por ventas.js */
.crear-venta .nuevoProducto{ margin:0; }
.crear-venta .nuevoProducto .row{ background:#fdfaf4; border:1px solid #eee3d2; border-radius:10px; margin:10px 0 !important; align-items:center; }
.crear-venta .nuevoProducto .input-group-addon{ background:#fff; border:1px solid #e5d8bf; color:var(--espresso); }
.crear-venta .nuevoProducto .form-control{ border:1px solid #e5d8bf; }
.crear-venta .nuevoProducto .form-control[readonly]{ background:#fff; }
.crear-venta .nuevoProducto .btn-danger.quitarProducto{ background:var(--sienna); border-color:var(--sienna); }
.crear-venta .nuevoProducto .btn-info.descuentos{ background:var(--rust); border-color:var(--rust); }
.crear-venta .descuento-leyenda{ display:inline-block; margin-left:8px; font-size:12px; color:#8d7a68; }

.cv-separador{ border:none; border-top:1px solid #eee3d2; margin:20px 0; }
.cv-separador-acento{ border-top:2px solid var(--accent); }

.cv-pago{ display:flex; gap:20px; margin-bottom:8px; }
.cv-pago-item{ flex:1; }
.cv-pago-label{ display:block; color:var(--espresso); font-weight:700; font-size:15px; margin-bottom:8px; }
.cv-pago .input-group-addon{ background:var(--sand); border:1px solid var(--sand); color:var(--espresso); font-size:16px; }
.cv-pago .form-control{ border:1px solid #e5d8bf; font-weight:700; color:var(--espresso); font-size:18px; padding:12px 14px; height:auto; }

.cv-recargas-titulo{ text-align:center; font-weight:700; color:var(--espresso); margin-bottom:16px; font-size:14px; }
.cv-recargas-stats{ display:flex; flex-wrap:wrap; align-items:center; justify-content:center; gap:22px; }
.cv-recargas-stat{ display:flex; flex-direction:column; align-items:center; gap:4px; }
.cv-recargas-stat-label{ color:#8d7a68; font-size:12px; text-transform:uppercase; letter-spacing:.4px; }
.cv-recargas-stat-valor{ font-size:16px; font-weight:700; color:var(--espresso); }

.cv-btn-secundario{ background:#fff; border:1px solid var(--accent); color:var(--accent); border-radius:8px; padding:8px 16px; font-weight:600; transition:.15s; }
.cv-btn-secundario:hover{ background:var(--accent); color:#fff; }
.cv-btn-secundario.cv-btn-verde{ border-color:#4c8c5a; color:#4c8c5a; }
.cv-btn-secundario.cv-btn-verde:hover{ background:#4c8c5a; color:#fff; }

.cv-btn-guardar{ background:var(--accent); color:#fff; border:none; border-radius:8px; padding:11px 24px; font-weight:700; transition:background .15s; margin-top:20px; }
.cv-btn-guardar:hover:not(:disabled){ background:var(--accent-hover); color:#fff; }
.cv-btn-guardar:disabled{ background:#cbbfa9; color:#fff; cursor:not-allowed; }
.cv-btn-guardar-full{ display:block; width:100%; padding:16px 24px; font-size:17px; margin-top:28px; }
.cv-clear{ clear:both; }

/* Modales Recargas/Verificar: mismo lenguaje de color que el resto de la pantalla */
#modalRecargas .modal-header .close,
#modalVerificar .modal-header .close{ color:#f6ecdb; opacity:.75; text-shadow:none; }
#modalRecargas .modal-header .close:hover,
#modalVerificar .modal-header .close:hover{ color:#fff; opacity:1; }

#modalRecargas .nav-tabs-custom > .nav-tabs > li.active > a{ border-top-color:var(--accent); color:var(--espresso); }
#modalRecargas .cv-btn-modal,
#modalVerificar .cv-btn-modal{ background:var(--accent); border:1px solid var(--accent); color:#fff; }
#modalRecargas .cv-btn-modal:hover,
#modalVerificar .cv-btn-modal:hover{ background:var(--accent-hover); border-color:var(--accent-hover); color:#fff; }

#modalRecargas .cv-btn-salir,
#modalVerificar .cv-btn-salir{ background:#fff; border:1px solid var(--accent); color:var(--accent); }
#modalRecargas .cv-btn-salir:hover,
#modalVerificar .cv-btn-salir:hover{ background:var(--accent); color:#fff; }
</style>

<div class="content-wrapper crear-venta">

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

    <h1 class="cv-titulo">CREAR VENTA</h1>
    <div class="cv-rule"></div>

    <?php
      // Id_Negocio (sesión) y su Id_Hotel real ligado, usados por el combo de
      // habitaciones y por el panel de Saldos Recargas.
      $IdNegocio = $_SESSION["IdNegocio"];
      $HotelNegocio = ModeloHoteles::MdlObtenerHotelPorNegocio($IdNegocio);
      $IdHotel = $HotelNegocio ? $HotelNegocio['Id_Hotel'] : null;
      $habitacionesOcupadas = $IdHotel ? ModeloHoteles::MdlObtenerHabitacionesOcupadas($IdHotel) : [];
    ?>

    <div class="cv-panel">
      <form role="form" method="post" class="formularioVenta">

        <div class="cv-negocio-item">
          <span class="cv-negocio-icono cv-icono-sand">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
              <rect x="10" y="1" width="4" height="2.6" rx="0.6" fill="currentColor"/>
              <rect x="3" y="4.2" width="18" height="5" rx="2" fill="currentColor"/>
              <rect x="4.3" y="5.3" width="15.4" height="2.8" rx="1" fill="#dfc6a2"/>
              <rect x="4" y="9.8" width="16" height="11.2" rx="1.5" fill="currentColor"/>
              <rect x="6.3" y="12.2" width="2.6" height="2.6" rx="0.6" fill="#dfc6a2"/>
              <rect x="10.7" y="12.2" width="2.6" height="2.6" rx="0.6" fill="#dfc6a2"/>
              <rect x="15.1" y="12.2" width="2.6" height="2.6" rx="0.6" fill="#dfc6a2"/>
              <rect x="10.2" y="17.3" width="3.6" height="3.7" rx="0.4" fill="#dfc6a2"/>
              <rect x="11.85" y="17.3" width="0.3" height="3.7" fill="currentColor"/>
              <rect x="2.5" y="21" width="19" height="1.3" rx="0.6" fill="currentColor"/>
            </svg>
          </span>
          <input type="text" class="cv-negocio-valor" value="<?php echo $_SESSION["RazonSocial"]; ?>" readonly>
        </div>
        <div class="cv-negocio-item">
          <span class="cv-negocio-icono cv-icono-rust"><i class="fa fa-users"></i></span>
          <input type="text" class="cv-negocio-valor" id="Perfil" value="<?php echo $_SESSION["Perfil"]; ?>" readonly>
          <input type="hidden" class="form-control" id="usuario" value="<?php echo $_SESSION["IdUsuario"]; ?>" readonly>
          <input type="hidden" class="form-control" id="telefono" value="<?php echo $_SESSION["Telefono"]; ?>" readonly>
        </div>
        <div class="cv-negocio-item">
          <span class="cv-negocio-icono cv-icono-sienna"><i class="fa fa-user"></i></span>
          <input type="text" class="cv-negocio-valor" id="Nuser" value="<?php echo $_SESSION["NombreUser"]; ?>" readonly>
        </div>

        <div class="cv-campo cv-campo-folio">
          <label for="folioReservacion">Buscar por folio de reservación o nombre del huésped</label>
          <div class="cv-input-icon">
            <i class="fa fa-bookmark"></i>
            <input type="text" class="form-control" id="folioReservacion" name="folioReservacion" placeholder="Ej. 20260812150052 o nombre del huésped" autocomplete="off">
            <div class="cv-folio-sugerencias" id="folioSugerencias"></div>
          </div>
        </div>

        <div class="cv-campo cv-campo-habitacion">
          <label for="habitacion">Seleccionar habitación</label>
          <div class="cv-input-icon">
            <i class="fa fa-bed"></i>
            <select class="form-control select2" id="habitacion" name="habitacion" style="width:100%">
            <option value="0">-- Selecciona --</option>
            <?php foreach ($habitacionesOcupadas as $habitacion): ?>
              <option value="<?php echo $habitacion["Id_Habitacion"]; ?>"
                      data-reservacion="<?php echo $habitacion["Id_Reservacion"]; ?>"
                      data-cliente="<?php echo $habitacion["Id_Cliente"]; ?>"
                      data-nombre-cliente="<?php echo htmlspecialchars($habitacion["NombreCliente"].' '.$habitacion["ApellidoCliente"]); ?>"
                      data-numero="<?php echo htmlspecialchars($habitacion["NumeroHabitacion"]); ?>"
                      data-fecha-entrada="<?php echo $habitacion["FechaEntrada"]; ?>"
                      data-fecha-salida="<?php echo $habitacion["FechaSalida"]; ?>"
                      data-horas-extras="<?php echo (int)$habitacion["HorasExtras"]; ?>">
                <?php echo htmlspecialchars($habitacion["TipoHabitacion"]); ?>
              </option>
            <?php endforeach; ?>
            </select>
          </div>

          <div class="cv-info-row">
            <div class="cv-huesped-box" id="huespedBox" style="display:none;">
              <i class="fa fa-user"></i>
              <span class="cv-huesped-label">Huésped:</span>
              <span class="cv-huesped-nombre" id="huespedNombre"></span>
            </div>

            <div class="cv-huesped-box" id="habitacionNumeroBox" style="display:none;">
              <i class="fa fa-key"></i>
              <span class="cv-huesped-label">Habitación:</span>
              <span class="cv-huesped-nombre" id="habitacionNumero"></span>
            </div>
          </div>

          <div class="cv-info-row">
            <div class="cv-huesped-box" id="entradaBox" style="display:none;">
              <i class="fa fa-sign-in"></i>
              <span class="cv-huesped-label">Entrada:</span>
              <span class="cv-huesped-nombre" id="entradaFecha"></span>
            </div>

            <div class="cv-huesped-box cv-huesped-box-split" id="salidaBox" style="display:none;">
              <div class="cv-huesped-mitad">
                <i class="fa fa-sign-out"></i>
                <span class="cv-huesped-label">Salida:</span>
                <span class="cv-huesped-nombre" id="salidaFecha"></span>
              </div>
              <div class="cv-huesped-mitad cv-huesped-mitad-extra" id="tiempoExtraBox" style="display:none;">
                <i class="fa fa-clock-o"></i>
                <span class="cv-huesped-label">Tiempo extra:</span>
                <span class="cv-huesped-nombre" id="tiempoExtraFecha"></span>
              </div>
            </div>
          </div>

          <div class="cv-info-row">
            <div class="cv-huesped-box" id="reservacionBox" style="display:none;">
              <i class="fa fa-bookmark"></i>
              <span class="cv-huesped-label">Reservación:</span>
              <span class="cv-huesped-nombre" id="reservacionId"></span>
            </div>
          </div>
        </div>

        <button type="button" class="cv-btn-corte btnGeneraCierre pull-right">Generar Corte</button>
        <div class="cv-clear"></div>

        <div class="cv-scan">
          <i class="fa fa-barcode"></i>
          <input type="text" class="form-control input-sm" name="Qbarra" id="Qbarra" placeholder="Codigo Barras" onchange="CargaProducto();">
        </div>

        <div class="form-group row nuevoProducto"></div>

        <input type="hidden" id="listaProductos" name="listaProductos">

        <hr class="cv-separador">

        <div class="cv-pago">
          <div class="cv-pago-item">
            <span class="cv-pago-label">Paga:</span>
            <div class="input-group">
              <span class="input-group-addon"><i class="ion ion-social-usd"></i></span>
              <input type="number" class="form-control" id="paga" name="paga" placeholder="00000" required>
            </div>
          </div>

          <div class="cv-pago-item">
            <span class="cv-pago-label">Cambio:</span>
            <div class="input-group">
              <span class="input-group-addon"><i class="ion ion-social-usd"></i></span>
              <input type="text" class="form-control" id="cambio" name="cambio" placeholder="00000" readonly required>
            </div>
          </div>

          <div class="cv-pago-item">
            <span class="cv-pago-label">Total</span>
            <div class="input-group">
              <span class="input-group-addon"><i class="ion ion-social-usd"></i></span>
              <input type="text" class="form-control" id="nuevoTotalVenta" name="nuevoTotalVenta" total="" placeholder="00000" readonly required>
              <input type="hidden" name="totalVenta" id="totalVenta">
            </div>
          </div>
        </div>

        <!-- Oculto a petición: se conserva en el DOM para poder reactivarse después -->
        <div style="display:none;">

        <hr class="cv-separador cv-separador-acento">

        <div class="cv-recargas">

          <div class="cv-recargas-titulo">Saldos Recargas</div>

          <?php
          $datos = controladorWebservice::ctrConsultarSaldos();
          $tiempo = $datos->{'tiempoaire'};
          $servicios = $datos->{'pagoservicios'};
          $sms = $datos->{'sms'};
          $comision = $tiempo * 0.05;
          $pago = $tiempo - $comision;

          $SNegocio = $IdHotel ? ModeloHoteles::MdlObtenerHotel($IdHotel) : null;
          $tiempo = $SNegocio['SAire'] ?? 0;
          $servicios = $SNegocio['SServicios'] ?? 0;
          ?>

          <div class="cv-recargas-stats">
            <div class="cv-recargas-stat">
              <span class="cv-recargas-stat-label">T. Aire</span>
              <span class="cv-recargas-stat-valor">$<?= number_format($tiempo, 2) ?></span>
            </div>
            <div class="cv-recargas-stat">
              <span class="cv-recargas-stat-label">Servicios</span>
              <span class="cv-recargas-stat-valor">$<?= number_format($servicios, 2) ?></span>
            </div>
            <div class="cv-recargas-stat">
              <span class="cv-recargas-stat-label">SMS</span>
              <span class="cv-recargas-stat-valor">$<?= number_format($pago, 2) ?></span>
            </div>
            <div class="cv-recargas-stat">
              <button type="button" class="cv-btn-secundario" data-toggle="modal" data-target="#modalRecargas" data-dismiss="modal">Recargar</button>
            </div>
            <div class="cv-recargas-stat">
              <button type="button" class="cv-btn-secundario cv-btn-verde" data-toggle="modal" data-target="#modalVerificar" data-dismiss="modal">Verificar</button>
            </div>
          </div>

        </div>

        </div>
        <!-- /Saldos Recargas oculto -->

        <button type="button" class="cv-btn-guardar cv-btn-guardar-full btnGuardarVenta" disabled>Guardar venta</button>
        <div class="cv-clear"></div>

      </form>
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

        <div class="modal-header" style="background:#3f342e; color:#f6ecdb">

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
                  <button type="button" class="btn cv-btn-modal mb-3" onclick="reseRecarProd(event);">Reservar</button>
                  <input type="hidden" id="tiempo" value="<?php echo $tiempo; ?>">
                  <button type="button" class="btn cv-btn-modal mb-3" onclick="proceRecarProd(event);">Recargar</button>
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
                  <button type="button" class="btn cv-btn-modal mb-3" onclick="reseRecarServ(event);">Reservar</button>
                  <button type="button" class="btn cv-btn-modal mb-3" onclick="proceRecarServ(event);">Recargar</button>


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
                  <button type="button" class="btn cv-btn-modal mb-3" onclick="reseRecarPin(event);">Reservar</button>
                  <button type="button" class="btn cv-btn-modal mb-3" onclick="proceRecarPin(event);">Recargar</button>


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

          <button type="submit" class="btn cv-btn-salir">Salir</button>

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

        <div class="modal-header" style="background:#3f342e; color:#f6ecdb">

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

          <button type="button" class="btn cv-btn-salir pull-left" data-dismiss="modal">Salir</button>
          <button type="button" class="btn cv-btn-modal mb-3" onclick="verificarRecarga(event);">Verificar</button>
        </div>

      </form>

    </div>

  </div>

</div>
