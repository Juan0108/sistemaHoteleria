<?php

$ctlMantenimiento = new ControladorMantenimiento();

$tablero = ControladorMantenimiento::crtObtenerTablero();
$pendientesLiquidarMtto = ControladorMantenimiento::crtObtenerPendientesLiquidar($tablero);
$habitacionesMtto = ControladorMantenimiento::crtObtenerHabitaciones();
$tiposMtto = ControladorMantenimiento::crtObtenerTipos();
$motivosMtto = ControladorMantenimiento::crtObtenerMotivos();

?>
<div class="mtto-reload-overlay mtto-reload-visible" id="mttoReloadOverlay">
	<i class="fa fa-refresh fa-spin mtto-reload-spinner"></i>
</div>
<script>
(function(){

	// Mismo mecanismo que usa Crear venta (cv-reload-overlay): se muestra al
	// recargar (F5) o justo antes de que el navegador navegue a la página
	// nueva, y se oculta solo cuando esa página termina de cargar. Independiente
	// de jQuery y del resto de los scripts (que cargan al final del body) para
	// que nunca se quede pegado si algo más falla.
	//
	// Con F5 (sobre todo con todo ya en caché del navegador) la página puede
	// cargar tan rápido que el overlay se oculta casi al instante y no da
	// tiempo de percibirlo. _inicio guarda cuándo arrancó este script para
	// forzar un mínimo de tiempo visible, sin importar qué tan rápido cargue.
	var _oculto = false;
	var _inicio = Date.now();
	var _minimoVisibleMs = 400;

	function ocultarOverlayRecargaMtto(){
		if(_oculto) return;

		var _faltante = _minimoVisibleMs - (Date.now() - _inicio);

		if(_faltante > 0){
			setTimeout(ocultarOverlayRecargaMtto, _faltante);
			return;
		}

		_oculto = true;
		var _overlay = document.getElementById("mttoReloadOverlay");
		if(_overlay) _overlay.classList.remove("mtto-reload-visible");
	}

	document.addEventListener("DOMContentLoaded", ocultarOverlayRecargaMtto);
	window.addEventListener("load", ocultarOverlayRecargaMtto);
	setTimeout(ocultarOverlayRecargaMtto, 6000);

	window.addEventListener("beforeunload", function(){
		_oculto = false;
		var _overlay = document.getElementById("mttoReloadOverlay");
		if(_overlay) _overlay.classList.add("mtto-reload-visible");
	});

})();
</script>

<div class="content-wrapper mtto-wrapper">

	<section class="content-header">
		<h1>Mantenimiento</h1>
		<ol class="breadcrumb">
			<li><a href="inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
			<li class="active">Mantenimiento</li>
		</ol>
	</section>

	<section class="content">

		<div class="mtto-encabezado">
			<h1 class="mtto-titulo">MANTENIMIENTO</h1>
			<div class="mtto-rule"></div>
			<button type="button" class="btn mtto-btn-primary" data-toggle="modal" data-target="#modalAgregarMantenimiento">
				<i class="fa fa-plus"></i> Registrar incidencia
			</button>
		</div>

		<div class="mtto-tablero">

			<div class="mtto-columna mtto-columna-pendiente">
				<div class="mtto-columna-header">
					<span class="mtto-columna-punto"></span>
					Pendiente
					<div class="mtto-columna-header-der">
						<button type="button" class="mtto-btn-bitacora-eliminadas btnBitacoraEliminadas" title="Bitácora de incidencias eliminadas"><i class="fa fa-clock-o"></i></button>
						<span class="mtto-columna-contador"><?php echo count($tablero["pendiente"]); ?></span>
					</div>
				</div>
				<div class="mtto-columna-body">
					<?php if(count($tablero["pendiente"]) === 0): ?>
						<div class="mtto-vacio">Sin incidencias pendientes</div>
					<?php endif; ?>
					<?php foreach($tablero["pendiente"] as $item){ echo ControladorMantenimiento::crtRenderizarTarjeta($item, "pendiente"); } ?>
				</div>
			</div>

			<div class="mtto-columna mtto-columna-proceso">
				<div class="mtto-columna-header">
					<span class="mtto-columna-punto"></span>
					En Proceso
					<div class="mtto-columna-header-der">
						<button type="button" class="mtto-btn-bitacora-eliminadas btnBitacoraEliminadas" title="Bitácora de incidencias eliminadas"><i class="fa fa-clock-o"></i></button>
						<span class="mtto-columna-contador"><?php echo count($tablero["proceso"]); ?></span>
					</div>
				</div>
				<div class="mtto-columna-body">
					<?php if(count($tablero["proceso"]) === 0): ?>
						<div class="mtto-vacio">Nada en proceso</div>
					<?php endif; ?>
					<?php foreach($tablero["proceso"] as $item){ echo ControladorMantenimiento::crtRenderizarTarjeta($item, "proceso"); } ?>
				</div>
			</div>

			<div class="mtto-columna mtto-columna-resuelto">
				<div class="mtto-columna-header">
					<span class="mtto-columna-punto"></span>
					Resuelto
					<div class="mtto-columna-header-der">
						<button type="button" class="mtto-btn-bitacora-eliminadas btnBitacoraEliminadas" title="Bitácora de incidencias eliminadas"><i class="fa fa-clock-o"></i></button>
						<span class="mtto-columna-contador"><?php echo count($tablero["resuelto"]); ?></span>
					</div>
				</div>
				<div class="mtto-columna-body">
					<?php if(count($tablero["resuelto"]) === 0): ?>
						<div class="mtto-vacio">Nada resuelto todavía</div>
					<?php endif; ?>
					<?php foreach($tablero["resuelto"] as $item){ echo ControladorMantenimiento::crtRenderizarTarjeta($item, "resuelto"); } ?>
				</div>
			</div>

		</div>

	</section>
