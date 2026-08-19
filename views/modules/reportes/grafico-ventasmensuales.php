<?php

$idnegocio = $_SESSION["IdUsuario"];

$Inventario = ControladorInventarios::crtObtenerStocksInventarios($idnegocio);

$VentaDiaria = ControladorVentas::crtObtenerSumaVentas($idnegocio);
$GananciaDiaria = ControladorVentas::crtObtenerSumaGanancias($idnegocio);

?>

<!--=====================================
GRÁFICO DE VENTAS
======================================-->

<div class="box box-solid bg-teal-gradient">
	
	<div class="box-header">
		
 		<i class="fa fa-th"></i>

  		<h3 class="box-title">Gráfico de Ventas Mensuales 2026</h3>
  		<input type="hidden" class="form-control" id="usuario" value="<?php echo $_SESSION["IdUsuario"]; ?>" readonly>

	</div>

	<div class="box-body border-radius-none nuevoGraficoVentas">

		<div class="chart" id="line-chart-ventas" style="height: 250px;"></div>

  </div>

</div>
