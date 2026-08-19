<?php

$mesActual = isset($_GET["mes"]) ? (int) $_GET["mes"] : (int) date("n");
$anioActual = isset($_GET["anio"]) ? (int) $_GET["anio"] : (int) date("Y");

if ($mesActual < 1 || $mesActual > 12) {
	$mesActual = (int) date("n");
}

$totalDias = (int) date("t", mktime(0, 0, 0, $mesActual, 1, $anioActual));

$mesAnterior = $mesActual - 1;
$anioMesAnterior = $anioActual;
if ($mesAnterior < 1) { $mesAnterior = 12; $anioMesAnterior--; }

$mesSiguiente = $mesActual + 1;
$anioMesSiguiente = $anioActual;
if ($mesSiguiente > 12) { $mesSiguiente = 1; $anioMesSiguiente++; }

$nombresMes = [1 => "ENERO", 2 => "FEBRERO", 3 => "MARZO", 4 => "ABRIL", 5 => "MAYO", 6 => "JUNIO",
			   7 => "JULIO", 8 => "AGOSTO", 9 => "SEPTIEMBRE", 10 => "OCTUBRE", 11 => "NOVIEMBRE", 12 => "DICIEMBRE"];
$diasSemana = ["Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sáb"];

$hoy = date("Y-m-d");

$ctlReservas = new ControladorHabitaciones();

$Habitaciones = $ctlReservas->crtObtenerHabitacionesReserva($anioActual, $mesActual);

