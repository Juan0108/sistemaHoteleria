<?php 

$objCliente = new ControladorClientes();

?>
<div class="content-wrapper">

  <section class="content-header">
    <h1>
      Administrar Clientes
    </h1>
    <ol class="breadcrumb">
      <li><a href="inicio"><i class="fa fa-dashboard"></i>Inicio</a></li>
      <li class="active">Administrar Clientes</li>
    </ol>
  </section>

  <section class="content">

    <div class="box">
      <div class="box-header with-border">
        <button class="btn btn-primary" data-toggle="modal" data-target="#modalAgregarCliente">
          Agregar Cliente 
        </button>
      </div>

      <div class="table-responsive">

           <table class="table table-bordered table-striped tablaClientes style=text-align:center">
             <thead>
               <tr>
                <th>ID Cliente</th>
                <th>Nombre</th>
                <th>Apellido Paterno</th>
                <th>Apellido Materno</th>
                <th>Teléfono</th>
                <th>Acciones</th>
               </tr>
             </thead>
           </table> 
        </div>

    </div>
  </section>
</div>

<!-- Modal agregar Cliente-->
<div id="modalAgregarCliente" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <form role="form" method="post" enctype="multipart/form-data">

      <div class="modal-header" style="background:#00a65a; color:white">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Agregar Cliente</h4>
      </div>

      
      <div class="modal-body">
        <div class="box-body">

        <input type="hidden" class="form-control" name="nuevoNegocio" value="<?php echo $_SESSION["IdNegocio"]; ?>" readonly>

          <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-user"></i> </span>
            <input type="text" class="form-control input-sm" name="nuevoNombre" placeholder="Nombre" required>
          </div>

          <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-user"></i> </span>
            <input type="text" class="form-control input-sm" name="nuevoAPaterno" placeholder="Apellido Paterno" required>
          </div>

          <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-user"></i> </span>
            <input type="text" class="form-control input-sm" name="nuevoAMaterno" placeholder="Apellido Materno" required>
          </div>

          <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-phone"></i> </span>
            <input type="text" class="form-control input-sm" name="nuevoTelefono" data-inputmask='"mask": "(999) 999-9999"' data-mask required>
          </div>

        </div> 
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-primary pull-left" data-dismiss="modal">Salir</button>
        <button type="submit" class="btn btn-success">Guardar Cliente</button>
      </div>

      <?php
      $objCliente->ctrCrearCliente();
      ?>
    </form>
    </div>
  </div>
</div>

<!-- Modal editar Cliente-->
<div id="modalEditarCliente" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <form role="form" method="post">

      <div class="modal-header" style="background:#00a65a; color:white">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Modificar Cliente</h4>
      </div>
      <div class="modal-body">
        <div class="box-body">

          <input type="hidden" name="idCliente" id="editarIdCliente">

          <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-user"></i> </span>
            <input type="text" class="form-control input-sm" name="editarNombre" id="editarNombre" placeholder="Nombre" required>
          </div>

          <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-user"></i> </span>
            <input type="text" class="form-control input-sm" name="editarAPaterno" id="editarAPaterno" placeholder="Apellido Paterno" required>
          </div>

          <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-user"></i> </span>
            <input type="text" class="form-control input-sm" name="editarAMaterno" id="editarAMaterno" placeholder="Apellido Materno" required>
          </div>

          <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-phone"></i> </span>
            <input type="text" class="form-control input-sm" name="editarTelefono" id="editarTelefono" data-inputmask='"mask": "(999) 999-9999"' data-mask required>
          </div>

        </div> 
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-primary pull-left" data-dismiss="modal">Salir</button>
        <button type="submit" class="btn btn-success">Modificar Cliente</button>
      </div>

      <?php
      $objCliente->ctrEditarCliente();
      ?>
    </form>
    </div>
  </div>
</div>
