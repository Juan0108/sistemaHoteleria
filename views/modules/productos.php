  <div class="content-wrapper">

    <section class="content-header">
      <h1>
        Administrar Productos
      </h1>
      <ol class="breadcrumb">
        <li><a href="inicio"><i class="fa fa-dashboard"></i>Inicio</a></li>
        <li class="active">Administar Productos</li>
      </ol>
    </section>

    <section class="content">

      <div class="box">
        <div class="box-header with-border">
          <button class="btn btn-primary" data-toggle="modal" data-target="#modalAgregarProducto">
            Agregar Producto 

          </button>

        </div>
        <div class="table-responsive">

           <table class="table table-bordered table-striped tablaProductos style=text-align:center">
             <thead>
               <tr>
                <th>Código Barras</th>
                <th>Categoría</th>
                <th>Marca</th>
                <th>Sub-Marca</th>
                <th>Producto</th>
                <th>Clasificación</th>
                <th>Gramaje</th>
                <th>Estatus</th>
                <th>Acciones</th>
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
        <h4 class="modal-title">Agregar Producto</h4>
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
                    <span class="input-group-addon"><i class="fa fa-th"></i> </span>
                    <select class="form-control input-sm" id="cbcategoriaproductos" required onchange="GetvalueCategoria();">
                      <option value="" selected hidden>--Seleccionar Categoría--</option>
                      <input type="hidden" name="NidCategoria" id="NidCategoria" value required>
                    </select>
                  </div>
                </td>
              </tr>
              <tr>
                <td>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-registered"></i> </span>
                    <select class="form-control input-sm" id="cbMarcasActivas" required onchange="GetvalueMarca();"  >
                      <option value="" selected hidden>--Seleccionar Marca--</option>
                      <input type="hidden" name="NuevaidMarca" id="NuevaidMarca" value required>
                    </select>
                  </div>
                </td>
              </tr>
              <tr>
                <td>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-cc"></i> </span>
                    <select class="form-control input-sm" id="cbsubMarcasActivas" required onchange="GetvalueSubMarca();">
                      <option value="" selected hidden>--Seleccionar Sub-Marca--</option>
                      <input type="hidden" name="NuevaidSubMarca" id="NuevaidSubMarca" value required>
                    </select>
                  </div>
                </td>
              </tr>
              <tr>
                <td>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-clone"></i> </span>
                    <select class="form-control input-sm" id="cbclasificaciones" required onchange="GetvalueClasificacion();">
                      <option value="" selected hidden>--Seleccionar Clasificación--</option>
                      <input type="hidden" name="NuevaidClasificacion" id="NuevaidClasificacion" value required>
                    </select>
                  </div>
                </td>
              </tr>
              <tr>
                <td>
                  <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-product-hunt"></i> </span>
                      <input type="text" class="form-control input-sm" name="nuevaProducto"  placeholder="Producto" required>
                  </div>
                </td>
              </tr>
              <tr>
                <td>
                  <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-balance-scale"></i> </span>
                      <input type="text" class="form-control input-sm" name="nuevaGramaje"  placeholder="Gramaje" required>
                  </div>
                </td>
              </tr>
              <tr>
                <td>
                  <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-barcode"></i> </span>
                      <input type="text" class="form-control input-sm" name="nuevaQbarra" placeholder="Codigo Barras" required>
                  </div>
                </td>
              </tr>
             </tbody>
           </table>

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
                  <div class="input-group">
                        <label for="EstatusCategoria">Estatus del Producto</label>
                  </div>
                </td>
                <td>

                  <style>
                    .toggle.ios, .toggle-on.ios, .toggle-off.ios { border-radius: 20rem; }
                    .toggle.ios .toggle-handle { border-radius: 20rem; }
                  </style>

                  <div  style="text-align:center">
                    <input type="checkbox" checked data-toggle="toggle" data-style="ios" value="1" data-onstyle="success" data-offstyle="danger" name="nuevaEstatus" data-on="Activo" data-off="In-Activo" data-width="100">
                  </div>
                </td>
               </tr>
             </tbody>
           </table> 
             
        </div> 
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary pull-left" data-dismiss="modal">Salir</button>
        <button type="submit" class="btn btn-success">Guardar Producto</button>
      </div>

      <?php

      $InsertarProducto = new ControladorProductos();
      $InsertarProducto -> ctrInsertarProducto();

      ?>
    </form>
    </div>
  </div>
