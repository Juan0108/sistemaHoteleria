  <div class="content-wrapper">

    <section class="content-header">
      <h1>
        Administrar Sub-Marcas
      </h1>
      <ol class="breadcrumb">
        <li><a href="inicio"><i class="fa fa-dashboard"></i>Inicio</a></li>
        <li class="active">Administar Sub-Marcas</li>
      </ol>
    </section>

    <section class="content">

      <div class="box">
        <div class="box-header with-border">
          <button class="btn btn-primary" data-toggle="modal" data-target="#modalAgregarSubMarca">
            Agregar Sub-Marca

          </button>

        </div>
        <div class="table-responsive">

           <table class="table table-bordered table-striped tablaSubMarcas style=text-align:center">
             <thead>
               <tr>
                <th>#</th>
                <th>Marca</th>
                <th>Sub-Marca</th>
                <th>Estatus</th>
                <th>Acciones</th>
               </tr>
             </thead>
           </table> 
        </div>
      </div>
    </section>
  </div>

<!-- Modal agregar SubMarca-->
<div id="modalAgregarSubMarca" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <form role="form" method="post" enctype="multipart/form-data">

      <div class="modal-header" style="background:#00a65a; color:white">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Agregar SubMarca</h4>
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
                    <span class="input-group-addon"><i class="fa fa-registered"></i> </span>
                    <select class="form-control input-sm" id="cbMarcas" required onchange="GetvalueMarcas();">
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
                    <input type="text" class="form-control input-sm" name="nuevaSubMarca" placeholder="SubMarca" required>
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
                        <label for="EstatusSubMarca">Estatus de la SubMarca</label>
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
        <button type="submit" class="btn btn-success">Guardar SubMarca</button>
      </div>

      <?php

      $InsertarSubMarca = new ControladorSubMarcas();
      $InsertarSubMarca -> ctrInsertarSubMarca();

      ?>
    </form>
    </div>
  </div>
</div>

<!-- Modal Editar SubMarca-->
<div id="modalEditarSubMarca" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <form role="form" method="post" enctype="multipart/form-data">

      <div class="modal-header" style="background:#00a65a; color:white">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Editar Marca</h4>
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
                    <span class="input-group-addon"><i class="fa fa-registered"></i> </span>
                    <select class="form-control input-sm" id="editarcbMarcas"  required disabled>
                      <option value="" selected hidden>--Seleccionar Marca--</option>
                      <input type="hidden" name="editaridMarca" id="editaridMarca" value="" required >
                      <input type="hidden" name="idsubmarca" id="idsubmarca" value required>
                    </select>
                  </div>
                </td>
              </tr>
              <tr>
                <td>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-cc"></i> </span>
                    <input type="text" class="form-control input-sm" name="editarsubMarca" id="editarsubMarca" value="" required >
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
                        <label for="EstatusCategoria">Estatus de la SubMarca</label>
                  </div>
                </td>
                <td>

                  <style>
                    .toggle.ios, .toggle-on.ios, .toggle-off.ios { border-radius: 20rem; }
                    .toggle.ios .toggle-handle { border-radius: 20rem; }
                  </style>

                  <div  style="text-align:center">
                    <input type="checkbox" checked data-toggle="toggle" data-style="ios" value="1" data-onstyle="success" data-offstyle="danger" name="editarEstatus" id="editarEstatus" data-on="Activo" data-off="In-Activo" data-width="100">
                  </div>
                </td>
               </tr>
             </tbody>
           </table> 
             
        </div> 
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary pull-left" data-dismiss="modal">Salir</button>
        <button type="submit" class="btn btn-success">Modificar SubMarca</button>
      </div>

      <?php

      $ActualizarSubMarca = new ControladorSubMarcas();
      $ActualizarSubMarca -> crtActualizarSubMarca();

      ?>
    </form>
    </div>
  </div>
</div>

