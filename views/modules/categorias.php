  <div class="content-wrapper">

    <section class="content-header">
      <h1>
        Administar Categorías
      </h1>
      <ol class="breadcrumb">
        <li><a href="inicio"><i class="fa fa-dashboard"></i>Inicio</a></li>
        <li class="active">Administar Categorías</li>
      </ol>
    </section>

    <section class="content">

      <div class="box">
        <div class="box-header with-border">
          <button class="btn btn-primary" data-toggle="modal" data-target="#modalAgregarCategoria">
            Agregar Categoría

          </button>

        </div>
        <div class="box-body">

           <table class="table table-bordered table-striped tablaCategorias style=text-align:center">
             <thead>
               <tr>
                <th>#</th>
                <th>Categoría</th>
                <th>Descripción</th>
                <th>Estatus</th>
                <th>Acciones</th>
               </tr>
             </thead>
           </table> 
        </div>
      </div>
    </section>
  </div>

<!-- Modal agregar Categoria-->
<div id="modalAgregarCategoria" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <form role="form" method="post" enctype="multipart/form-data">

      <div class="modal-header" style="background:#00a65a; color:white">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Agregar Categoría</h4>
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
                    <input type="text" class="form-control input-sm" name="nuevaCategoria" placeholder="Categoría" required>
                  </div>
                </td>
              </tr>
              <tr>
                <td>
                  <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-address-card"></i> </span>
                      <input type="text" class="form-control input-sm" name="nuevaDescripcion" placeholder="Descripcion" required>
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
                        <label for="EstatusCategoria">Estatus de la categoría</label>
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
        <button type="submit" class="btn btn-success">Guardar Categoría</button>
      </div>

      <?php

      $InsertarCategoria = new ControladorCategorias();
      $InsertarCategoria -> ctrInsertarCategoria();

      ?>
    </form>
    </div>
  </div>
</div>

<!-- Editar Categoria-->
<div id="modalEditarCategoria" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <form role="form" method="post" enctype="multipart/form-data">

      <div class="modal-header" style="background:#00a65a; color:white">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Editar Categoría</h4>
      </div>
      <div class="modal-body">
        <div class="box-body">

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
                    <input type="text" class="form-control input-sm" name="editarCategoria" id="editarCategoria" value required>
                    <input type="hidden" name="idCategoria" id="idCategoria" value required>
                  </div>
                </td>
              </tr>
              <tr>
                <td>
                  <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-address-card"></i> </span>
                      <input type="text" class="form-control input-sm" name="editarDescripcion" id="editarDescripcion" value=""  required>
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
                        <label for="EstatusCategoria">Estatus de la categoría</label>
                  </div>
                </td>
                <td>

                  <style>
                    .toggle.ios, .toggle-on.ios, .toggle-off.ios { border-radius: 20rem; }
                    .toggle.ios .toggle-handle { border-radius: 20rem; }
                  </style>
                  <div  style="text-align:center">
                    <input type="checkbox" name="editarEstatus" id="editarEstatus" data-style="ios" data-toggle="toggle" data-onstyle="success" data-offstyle="danger" data-on="Activo" data-off="In-Activo" data-width="100">
                  </div>
                </td>
               </tr>
             </tbody>
           </table> 
        </div> 
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-primary pull-left" data-dismiss="modal">Salir</button>
        <button type="submit" class="btn btn-success">Modificar Categoría</button>
      </div>

      <?php

      $ActualizarCategoria = new ControladorCategorias();
      $ActualizarCategoria -> crtActualizarCategoria();

      ?>

    </form>
    </div>
  </div>
</div>