<?php

$idnegocio = $_SESSION["IdUsuario"];
$Inventario = ControladorInventarios::crtObtenerStocksInventarioHoteleria($idnegocio);

?>

<div class="col-lg-3 col-xs-6">

  <div class="small-box bg-yellow">
    
    <div class="inner">
    
      <h2><?php echo number_format($Inventario["SUM(Stock)"]); ?></h2>

      <p>Stock's en Inventario</p>
  
    </div>
    
    <div class="icon">
    
      <i class="ion-social-dropbox"></i>
    
    </div>
    
    <a href="inventarios" class="small-box-footer">

      Más info <i class="fa fa-arrow-circle-right"></i>

    </a>

  </div>

</div>