</div>

<style>
	.mtto-wrapper{ background:#f2ece0; }
	.mtto-wrapper .content-header h1{ display:none; }

	.mtto-encabezado{ margin-bottom:20px; }
	.mtto-titulo{ font-weight:800; text-transform:uppercase; letter-spacing:.5px; margin:0 0 6px; font-size:28px; color:#3f342e; }
	.mtto-rule{ width:60px; height:4px; border-radius:2px; background:#81412d; margin-bottom:18px; }

	.mtto-btn-primary{ background:#81412d; border-color:#81412d; color:#fff; border-radius:8px; font-weight:600; padding:9px 18px; }
	.mtto-btn-primary:hover, .mtto-btn-primary:focus{ background:#6e3625; border-color:#6e3625; color:#fff; }

	.mtto-tablero{ display:grid; grid-template-columns:repeat(3, 1fr); gap:18px; align-items:start; }
	@media (max-width:991px){ .mtto-tablero{ grid-template-columns:1fr; } }

	.mtto-columna{ background:#f8f4ea; border:1px solid #eee3d2; border-radius:12px; padding:14px; min-height:120px; }

	.mtto-columna-header{ display:flex; align-items:center; gap:8px; font-weight:800; text-transform:uppercase; letter-spacing:.4px; font-size:13px; color:#3f342e; margin-bottom:12px; }
	.mtto-columna-header-der{ margin-left:auto; display:flex; align-items:center; gap:8px; }
	.mtto-columna-contador{ background:#3f342e; color:#fff; border-radius:999px; font-size:11px; font-weight:700; padding:2px 9px; }
	.mtto-btn-bitacora-eliminadas{ background:none; border:none; color:#8d7a68; font-size:14px; padding:0; line-height:1; }
	.mtto-btn-bitacora-eliminadas:hover{ color:#3f342e; }
	.mtto-columna-punto{ width:10px; height:10px; border-radius:50%; display:inline-block; }
	.mtto-columna-pendiente .mtto-columna-punto{ background:#b96a37; }
	.mtto-columna-proceso .mtto-columna-punto{ background:#81412d; }
	.mtto-columna-resuelto .mtto-columna-punto{ background:#4c8c5a; }

	.mtto-vacio{ color:#a3927c; font-size:13px; text-align:center; padding:18px 6px; }

	.mtto-columna-body{ display:flex; flex-direction:column; gap:12px; }

	.mtto-tarjeta{ background:#fff; border:1px solid #eee3d2; border-radius:10px; padding:12px 14px; box-shadow:0 1px 3px rgba(63,52,46,.06); }
	.mtto-columna-pendiente .mtto-tarjeta{ border-left:4px solid #b96a37; }
	.mtto-columna-proceso .mtto-tarjeta{ border-left:4px solid #81412d; }
	.mtto-columna-resuelto .mtto-tarjeta{ border-left:4px solid #4c8c5a; opacity:.88; }

	.mtto-cabecera{ display:flex; align-items:flex-start; justify-content:space-between; gap:10px; margin-bottom:12px; }
	.mtto-col-izq{ display:flex; flex-direction:column; gap:8px; min-width:0; flex:1; }
	.mtto-col-der{ display:flex; flex-direction:column; align-items:flex-end; gap:8px; flex-shrink:0; text-align:right; }

	.mtto-hab-badge{ background:#f4efe4; color:#3f342e; font-weight:700; font-size:12px; border-radius:999px; padding:3px 10px; align-self:flex-start; }
	.mtto-btn-detalle{ align-self:flex-start; background:none; border:none; color:#81412d; font-size:11px; font-weight:700; padding:0; text-decoration:underline; }
	.mtto-btn-detalle:hover{ color:#6e3625; }
	.mtto-reabierta-badge{ align-self:flex-start; background:#fbeae5; color:#c85c3c; font-weight:700; font-size:11px; border-radius:999px; padding:3px 10px; }
	.mtto-liquidar-badge{ align-self:flex-start; background:#fdf1de; color:#b96a37; font-weight:700; font-size:11px; border-radius:999px; padding:3px 10px; }
	.mtto-pagada-badge{ align-self:flex-start; background:#e6f4e9; color:#3f7649; font-weight:700; font-size:11px; border-radius:999px; padding:3px 10px; }
	.mtto-tipo{ color:#8d7a68; font-size:12px; font-weight:600; text-align:right; white-space:nowrap; }

	.mtto-pieza{ display:flex; align-items:center; gap:8px; }
	.mtto-pieza-icono{ width:28px; height:28px; border-radius:8px; background:#dfc6a2; color:#3f342e; display:flex; align-items:center; justify-content:center; font-size:13px; flex-shrink:0; }
	.mtto-pieza-nombre{ font-weight:700; color:#3f342e; font-size:13px; }

	.mtto-descripcion{ color:#6b5c4c; font-size:12px; }

	.mtto-datos{ display:flex; flex-direction:column; align-items:flex-end; gap:4px; }
	.mtto-dato{ color:#8d7a68; font-size:12px; font-weight:600; white-space:nowrap; }
	.mtto-dato i{ color:#b96a37; margin-right:3px; }

	.mtto-bitacoras{ display:flex; gap:6px; margin-top:2px; }
	.mtto-btn-bitacora{ background:#f4efe4; border:1px solid #e5d8bf; color:#b96a37; border-radius:6px; width:28px; height:28px; font-size:12px; }
	.mtto-btn-bitacora:hover{ background:#eee3d2; }
	.mtto-btn-bitacora-abonos{ color:#3f7649; }

	.mtto-acciones{ display:flex; align-items:center; gap:6px; flex-wrap:wrap; border-top:1px solid #f0e6d2; padding-top:10px; }
	.mtto-btn-orden{ background:#f4efe4; border:1px solid #e5d8bf; color:#3f342e; border-radius:6px; width:28px; height:28px; font-size:12px; }
	.mtto-btn-orden:hover{ background:#eee3d2; }
	.mtto-btn-avanzar{ margin-left:auto; background:#81412d; border:1px solid #81412d; color:#fff; border-radius:6px; font-size:12px; font-weight:600; padding:5px 10px; }
	.mtto-btn-avanzar:hover{ background:#6e3625; border-color:#6e3625; color:#fff; }
	.mtto-btn-resolver{ background:#4c8c5a; border-color:#4c8c5a; }
	.mtto-btn-resolver:hover{ background:#3f7649; border-color:#3f7649; }
	.mtto-btn-reabrir{ margin-left:auto; background:#fff; border:1px solid #81412d; color:#81412d; border-radius:6px; font-size:12px; font-weight:600; padding:5px 10px; }
	.mtto-btn-reabrir:hover{ background:#81412d; color:#fff; }
	.mtto-btn-eliminar{ background:#fff; border:1px solid #c85c3c; color:#c85c3c; border-radius:6px; width:28px; height:28px; font-size:12px; }
	.mtto-btn-eliminar:hover{ background:#c85c3c; color:#fff; }
	.mtto-btn-abonos{ background:#fff; border:1px solid #3f7649; color:#3f7649; border-radius:6px; font-size:12px; font-weight:600; padding:5px 10px; }
	.mtto-btn-abonos:hover{ background:#3f7649; color:#fff; }

	/* Modal Abonos */
	#modalAbonosMantenimiento .modal-content{ border-radius:16px; overflow:hidden; }
	.mtto-abonos-resumen{ display:grid; grid-template-columns:repeat(3, 1fr); gap:10px; margin-bottom:16px; }
	.mtto-abonos-tarjeta{ background:#f8f4ea; border:1px solid #eee3d2; border-radius:10px; padding:10px 12px; text-align:center; }
	.mtto-abonos-tarjeta-label{ color:#8d7a68; font-size:11px; text-transform:uppercase; letter-spacing:.3px; font-weight:700; }
	.mtto-abonos-tarjeta-valor{ color:#3f342e; font-size:18px; font-weight:800; margin-top:4px; }
	.mtto-abonos-tabla{ width:100%; border-collapse:collapse; margin-bottom:16px; }
	.mtto-abonos-tabla th{ background:#f4efe4; color:#3f342e; text-align:left; font-size:11px; text-transform:uppercase; letter-spacing:.3px; padding:8px 10px; }
	.mtto-abonos-tabla td{ padding:8px 10px; font-size:13px; color:#3f342e; border-top:1px solid #eee3d2; }
	.mtto-abonos-foto{ max-width:48px; max-height:48px; border-radius:6px; border:1px solid #eee3d2; object-fit:cover; }
	.mtto-abonos-vacio{ color:#8d7a68; font-style:italic; text-align:center; padding:14px; }
	#formAbonoMtto{ border-top:1px solid #eee3d2; padding-top:14px; }

	/* Modales Bitácora de Incidencias / Bitácora de Abonos */
	#modalBitacoraIncidencias .modal-content, #modalBitacoraAbonos .modal-content{ border-radius:16px; overflow:hidden; }
	.mtto-bitacora-tabla{ width:100%; border-collapse:collapse; }
	.mtto-bitacora-tabla th{ background:#f4efe4; color:#3f342e; text-align:left; font-size:11px; text-transform:uppercase; letter-spacing:.3px; padding:8px 10px; }
	.mtto-bitacora-tabla td{ padding:8px 10px; font-size:13px; color:#3f342e; border-top:1px solid #eee3d2; }
	.mtto-bitacora-foto{ max-width:48px; max-height:48px; border-radius:6px; border:1px solid #eee3d2; object-fit:cover; }
	.mtto-bitacora-ver-foto{ background:none; border:none; color:#81412d; font-size:12px; font-weight:700; padding:0; text-decoration:underline; }
	.mtto-bitacora-ver-foto:hover{ color:#6e3625; }
	.mtto-bitacora-vacio{ color:#8d7a68; font-style:italic; text-align:center; padding:14px; }
	.mtto-bitacora-estatus{ font-size:11px; font-weight:700; border-radius:999px; padding:2px 8px; white-space:nowrap; }
	.mtto-bitacora-estatus-pendiente{ background:#fdf1de; color:#b96a37; }
	.mtto-bitacora-estatus-en-proceso{ background:#f4e7e1; color:#81412d; }
	.mtto-bitacora-estatus-resuelto{ background:#e6f4e9; color:#3f7649; }
	.mtto-bitacora-estatus-otro{ background:#f0e6d2; color:#8d7a68; }

	/* Modal Bitácora de Incidencias Eliminadas */
	#modalBitacoraEliminadas .modal-content{ border-radius:16px; overflow:hidden; }

	/* Modal Registrar incidencia */
	#modalAgregarMantenimiento .modal-content{ border-radius:16px; overflow:hidden; }
	#modalAgregarMantenimiento .form-control{ border-radius:8px; border-color:#e4d9c8; }
	#modalAgregarMantenimiento .input-group .form-control{ border-top-left-radius:0; border-bottom-left-radius:0; }
	#modalAgregarMantenimiento .input-group-addon{ border-radius:8px 0 0 8px; background:#f2ede6; color:#81412d; border-color:#e4d9c8; }
	#modalAgregarMantenimiento .btn{ border-radius:8px; }
	#modalAgregarMantenimiento .btn-success{ background:#81412d; border-color:#81412d; }
	#modalAgregarMantenimiento .btn-success:hover{ background:#6e3625; border-color:#6e3625; }
	.mtto-btn-secondary{ background:#fff; border:1px solid #3f342e; color:#3f342e; border-radius:8px; }
	.mtto-btn-secondary:hover, .mtto-btn-secondary:focus{ background:#f2ede6; border-color:#3f342e; color:#3f342e; }

	.mtto-detalle-subtitulo{ color:#c9b8a3; font-size:12px; margin-top:6px; }

	/* Modal Detalle de la incidencia */
	#modalDetalleMantenimiento .modal-content{ border-radius:16px; overflow:hidden; }
	.mtto-detalle-tabla{ width:100%; border-collapse:collapse; }
	.mtto-detalle-tabla th{ background:#f4efe4; color:#3f342e; text-align:left; font-size:12px; text-transform:uppercase; letter-spacing:.3px; padding:8px 12px; width:45%; }
	.mtto-detalle-tabla td{ padding:8px 12px; font-size:13px; color:#3f342e; border-top:1px solid #eee3d2; }
	.mtto-detalle-tabla tr th{ border-top:1px solid #eee3d2; }
	.mtto-detalle-vacio{ color:#8d7a68; font-style:italic; }
	.mtto-detalle-foto{ max-width:120px; max-height:120px; border-radius:8px; border:1px solid #eee3d2; object-fit:cover; }

	/* Overlay de recarga (mismo diseño que usa Crear venta) */
	.mtto-reload-overlay{
		position:fixed;
		inset:0;
		background:rgba(255,255,255,.55);
		z-index:99999;
		display:flex;
		align-items:center;
		justify-content:center;
		opacity:0;
		visibility:hidden;
		pointer-events:none;
		transition:opacity .15s ease;
	}
	.mtto-reload-overlay.mtto-reload-visible{
		opacity:1;
		visibility:visible;
		pointer-events:all;
	}
	.mtto-reload-spinner{
		font-size:52px;
		color:#3f342e;
	}
</style>

<!-- Modal Registrar incidencia de mantenimiento -->
<div id="modalAgregarMantenimiento" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<form role="form" method="post" enctype="multipart/form-data">

				<div class="modal-header" style="background:#3f342e; color:white">
					<button type="button" class="close" data-dismiss="modal" style="color:white; opacity:1;">&times;</button>
					<h4 class="modal-title"><i class="fa fa-wrench"></i> Registrar incidencia de mantenimiento</h4>
				</div>

				<div class="modal-body">
					<div class="box-body">

						<div class="form-group">
							<label><i class="fa fa-hotel"></i> Habitación</label>
							<select class="form-control" name="nuevaHabitacionMtto" required>
								<option value="">-- Selecciona --</option>
								<?php foreach($habitacionesMtto as $hab): ?>
									<option value="<?php echo $hab["Id_Habitacion"]; ?>">
										<?php echo htmlspecialchars($hab["NumeroHabitacion"] . " — " . $hab["TipoHabitacion"]); ?>
									</option>
								<?php endforeach; ?>
							</select>
						</div>

						<div class="form-group">
							<label><i class="fa fa-list"></i> Tipo de mantenimiento</label>
							<select class="form-control" name="nuevoTipoMtto" required>
								<option value="">-- Selecciona --</option>
								<?php foreach($tiposMtto as $tipo): ?>
									<option value="<?php echo $tipo["Id_TipoMantenimiento"]; ?>"><?php echo htmlspecialchars($tipo["Nombre"]); ?></option>
								<?php endforeach; ?>
							</select>
						</div>

						<div class="form-group">
							<label><i class="fa fa-cogs"></i> Pieza / parte afectada</label>
							<input type="text" class="form-control" name="nuevaPiezaMtto" maxlength="150" placeholder="Ej. Aire acondicionado, cerradura de la puerta…" required>
						</div>

						<div class="form-group">
							<label><i class="fa fa-truck"></i> Proveedor</label>
							<input type="text" class="form-control" name="nuevoProveedorMtto" maxlength="150" placeholder="Nombre del proveedor" required>
						</div>

						<div class="row">
							<div class="col-xs-6">
								<div class="form-group">
									<label><i class="fa fa-calendar-plus-o"></i> Fecha inicio estimada</label>
									<input type="date" class="form-control" name="nuevaFechaInicioMtto" min="<?php echo date('Y-m-d'); ?>" required>
								</div>
							</div>
							<div class="col-xs-6">
								<div class="form-group">
									<label><i class="fa fa-hourglass-half"></i> Fecha fin estimada</label>
									<input type="date" class="form-control" name="nuevaFechaFinMtto" min="<?php echo date('Y-m-d'); ?>" required>
								</div>
							</div>
						</div>

						<div class="form-group">
							<label><i class="fa fa-money"></i> Costo estimado</label>
							<div class="input-group">
								<span class="input-group-addon">$</span>
								<input type="text" inputmode="decimal" class="form-control" id="nuevoCostoMtto" name="nuevoCostoMtto" placeholder="0.00" required>
							</div>
						</div>

						<div class="form-group">
							<label><i class="fa fa-align-left"></i> Descripción (requerido)</label>
							<textarea class="form-control" name="nuevaDescripcionMtto" id="nuevaDescripcionMtto" rows="3" maxlength="255" style="resize:none;" placeholder="Detalle de la incidencia (mínimo 10 palabras)" required></textarea>
							<span id="contadorNuevaDescripcionMtto" class="help-block small text-muted">0/255 caracteres · 0 palabras (mínimo 10)</span>
						</div>

						<div class="form-group">
							<label><i class="fa fa-camera"></i> Foto de la incidencia (requerido)</label>
							<input type="file" class="nuevaFotoMtto" name="nuevaFotoMtto" accept="image/*">
							<p class="help-block"><i class="fa fa-exclamation-triangle"></i> Peso máximo de la foto 3MB. Queda registrada para la bitácora.</p>
						</div>

					</div>
				</div>

				<div class="modal-footer">
					<button type="button" class="btn mtto-btn-secondary pull-left" data-dismiss="modal">Salir</button>
					<button type="submit" name="nuevoMantenimiento" class="btn btn-success">Registrar incidencia</button>
				</div>

				<?php $ctlMantenimiento->crtInsertarMantenimiento(); ?>

			</form>
		</div>
	</div>
</div>

<!-- Modal Bitácora de la Incidencia -->
<div id="modalDetalleMantenimiento" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header" style="background:#3f342e; color:white">
				<button type="button" class="close" data-dismiss="modal" style="color:white; opacity:1;">&times;</button>
				<h4 class="modal-title"><i class="fa fa-list-alt"></i> Bitácora de la Incidencia</h4>
				<div id="mttoDetalleSubtitulo" class="mtto-detalle-subtitulo"></div>
			</div>
			<div class="modal-body">
				<div id="mttoDetalleContenido">
					<p class="text-muted text-center" style="padding:20px;">Cargando…</p>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Modal Abonos -->
<div id="modalAbonosMantenimiento" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header" style="background:#3f342e; color:white">
				<button type="button" class="close" data-dismiss="modal" style="color:white; opacity:1;">&times;</button>
				<h4 class="modal-title"><i class="fa fa-money"></i> Abonos de la incidencia</h4>
			</div>
			<div class="modal-body">
				<div id="mttoAbonosContenido">
					<p class="text-muted text-center" style="padding:20px;">Cargando…</p>
				</div>

				<form id="formAbonoMtto" enctype="multipart/form-data">
					<input type="hidden" name="idMantenimiento" id="abonoIdMantenimiento" value="">
					<div class="form-group">
						<label><i class="fa fa-money"></i> Monto del abono</label>
						<div class="input-group">
							<span class="input-group-addon">$</span>
							<input type="number" class="form-control" id="abonoMonto" name="monto" min="0.01" step="0.01" placeholder="0.00" required>
						</div>
						<p class="help-block" id="abonoMontoAyuda"></p>
					</div>
					<div class="form-group">
						<label><i class="fa fa-camera"></i> Foto del ticket</label>
						<input type="file" name="fotoAbono" accept="image/*" required>
						<p class="help-block"><i class="fa fa-exclamation-triangle"></i> Peso máximo de la foto 3MB.</p>
					</div>
					<button type="submit" class="btn btn-success"><i class="fa fa-plus"></i> Registrar abono</button>
				</form>
			</div>
		</div>
	</div>
</div>

<!-- Popup para ver en grande la foto de una incidencia de la bitácora -->
<div id="modalFotoBitacora" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header" style="background:#3f342e; color:white">
				<button type="button" class="close" data-dismiss="modal" style="color:white; opacity:1;">&times;</button>
				<h4 id="mttoFotoBitacoraTitulo" class="modal-title"><i class="fa fa-camera"></i> Foto</h4>
			</div>
			<div class="modal-body text-center" style="background:#f4efe4;">
				<img id="mttoFotoBitacoraImg" src="" alt="Foto" style="max-width:100%; max-height:65vh; object-fit:contain; border-radius:8px;">
			</div>
		</div>
	</div>
</div>

<!-- Modal Bitácora de Incidencias Eliminadas -->
<div id="modalBitacoraEliminadas" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header" style="background:#3f342e; color:white">
				<button type="button" class="close" data-dismiss="modal" style="color:white; opacity:1;">&times;</button>
				<h4 class="modal-title"><i class="fa fa-clock-o"></i> Bitácora de incidencias eliminadas</h4>
			</div>
			<div class="modal-body">
				<div id="mttoBitacoraEliminadasContenido">
					<p class="text-muted text-center" style="padding:20px;">Cargando…</p>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Modal Bitácora de Incidencias -->
<div id="modalBitacoraIncidencias" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header" style="background:#3f342e; color:white">
				<button type="button" class="close" data-dismiss="modal" style="color:white; opacity:1;">&times;</button>
				<h4 class="modal-title"><i class="fa fa-book"></i> Bitácora de incidencias de la habitación</h4>
			</div>
			<div class="modal-body">
				<div id="mttoBitacoraIncidenciasContenido">
					<p class="text-muted text-center" style="padding:20px;">Cargando…</p>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Modal Bitácora de Abonos -->
<div id="modalBitacoraAbonos" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header" style="background:#3f342e; color:white">
				<button type="button" class="close" data-dismiss="modal" style="color:white; opacity:1;">&times;</button>
				<h4 class="modal-title"><i class="fa fa-book"></i> Bitácora de abonos de la habitación</h4>
			</div>
			<div class="modal-body">
				<div id="mttoBitacoraAbonosContenido">
					<p class="text-muted text-center" style="padding:20px;">Cargando…</p>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	// Motivos de eliminación para el select del popup de "Eliminar" (mantenimiento.js)
	var MTTO_MOTIVOS = <?php
		$mapaMotivos = [];
		foreach($motivosMtto as $motivo){
			$mapaMotivos[$motivo["Id_MotivoMantenimiento"]] = $motivo["Nombre"];
		}
		echo json_encode($mapaMotivos, JSON_UNESCAPED_UNICODE);
	?>;

	// Incidencias Resueltas con saldo pendiente por cobrar (mantenimiento.js
	// muestra el aviso "Pendiente de liquidar" al cargar el tablero)
	var MTTO_PENDIENTES_LIQUIDAR = <?php echo json_encode($pendientesLiquidarMtto, JSON_UNESCAPED_UNICODE); ?>;

	// Id de estatus "Pendiente": mantenimiento.js lo usa para saber si el
	// botón "Reabrir" que se acaba de presionar debe pedir la nota de reapertura
	var MTTO_ESTATUS_PENDIENTE = <?php echo ControladorMantenimiento::ESTATUS_PENDIENTE; ?>;

	// Id de estatus "Resuelto": mantenimiento.js lo usa para saber si el botón
	// "Marcar resuelto" que se acaba de presionar debe pedir la foto de cómo
	// quedó la incidencia ya reparada
	var MTTO_ESTATUS_RESUELTO = <?php echo ControladorMantenimiento::ESTATUS_RESUELTO; ?>;
</script>
