  <?php

  $ctlUsuario = new ControladorUsuarios();

  ?>
  <div class="content-wrapper">

    <section class="content-header">
      <h1>
        Administar Usuarios
      </h1>
      <ol class="breadcrumb">
        <li><a href="inicio"><i class="fa fa-dashboard"></i>Inicio</a></li>
        <li class="active">Administar Usuarios</li>
      </ol>
    </section>

    <section class="content">

      <div class="box">
        <div class="box-header with-border">
          <button class="btn btn-primary" data-toggle="modal" data-target="#modalAgregarUsuario">
            Agregar Usuario

          </button>
        </div>

        <div class="table-responsive">
           <table class="table table-bordered table-striped tablas">
             <thead>
               <tr>
                <th>#</th>
                <th>Nombre</th>
                <th>Usuario</th>
                <th>Perfil</th>
                <th>Estatus</th>
                <th>Acciones</th>
               </tr>
             </thead>
             <tbody>

              <?php

               $Usuarios = ControladorUsuarios::crtObtenerUsuarios();
               if($_SESSION["Perfil"] =="Soporte Tecnico"){

                foreach ($Usuarios as $key => $value) {                 
                  echo '
                  <tr>
                    <td>'.$value["id_usuario"].'</td>
                    <td>'.$value["Nombre"].'</td>
                    <td>'.$value["Usuario"].'</td>
                    <td>'.$value["NombrePerfil"].'</td>
                    <td><button class="btn btn-warning btn-xs"><i class="fa fa-frown-o"></i></button>'.$value["Estatus"].'</td>
                    <td>
                      <div class="btn-group">
                        <button class="btn tn btn-info btnEditarUsuario" idUsuario="'.$value["Usuario"].'" data-toggle="modal" data-target="#modalEditarUsuario"><i class="fa fa-pencil"></i> </button>
                        <button class="btn btn-danger"><i class="fa fa-times"></i> </button>
                      </div>
                    </td>
                 </tr>'; 
                 }                
               }

              ?>
             </tbody>
           </table> 
        </div>
      </div>
    </section>
  </div>

<!-- Modal agregar Usuario-->
<div id="modalAgregarUsuario" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <form role="form" method="post" enctype="multipart/form-data">

      <div class="modal-header" style="background:#00a65a; color:white">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Agregar Usuario</h4>
      </div>
      <div class="modal-body">
        <div class="box-body" >

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
                    <span class="input-group-addon"><i class="fa fa-user"></i> </span>
                    <input type="text" class="form-control input-sm" name="nuevoNombre" placeholder="Nombre" required>
                  </div>
                </td>
                <td>
                 <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-users"></i> </span>
                    <select class="form-control input-sm" name="Perfil" required>
                      <option value="">Seleccionar Perfil</option>
                      <option value="1">Administrador</option>
                      <option value="2">Ventas</option>
                    </select>
                 </div>
                </td>
               </tr>
               <tr>
                <td>
                   <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-address-card"></i> </span>
                      <input type="text" class="form-control input-sm" name="Apaterno" placeholder="Apellido Paterno" required>
                  </div>
                </td>
                <td>
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-address-card"></i> </span>
                      <input type="text" class="form-control input-sm" name="Amaterno" placeholder="Apellido Materno" required>
                  </div>
                </td>
               </tr>
                <tr>
                <td>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-etsy"></i> </span>
                    <select class="form-control input-sm" name="NuevoEstado" id="cbestado" required>
                      <option value="" selected hidden>--Seleccionar Estado--</option>
                    </select>
                  </div>
                </td>
                <td>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-map-o"></i> </span>
                    <select class="form-control input-sm" name="NuevoMunicipio" id="cbmunicipio" required>
                      <option value="" selected hidden>--Seleccionar Municipio--</option>
                    </select>
                  </div>
                </td>
               </tr>
                <tr>
                <td>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-map-marker"></i> </span>
                    <select class="form-control input-sm" name="NuevaColonia" id="cbcolonia" required>
                      <option value="" selected hidden>--Seleccionar Colonia--</option>
                    </select>
                  </div>
                </td>
                <td>
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-street-view"></i> </span>
                      <input type="text" class="form-control input-sm" name="Calle" placeholder="Calle y Número" required>
                    </div>
                </td>
               </tr>
                <tr>
                <td>
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-envelope-open"></i> </span>
                      <input type="number" class="form-control input-sm" name="CodigoPostal" id="CodigoPostal" placeholder="Codigo Postal" readonly>
                    </div>
                </td>
               <td>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-university"></i> </span>
                    <select class="form-control input-sm" name="NuevoNegocio" id="cbnegocio" required>
                      <option value="" selected hidden>--Seleccionar Negocio--</option>
                    </select>
                  </div>
                </td>
               </tr>
                <tr>
                <td>
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-user-circle-o"></i> </span>
                      <input type="text" class="form-control input-sm" name="nuevoUsuario" placeholder="Ingresar Usuario" required>
                    </div>
                </td>
                <td>
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-lock"></i> </span>
                      <input type="password" class="form-control input-sm" name="Password" placeholder="Ingresar Password" required>
                    </div> 
                </td>
               </tr>
             </tbody>
           </table> 
            <div class="form-group">
              <div class="panel">CARGAR FOTO</div>
              <input type="file" class="nuevaFoto" name="nuevaFoto">
              <p class="help-block"><i class="fa fa-exclamation-triangle"></i> Peso maximo de la foto 3MB </p>
            </div>
        </div> 
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-primary pull-left" data-dismiss="modal">Salir</button>
        <button type="submit" class="btn btn-success">Guardar Usuario</button>
      </div>

      <?php

      $ctlUsuario -> crtInsertarUsuario();

      ?>
    </form>
    </div>
  </div>
