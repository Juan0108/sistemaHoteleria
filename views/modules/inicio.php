  <div class="content-wrapper">

    <section class="content-header">
      <h1>
        Tablero de Control
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i>Inicio</a></li>
        <li class="active">Tablero de Control</li>
      </ol>
    </section>

    <section class="content">
      <div class="row">
        
      <?php
        if($_SESSION["Perfil"] =="Soporte Tecnico" || $_SESSION["Perfil"] =="Administrador" || $_SESSION["Perfil"] =="Ventas"){

          if($_SESSION["Perfil"] =="Ventas"){

            include "inicio/crearventa.php";
            include "inicio/inventario.php";
            // include "inicio/recargas.php"; // Oculto temporalmente

          }else{

            include "inicio/ganancias.php";
            include "inicio/crearventa.php";
            include "inicio/inventario.php";
            include "inicio/inversion.php";
            // include "inicio/recargas.php"; // Oculto temporalmente
            include "inicio/bitacoraInventario.php";
            
          }

        }
      ?>

      </div>

      <div class="row">
        
      <?php

        if($_SESSION["Perfil"] =="Soporte Tecnico" || $_SESSION["Perfil"] =="Administrador"){

          include "reportes/grafico-ventasmensuales.php";

        }

      ?>

      </div>

      <div class="row">
      <?php

        if($_SESSION["Perfil"] =="Soporte Tecnico"|| $_SESSION["Perfil"] =="Administrador"){

          include "reportes/grafico-balance.php";

        }

      ?>

      </div>
      <div class="row">
      <?php

        if($_SESSION["Perfil"] =="Soporte Tecnico" || $_SESSION["Perfil"] =="Administrador"){

          include "reportes/TopProductos-vendidos.php";

        }

      ?>

      </div> 
    </section>
  </div>