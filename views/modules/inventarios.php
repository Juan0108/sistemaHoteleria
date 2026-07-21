  <div class="content-wrapper">

    <section class="content-header">
      <h1>
        Administrar Inventario
      </h1>
      <ol class="breadcrumb">
        <li><a href="inicio"><i class="fa fa-dashboard"></i>Inicio</a></li>
        <li class="active">Administar Inventario</li>
      </ol>
    </section>

    <section class="content">

      <div class="box">
        <div class="box-header with-border">
          <button class="btn btn-primary" data-toggle="modal" data-target="#modalAgregarProducto">
            Cargar Inventario 

          </button>

          <button class="btn btn-primary pull-right" onclick="ObtenerReporte();">
            Reporte de inventario 

          </button>
          <input type="hidden" class="form-control" id="usuario" value="<?php echo $_SESSION["IdUsuario"]; ?>" readonly>

        </div>
        <div class="table-responsive">

           <table class="table table-bordered table-striped tablaInventario style=text-align:center">
             <thead>
               <tr>
                <th>Id</th>
                <th>Código Barras</th>  
                <th>Categoría</th>
                <th>Marca</th>
                <th>Sub-Marca</th>
                <th>Producto</th>
                <th>Clasificación</th>
                <th>Gramaje</th>
                <th>Stock</th>
                <th>Precio Compra</th>
                <th>Precio Venta</th>
               </tr>
             </thead>
           </table> 
        </div>
      </div>
    </section>
  </div>

<!-- Modal agregar Producto-->
<div id="modalAgregarProducto" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <form role="form" method="post" enctype="multipart/form-data">

      <div class="modal-header" style="background:#00a65a; color:white">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Inventario</h4>
      </div>
      <div class="modal-body">
        <div class="box-body" >

           <table class="table table-striped">
             <thead>
               <tr>
                <th></th>
               </tr>
             </thead>
             <tbody>
              <tr>
                <td>
                  <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-barcode"></i> </span>
                      <input type="hidden" class="form-control" name="IdUsuario" value="<?php echo $_SESSION["IdUsuario"]; ?>" readonly>
                      <input type="text" class="form-control input-sm" name="Qbarra" id="Qbarra" placeholder="Buscar Producto" required onchange="GetValuesInventario();">
                      <input type="hidden" name="idInventario" id="idInventario" value>
                      <input type="hidden" class="form-control"  name="negocio" id="negocio" value="<?php echo $_SESSION["IdNegocio"]; ?>" readonly>
                  </div>
                </td>
              </tr>
               <tr>
                <td>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-th"></i> </span>
                    <input type="text" class="form-control input-sm" name="Categoria" id="Categoria" value="" placeholder="Categoria" disabled required>
                    </select>
                  </div>
                </td>
              </tr>
              <tr>
                <td>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-registered"></i> </span>
                    <input type="text" class="form-control input-sm" name="Marca" id="Marca" placeholder="Marca" disabled required>
                    </select>
                  </div>
                </td>
              </tr>
              <tr>
                <td>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-cc"></i> </span>
                    <input type="text" class="form-control input-sm" name="SuMarca" id="SuMarca" placeholder="SuMarca" disabled required>
                    </select>
                  </div>
                </td>
              </tr>
              <tr>
                <td>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-clone"></i> </span>
                    <input type="text" class="form-control input-sm" name="clasificacion" id="clasificacion" placeholder="clasificacion" disabled required>
                    </select>
                  </div>
                </td>
              </tr>
              <tr>
                <td>
                  <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-product-hunt"></i> </span>
                      <input type="text" class="form-control input-sm" name="Producto" id="Producto"  placeholder="Producto" disabled required>
                  </div>
                </td>
              </tr>
              <tr>
                <td>
                  <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-balance-scale"></i> </span>
                      <input type="text" class="form-control input-sm" name="Gramaje" id="Gramaje" placeholder="Gramaje" disabled required>
                  </div>
                </td>
              </tr>
              <tr>
                <table class="table table-striped">
                    <thead>
                       <tr>
                        <th></th>
                        <th></th>
                       </tr>
                     </thead>
                     <tbody>
                     <tr>
                      <td>
                        <label> Stock Actual:</label>
                        <div class="input-group">

                            <span class="input-group-addon"><i class="fa fa-cubes"></i></span>
                            
                            <input type="number" class="form-control input-sm" name="stockActual" id="stockActual" placeholder="stockActual" disabled>
                        </div>
                      </td> 
                      <td>
                        <label> Agregar Stock:</label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-cubes"></i></span>
                            <input type="number" min=0 class="form-control input-sm" name="stockNuevo" id="stockNuevo" placeholder="stockNuevo" required>
                        </div>
                      </td>                       
                      </tr>
                      <tr>
                        <td>
                        <label> Precio Compra:</label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-money"></i></span>
                            <input type="number" min=0 step="any" class="form-control input-sm" name="PrecioCompra" id="PrecioCompra" placeholder="Precio Compra" required>
                        </div>
                      </td> 
                      <td>
                        <label> Precio Venta:</label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-money"></i></span>
                            <input type="number" min=0 step="any" class="form-control input-sm" name="PrecioVenta" id="PrecioVenta"  placeholder="Precio Venta" required>
                        </div>
                      </td>
                      </tr>
                     </tbody>
                   </table>
              </tr>
             </tbody>
           </table>
             
        </div> 
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary pull-left" data-dismiss="modal">Salir</button>
        <button type="submit" class="btn btn-success" id="btnSave">
          <span class="ui-button-text">Cargar</span>
        </button>
      </div>

      <?php

      $InsertUpdateInventario = new ControladorInventarios();
      $InsertUpdateInventario -> ctrInsertarUpdateInventario();

      ?>
    </form>
    </div>
  </div>
</div>