</div>

<!-- Nuevo Modal Editar Usuario-->
<div id="modalEditarUsuario" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <form role="form" method="post" enctype="multipart/form-data">

      <div class="modal-header" style="background:#00a65a; color:white">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Editar Usuario</h4>
      </div>
      <div class="modal-body">
        <div class="box-body" >

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
                    <span class="input-group-addon"><i class="fa fa-user"></i> </span>
                    <input type="text" class="form-control input-sm" name="editarNombre" id="editarNombre" placeholder="Nombre" required>
                  </div>
                </td>
                <td>
                 <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-users"></i> </span>
                    <select class="form-control input-sm" name="editarPerfil" id="editarPerfil">
                      <option value="">Seleccionar Perfil</option>
                      <option value="1">Administrador</option>
                      <option value="2">Ventas</option>
                      <option value="3">Almacen</option>
                      <option value="4">Soporte Tecnico</option>
                    </select>
                 </div>
                </td>
               </tr>
               <tr>
                <td>
                   <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-address-card"></i> </span>
                      <input type="text" class="form-control input-sm" name="editarApaterno" id="editarApaterno" placeholder="Apellido Paterno" required>
                  </div>
                </td>
                <td>
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-address-card"></i> </span>
                      <input type="text" class="form-control input-sm" name="editarAmaterno" id="editarAmaterno" placeholder="Apellido Materno" required>
                  </div>
                </td>
               </tr>
                <tr>
                <td>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-etsy"></i> </span>
                    <select class="form-control input-sm" name="editarEstado" id="editarCbestado" required>
                      <option value="" selected hidden>--Seleccionar Estado--</option>
                    </select>
                  </div>
                </td>
                <td>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-map-o"></i> </span>
                    <select class="form-control input-sm" name="editarMunicipio" id="editarCbmunicipio" required>
                      <option value="" selected hidden>--Seleccionar Municipio--</option>
                    </select>
                  </div>
                </td>
               </tr>
                <tr>
                <td>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-map-marker"></i> </span>
                    <select class="form-control input-sm" name="editarColonia" id="editarCbcolonia" required>
                      <option value="" selected hidden>--Seleccionar Colonia--</option>
                    </select>
                  </div>
                </td>
                <td>
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-street-view"></i> </span>
                      <input type="text" class="form-control input-sm" name="editarCalle" id="editarCalle" placeholder="Calle y Número" required>
                    </div>
                </td>
               </tr>
                <tr>
                <td>
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-envelope-open"></i> </span>
                      <input type="number" class="form-control input-sm" name="editarCodigoPostal" id="editarCodigoPostal" placeholder="Codigo Postal" readonly>
                    </div>
                </td>
               <td>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-university"></i> </span>
                    <select class="form-control input-sm" name="editarNegocio" id="editarCbnegocio" required>
                      <option value="" selected hidden>--Seleccionar Negocio--</option>
                    </select>
                  </div>
                </td>
               </tr>
                <tr>
                <td>
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-user-circle-o"></i> </span>
                      <input type="text" class="form-control input-sm" name="editarUsuario" id="editarUsuario" placeholder="Ingresar Usuario" required readonly>
                    </div>
                </td>
                <td>
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-lock"></i> </span>
                      <input type="password" class="form-control input-sm" name="editarPassword" placeholder="Ingresar Password">
                    </div> 
                </td>
               </tr>
             </tbody>
           </table>
        </div> 
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-primary pull-left" data-dismiss="modal">Salir</button>
        <button type="submit" class="btn btn-success">Modificar Usuario</button>
      </div>

      <?php

      $ctlUsuario -> crtActualizarUsuario();

      ?>
    </form>
    </div>
  </div>
</div>