?>
<div class="content-wrapper">

  <section class="content-header">
    <h1>
      Reserva
    </h1>
    <ol class="breadcrumb">
      <li><a href="inicio"><i class="fa fa-dashboard"></i>Inicio</a></li>
      <li class="active">Reservas</li>
    </ol>
  </section>

  <section class="content">

    <div class="box reserva-box">
      <div class="overlay" id="reservaOverlay" style="display:none;">
        <i class="fa fa-refresh fa-spin"></i>
      </div>
      <div class="box-body">

        <div class="reserva-toolbar">
          <div class="reserva-toolbar-grupo">
            <button type="button" class="reserva-btn reserva-btn-activo" id="reservaHoy">Hoy</button>
            <button type="button" class="reserva-btn-nav" id="reservaPrev" data-mes="<?php echo $mesAnterior; ?>" data-anio="<?php echo $anioMesAnterior; ?>"><i class="fa fa-chevron-left"></i></button>
            <button type="button" class="reserva-btn-nav" id="reservaNext" data-mes="<?php echo $mesSiguiente; ?>" data-anio="<?php echo $anioMesSiguiente; ?>"><i class="fa fa-chevron-right"></i></button>
          </div>
          <div class="reserva-toolbar-titulo" id="reservaTitulo"><?php echo $nombresMes[$mesActual] . " " . $anioActual; ?></div>
        </div>

        <div class="reserva-filtro">
          <div class="reserva-filtro-campo">
            <label for="reservaFiltroInicio">Del</label>
            <input type="date" id="reservaFiltroInicio" class="form-control input-sm">
          </div>
          <div class="reserva-filtro-campo">
            <label for="reservaFiltroFin">Al</label>
            <input type="date" id="reservaFiltroFin" class="form-control input-sm">
          </div>
          <button type="button" class="reserva-btn reserva-btn-activo" id="reservaFiltroBuscar">
            <i class="fa fa-search"></i> Buscar disponibilidad
          </button>
          <button type="button" class="reserva-btn-nav reserva-filtro-limpiar" id="reservaFiltroLimpiar" title="Quitar filtro" style="display:none;">
            <i class="fa fa-times"></i>
          </button>
          <span class="reserva-filtro-mensaje" id="reservaFiltroMensaje"></span>
        </div>

        <div class="reserva-grid-wrapper">
          <table class="reserva-grid" id="reservaTabla">
            <?php include __DIR__ . "/reservas_tabla.php"; ?>
          </table>
        </div>

        <div class="reserva-legend">
          <span><i class="reserva-dot reserva-dot-ocupada"></i> Ocupada</span>
          <span><i class="reserva-dot reserva-dot-reservada"></i> Reservada</span>
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

  .reserva-box{
    border-radius:16px;
    overflow:hidden;
  }
  .reserva-toolbar{
    display:flex;
    align-items:center;
    justify-content:flex-start;
    flex-wrap:wrap;
    gap:12px;
    margin-bottom:18px;
  }
  .reserva-toolbar-grupo{
    display:flex;
    align-items:center;
    gap:8px;
  }
  .reserva-toolbar-titulo{
    flex-grow:1;
    text-align:center;
    font-size:26px;
    font-weight:800;
    letter-spacing:1px;
    color:#3f342e;
  }
  .reserva-btn{
    display:inline-flex;
    align-items:center;
    padding:6px 16px;
    border-radius:6px;
    font-weight:600;
    font-size:13px;
    text-decoration:none;
    cursor:pointer;
    border:none;
  }
  .reserva-btn-activo{
    background:#3f342e;
    color:#fff;
  }
  .reserva-btn-nav{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    width:30px;
    height:30px;
    border-radius:6px;
    border:1px solid #ddd3c7;
    background:#fff;
    color:#3f342e;
    text-decoration:none;
    cursor:pointer;
  }
  .reserva-btn-nav:hover{
    background:#dfc6a2;
    color:#3f342e;
  }

  .reserva-filtro{
    display:flex;
    align-items:flex-end;
    flex-wrap:wrap;
    gap:12px;
    margin-bottom:18px;
    padding:12px;
    background:#faf5ec;
    border:1px solid #eee2d3;
    border-radius:10px;
  }
  .reserva-filtro-campo{
    display:flex;
    flex-direction:column;
    gap:4px;
  }
  .reserva-filtro-campo label{
    font-size:11px;
    font-weight:700;
    color:#7a6a58;
    text-transform:uppercase;
    letter-spacing:.5px;
  }
  .reserva-filtro-mensaje{
    font-size:13px;
    font-weight:600;
    color:#3f342e;
  }

  .reserva-grid-wrapper{
    border:1px solid #eee2d3;
    border-radius:10px;
    overflow:hidden;
  }
  .reserva-grid{
    border-collapse:collapse;
    table-layout:fixed;
    width:100%;
  }
  .reserva-grid th, .reserva-grid td{
    border:1px solid #eee2d3;
    text-align:center;
    padding:6px 2px;
    font-size:11px;
    overflow:hidden;
    text-overflow:ellipsis;
    white-space:nowrap;
  }
  .reserva-grid thead th{
    background:#3f342e;
    color:#fff;
    font-weight:600;
  }
  .reserva-dia-nombre{
    display:block;
    font-size:11px;
    opacity:.8;
  }
  .reserva-dia-numero{
    display:block;
    font-size:13px;
    font-weight:700;
  }
  .reserva-col-habitacion{
    position:sticky;
    left:0;
    background:#fff;
    text-align:left;
    width:130px;
    font-weight:600;
    color:#3f342e;
    z-index:2;
  }
  .reserva-grid thead .reserva-col-habitacion{
    background:#3f342e;
    color:#fff;
    z-index:3;
  }
  .reserva-col-hoy{
    background:#f6ecdd;
  }

  .reserva-fila{
    cursor:pointer;
  }
  .reserva-fila:hover td{
    background:#faf5ec;
  }
  .reserva-fila-activa td{
    background:#f3ded0 !important;
  }
  .reserva-fila-activa .reserva-col-habitacion{
    box-shadow:inset 4px 0 0 #b96a37;
  }

  .reserva-fila-descartada td{
    opacity:.3;
  }
  .reserva-fila-disponible .reserva-col-habitacion{
    box-shadow:inset 4px 0 0 #4c8c4a;
  }

  .reserva-celda-barra{
    padding:4px !important;
  }
  .reserva-barra{
    height:28px;
    border-radius:8px;
  }
  .reserva-barra.estado-ocupada{ background:#81412d; }
  .reserva-barra.estado-reservada{ background:#b96a37; }

  .reserva-legend{
    display:flex;
    gap:24px;
    align-items:center;
    margin-top:18px;
  }
  .reserva-legend span{
    display:inline-flex;
    align-items:center;
    gap:8px;
    font-weight:600;
    font-size:13px;
    color:#555;
  }
  .reserva-dot{
    width:16px;
    height:16px;
    border-radius:4px;
    display:inline-block;
  }
  .reserva-dot-ocupada{ background:#81412d; }
  .reserva-dot-reservada{ background:#b96a37; }
</style>