</div>

<!-- Modal Editar Producto-->
<div id="modalEditarProducto" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <form role="form" method="post" enctype="multipart/form-data">

      <div class="modal-header" style="background:#00a65a; color:white">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Editar Producto</h4>
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
                    <span class="input-group-addon"><i class="fa fa-th"></i> </span>
                    <select class="form-control input-sm" id="cbeditarcategoriaproductos" required onchange="GetvalueEditarCategorias();">
                      <option value="" selected hidden>--Seleccionar Categoría--</option>
                      <input type="hidden" name="editaridCategoria" id="editaridCategoria" value="" required>
                    </select>
                  </div>
                </td>
              </tr>
              <tr>
                <td>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-registered"></i> </span>
                    <select class="form-control input-sm" id="cbeditarMarcasActivas" required onchange="GetvalueEditarMarca();">
                      <option value="" selected hidden>--Seleccionar Marca--</option>
                      <input type="hidden" name="editaridMarca" id="editaridMarca" value="" required>
                    </select>
                  </div>
                </td>
              </tr>
              <tr>
                <td>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-cc"></i> </span>
                    <select class="form-control input-sm" id="cbeditarsubMarcasActivas" required onchange="GetvalueEditarSubMarca();">
                      <option value="" selected hidden>--Seleccionar Sub-Marca--</option>
                      <input type="hidden" name="editaridSubMarca" id="editaridSubMarca" value="" required>
                    </select>
                  </div>
                </td>
              </tr>
              <tr>
                <td>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-clone"></i> </span>
                    <select class="form-control input-sm" id="cbeditarclasificaciones" required onchange="GetvalueEditarClasificacion();">
                      <option value="" selected hidden>--Seleccionar Clasificación--</option>
                      <input type="hidden" name="editaridClasificacion" id="editaridClasificacion" value="" required>
                    </select>
                  </div>
                </td>
              </tr>
              <tr>
                <td>
                  <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-product-hunt"></i> </span>
                      <input type="text" class="form-control input-sm" name="editarProducto" id="editarProducto"  value required>
                  </div>
                </td>
              </tr>
              <tr>
                <td>
                  <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-balance-scale"></i> </span>
                      <input type="text" class="form-control input-sm" name="editarGramaje" id="editarGramaje" value required>
                  </div>
                </td>
              </tr>
              <tr>
                <td>
                  <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-barcode"></i> </span>
                      <input type="text" class="form-control input-sm" name="ProductoId" id="ProductoId" value  required>
                  </div>
                </td>
              </tr>
             </tbody>
           </table>

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
                  <div class="input-group">
                        <label for="EstatusCategoria">Estatus del Producto</label>
                  </div>
                </td>
                <td>

                  <style>
                    .toggle.ios, .toggle-on.ios, .toggle-off.ios { border-radius: 20rem; }
                    .toggle.ios .toggle-handle { border-radius: 20rem; }
                  </style>

                  <div  style="text-align:center">
                    <input type="checkbox" checked data-toggle="toggle" data-style="ios" value="1" data-onstyle="success" data-offstyle="danger" name="editarEstatus" id="editarEstatus"  data-on="Activo" data-off="In-Activo" data-width="100">
                  </div>
                </td>
               </tr>
             </tbody>
           </table> 
             
        </div> 
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary pull-left" data-dismiss="modal">Salir</button>
        <button type="submit" class="btn btn-success">Actualizar Producto</button>
      </div>

      <?php

      $ActualizarProducto = new ControladorProductos();
      $ActualizarProducto -> crtActualizarProducto();

      ?>
    </form>
    </div>
  </div>
</div>