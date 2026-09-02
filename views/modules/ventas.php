  <div class="content-wrapper">

    <section class="content-header">
      <h1>
        Administrar Ventas

      </h1>

      <input type="hidden" class="form-control" id="usuario" value="<?php echo $_SESSION["IdUsuario"]; ?>" readonly>
      <ol class="breadcrumb">
        <li><a href="inicio"><i class="fa fa-dashboard"></i>Inicio</a></li>
        <li class="active">Administar Ventas</li>
      </ol>

    </section>

    <section class="content">
      <!-- Default box -->
      <div class="box">

        <div class="box-header with-border">
        
          <button type="button" class="btn btn-default pull-left" id="daterange-btn">          
              <span>
                <i class="fa fa-calendar"></i> Rango de Fechas
              </span>
              <i class="fa fa-caret-down"></i>
           </button>

          <input type="hidden" class="form-control" id="FInicio" readonly>
          <input type="hidden" class="form-control" id="Ffin" readonly>

          <button class="btn btn-success pull-right" onclick="ObtenerReporteVentas();">
              Generar reporte
          </button>

          <button class="btn btn-info pull-right" style="margin-right:8px;" onclick="ObtenerReporteVentasExcel();">
              Generar Excel
          </button>
        </div>

         <div class="table-responsive">
          <div class="box-body">

            <table class="table table-bordered table-striped tablaVentas style=text-align:center">
             <thead>
               <tr>
                <th>Id_Ventas</th>
                <th>Nticket</th>
                <th>Id_Producto</th>
                <th>Categoria</th>
                <th>Marca</th>
                <th>SubMarca</th>
                <th>Producto</th>
                <th>Clasificacion</th>
                <th>Gramaje</th>
                <th>Cantidad</th>
                <th>PrecioCompra</th>
                <th>PrecioVenta</th>
                <th>Ganancia</th>
                <th>Venta Total</th>
                <th>Fecha_Compra</th>
                <th>Vendedor</th>
                </tr>
             </thead>
           </table> 
          </div>
        </div>
      </div>
    </section>
  </div>