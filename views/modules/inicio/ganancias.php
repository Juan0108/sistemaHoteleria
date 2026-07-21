<?php

$idnegocio = $_SESSION["IdUsuario"];
$VentaDiaria = ControladorVentas::crtObtenerSumaVentas($idnegocio);
$GananciaDiaria = ControladorVentas::crtObtenerSumaGanancias($idnegocio);

?>


<div class="col-lg-3 col-xs-12">

  <div class="small-box bg-green">
    
    <div class="inner">
      
      <h2>$ <?php echo number_format($VentaDiaria["Venta Total"],2) ?></h2>

      <p>Venta diaria</p>

      <h2>$ <?php echo number_format($GananciaDiaria["Ganancia"],2) ?></h2>

      <p>Ganancias</p>
    
    </div>
    
    <div class="icon">
      
      <i class="ion ion-social-usd"></i>
    
    </div>
    
    <a href="ventas" class="small-box-footer">
      
      Más info <i class="fa fa-arrow-circle-right"></i>
    
    </a>

  </div>

</div>