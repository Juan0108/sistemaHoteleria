  <div class="content-wrapper">

    <section class="content-header">
      <h1>
        Bitácora Inventario
      </h1>
      <ol class="breadcrumb">
        <li><a href="inicio"><i class="fa fa-dashboard"></i>Inicio</a></li>
        <li class="active">Bitácora Inventario</li>
      </ol>
    </section>

    <section class="content">

      <div class="box">
        <div class="box-header with-border">

            <button type="button" class="btn btn-default pull-left" id="daterange-btn">          
              <span>
                <i class="fa fa-calendar"></i> Rango de Fechas
              </span>
              <i class="fa fa-caret-down"></i>
           </button>

          <input type="hidden" class="form-control" id="usuario" value="<?php echo $_SESSION["IdUsuario"]; ?>" readonly>

        </div>
        
        <div class="table-responsive">


           <table class="table table-bordered table-striped tablaBitacoraInventario style=text-align:center">
             <thead>
               <tr>
                <th>Vendedor</th>
                <th>Acción</th>  
                <th>Fecha</th>
                <th>Detalle</th>
               </tr>
             </thead>
           </table> 
        </div>
      </div>
    </section>
  </div>