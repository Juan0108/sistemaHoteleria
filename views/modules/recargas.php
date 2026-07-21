<div class="content-wrapper">

  <section class="content-header">
    <h1>
      Historial de Recargas Telefónicas

    </h1>

    <input type="hidden" class="form-control" id="usuario" value="<?php echo $_SESSION["IdUsuario"]; ?>" readonly>
    <ol class="breadcrumb">
      <li><a href="inicio"><i class="fa fa-dashboard"></i>Inicio</a></li>
      <li class="active">Historial de Recargas Telefónicas</li>
    </ol>

  </section>

  <section class="content">
    <!-- Default box -->
    <div class="box">

      <div class="box-header with-border">

        <input type="hidden" class="form-control" id="FInicio" readonly>
        <input type="hidden" class="form-control" id="Ffin" readonly>

        
        <!-- <button class="btn btn-success pull-right" onclick="ObtenerReporteGanancias();">
            Generar reporte 
        </button>-->
        
      </div>

       <div class="table-responsive">
        <div class="box-body">

          <table class="table table-bordered table-striped tablaRecargas style=text-align:center">
           <thead>
             <tr>
              <th>NTicket</th>
              <th>Id_Codigo</th>
              <th>Numero</th>
              <th>Folio</th>
              <th>Precio Venta</th>
              <th>Ganancia</th>
              <th>Fecha_Compra</th>
              </tr>
           </thead>
         </table> 
        </div>
      </div>
    </div>
  </section>
</div>