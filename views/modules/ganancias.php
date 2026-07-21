  <div class="content-wrapper">

    <section class="content-header">
      <h1>
        Inversión VS Ganancias

      </h1>

      <input type="hidden" class="form-control" id="usuario" value="<?php echo $_SESSION["IdUsuario"]; ?>" readonly>
      <ol class="breadcrumb">
        <li><a href="inicio"><i class="fa fa-dashboard"></i>Inicio</a></li>
        <li class="active">Inversión VS Ganancia</li>
      </ol>

    </section>

    <section class="content">
      <!-- Default box -->
      <div class="box">

        <div class="box-header with-border">

          <input type="hidden" class="form-control" id="FInicio" readonly>
          <input type="hidden" class="form-control" id="Ffin" readonly>

          <button class="btn btn-success pull-right" onclick="ObtenerReporteGanancias();">
              Generar reporte 
          </button>
        </div>

         <div class="table-responsive">
          <div class="box-body">

            <table class="table table-bordered table-striped tablaGanancias style=text-align:center">
             <thead>
               <tr>
                <th>Marca</th>
                <th>SubMarca</th>
                <th>Clasificacion</th>
                <th>Stock</th>
                <th>Precio Compra</th>
                <th>Compra Total</th>
                <th>Precio Venta</th>
                <th>Venta Total</th>
                <th>Ganancia</th>
                </tr>
             </thead>
           </table> 
          </div>
        </div>
      </div>
    </section>
  </div>