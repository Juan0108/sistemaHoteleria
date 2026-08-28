  <?php

  $ctlUsuario = new ControladorUsuarios();

  // Paleta de icono/color asignada de forma automática por nombre de rol,
  // para que cualquier rol nuevo (aún sin definir hoy) reciba una tarjeta e
  // insignia consistentes sin tener que tocar este archivo.
  $paletteRoles = [
    ["color" => "sand",     "icon" => "fa-user-circle-o"],
    ["color" => "espresso", "icon" => "fa-cogs"],
    ["color" => "rust",     "icon" => "fa-briefcase"],
    ["color" => "sienna",   "icon" => "fa-shield"],
  ];

  // Excepciones puntuales de ícono para roles conocidos (el color se sigue
  // asignando automáticamente por hash, esto solo afina el ícono).
  $iconosPorNombreRol = [
    "ventas"  => "fa-usd",
    "almacen" => "fa-archive",
  ];

  function estiloRol($paletteRoles, $iconosPorNombreRol, $nombreRol){
    $indice = abs(crc32($nombreRol)) % count($paletteRoles);
    $estilo = $paletteRoles[$indice];

    $claveIcono = strtolower(trim($nombreRol));
    if (isset($iconosPorNombreRol[$claveIcono])) {
      $estilo["icon"] = $iconosPorNombreRol[$claveIcono];
    }

    return $estilo;
  }

  ?>
  <div class="content-wrapper gestion-usuarios">

    <section class="content-header">
      <h1>
        Gestión de Personal
      </h1>
      <ol class="breadcrumb">
        <li><a href="inicio"><i class="fa fa-dashboard"></i>Inicio</a></li>
        <li class="active">Gestión de Personal</li>
      </ol>
    </section>

    <section class="content">

      <?php

       $Usuarios = ControladorUsuarios::crtObtenerUsuariosHotel();

       // Se consulta una sola vez y se reutiliza en los dos selects de Perfil (Agregar y
       // Editar) más abajo, en vez de abrir una conexión nueva a la BD remota por cada uno.
       $PerfilesDisponibles = ControladorUsuarios::crtObtenerPerfiles()->fetchAll(PDO::FETCH_ASSOC);

       if($_SESSION["Perfil"] =="Soporte Tecnico" || $_SESSION["Perfil"] == "Administrador"){

        // Agregados para las tarjetas: totales, activos y conteo dinámico por rol.
        $totalUsuarios = count($Usuarios);
        $usuariosActivos = 0;
        $rolesConteo = [];

        foreach ($Usuarios as $value) {
          $idEstatus = isset($value["id_estatus"]) ? (int) $value["id_estatus"] : 0;
          if ($idEstatus !== 2) {
            $usuariosActivos++;
          }

          $rol = isset($value["NombrePerfil"]) ? trim($value["NombrePerfil"]) : "";
          if ($rol === "") continue;

          if (!isset($rolesConteo[$rol])) {
            $rolesConteo[$rol] = 0;
          }
          $rolesConteo[$rol]++;
        }

      ?>

      <h1 class="gu-titulo">GESTIÓN DE PERSONAL</h1>
      <div class="gu-rule"></div>

      <div class="gu-tarjetas">
        <div class="gu-tarjeta">
          <span class="gu-tarjeta-icono gu-icono-sand"><i class="fa fa-users"></i></span>
          <div>
            <div class="gu-tarjeta-label">Total de personal</div>
            <div class="gu-tarjeta-valor"><?php echo $totalUsuarios; ?></div>
          </div>
        </div>
        <div class="gu-tarjeta">
          <span class="gu-tarjeta-icono gu-icono-rust"><i class="fa fa-user-plus"></i></span>
          <div>
            <div class="gu-tarjeta-label">Personal activo</div>
            <div class="gu-tarjeta-valor"><?php echo $usuariosActivos; ?></div>
          </div>
        </div>

        <?php foreach ($rolesConteo as $rol => $cantidad):
          $estilo = estiloRol($paletteRoles, $iconosPorNombreRol, $rol);
        ?>
        <div class="gu-tarjeta">
          <span class="gu-tarjeta-icono gu-icono-<?php echo $estilo["color"]; ?>"><i class="fa <?php echo $estilo["icon"]; ?>"></i></span>
          <div>
            <div class="gu-tarjeta-label"><?php echo htmlspecialchars($rol); ?></div>
            <div class="gu-tarjeta-valor"><?php echo $cantidad; ?></div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>

      <div class="gu-caja">
        <div class="gu-toolbar">
          <button class="gu-btn-registrar" data-toggle="modal" data-target="#modalAgregarUsuario">
            <i class="fa fa-plus"></i> Registrar personal
          </button>
          <div class="gu-buscar">
            <i class="fa fa-search"></i>
            <input type="text" id="buscarUsuario" placeholder="Buscar personal...">
          </div>
          <select id="filtroRol" class="gu-select">
            <option value="">Todos los roles</option>
            <?php foreach (array_keys($rolesConteo) as $rol): ?>
              <option value="<?php echo htmlspecialchars($rol); ?>"><?php echo htmlspecialchars($rol); ?></option>
            <?php endforeach; ?>
          </select>
          <select id="filtroEstado" class="gu-select">
            <option value="">Todos los estados</option>
            <option value="Activo">Activo</option>
            <option value="Inactivo">Inactivo</option>
          </select>
        </div>

        <div class="gu-tabla-wrap">
           <table class="tablas gu-tabla" id="tablaUsuariosHotel">
             <thead>
               <tr>
                <th>N.°</th>
                <th>Nombre completo</th>
                <th>Nombre de usuario</th>
                <th>Correo electrónico</th>
                <th>Rol</th>
                <th>Estado</th>
                <th>Acciones</th>
               </tr>
             </thead>
             <tbody>

              <?php

                foreach ($Usuarios as $key => $value) {
                  $idEstatus = isset($value["id_estatus"]) ? (int) $value["id_estatus"] : 0;
                  $activo = ($idEstatus !== 2);
                  $estatusTexto = $activo ? "Activo" : "Inactivo";
                  $estatusClase = $activo ? "gu-badge-activo" : "gu-badge-inactivo";
                  $rol = isset($value["NombrePerfil"]) ? $value["NombrePerfil"] : "";
                  $estiloRol = estiloRol($paletteRoles, $iconosPorNombreRol, $rol);
                  $correo = !empty($value["Correo"]) ? $value["Correo"] : "—";
                  $accionEstado = $activo
                    ? '<li><a href="javascript:void(0)" class="btnSuspender" idUsuario="'.$value["id_usuario"].'"><i class="fa fa-times"></i> Suspender personal</a></li>'
                    : '<li><a href="javascript:void(0)" class="btnActivar" idUsuario="'.$value["id_usuario"].'"><i class="fa fa-check"></i> Activar personal</a></li>';

                  echo '
                  <tr>
                    <td>'.($key + 1).'</td>
                    <td>'.htmlspecialchars($value["Nombre"]).'</td>
                    <td>'.htmlspecialchars($value["Usuario"]).'</td>
                    <td>'.htmlspecialchars($correo).'</td>
                    <td><span class="gu-badge-rol gu-icono-'.$estiloRol["color"].'">'.htmlspecialchars($rol).'</span></td>
                    <td><span class="gu-badge-estado '.$estatusClase.'">'.$estatusTexto.'</span></td>
                    <td>
                      <div class="gu-acciones">
                        <button class="gu-btn-editar btnEditarUsuario" idUsuario="'.htmlspecialchars($value["Usuario"]).'" data-toggle="modal" data-target="#modalEditarUsuario"><i class="fa fa-pencil"></i> Editar</button>
                        <div class="dropdown gu-dropdown">
                          <button class="gu-btn-kebab" type="button" data-toggle="dropdown">&#8942;</button>
                          <ul class="dropdown-menu dropdown-menu-right">
                            <li><a href="javascript:void(0)" class="btnReset" idUsuario="'.$value["id_usuario"].'"><i class="fa fa-unlock"></i> Restablecer contraseña</a></li>
                            '.$accionEstado.'
                          </ul>
                        </div>
                      </div>
                    </td>
                 </tr>';
                 }

              ?>
             </tbody>
           </table>
        </div>
      </div>

      <?php } ?>

    </section>
  </div>

<!-- Modal agregar Usuario-->
<div id="modalAgregarUsuario" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <form role="form" method="post" enctype="multipart/form-data">

      <div class="modal-header" style="background:#3f342e; color:#f6ecdb">
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
                      <?php foreach ($PerfilesDisponibles as $perfilOpcion): ?>
                        <option value="<?php echo (int) $perfilOpcion["Id_perfil"]; ?>"><?php echo htmlspecialchars($perfilOpcion["Nombre"]); ?></option>
                      <?php endforeach; ?>
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
                    <select class="form-control input-sm" name="NuevaColonia" id="cbcolonia">
                      <option value="" selected hidden>--Seleccionar Colonia--</option>
                    </select>
                    <input type="text" class="form-control input-sm" name="NuevaColoniaTexto" id="cbcoloniaTexto" placeholder="Escribe la colonia (no está en el catálogo)" style="display:none;">
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
                      <span class="input-group-addon"><i class="fa fa-envelope"></i> </span>
                      <input type="email" class="form-control input-sm" name="nuevoCorreo" placeholder="Correo electrónico" required>
                    </div>
                </td>
               </tr>
               <tr>
                <td>
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-lock"></i> </span>
                      <input type="password" class="form-control input-sm" name="Password" placeholder="Ingresar Password" required>
                    </div>
                </td>
                <td></td>
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
      <form role="form" method="post" enctype="multipart/form-data" id="formEditarUsuario">

      <div class="modal-header" style="background:#3f342e; color:#f6ecdb">
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
                <td rowspan="3" class="gu-editar-foto-celda">
                  <label class="gu-editar-foto-preview" id="editarFotoPreviewWrap" for="editarFoto" title="Haz clic para cambiar la foto">
                    <img id="editarFotoPreview" src="" alt="Foto del usuario" style="display:none;">
                    <span id="editarFotoPlaceholder" class="gu-editar-foto-placeholder"><i class="fa fa-camera"></i><small>Agregar foto</small></span>
                    <span class="gu-editar-foto-overlay"><i class="fa fa-pencil"></i></span>
                    <input type="file" class="editarFoto" name="editarFoto" id="editarFoto" style="position:absolute; width:1px; height:1px; opacity:0; pointer-events:none;">
                  </label>
                </td>
               </tr>
               <tr>
                <td>
                   <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-address-card"></i> </span>
                      <input type="text" class="form-control input-sm" name="editarApaterno" id="editarApaterno" placeholder="Apellido Paterno" required>
                  </div>
                </td>
               </tr>
                <tr>
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
                      <span class="input-group-addon"><i class="fa fa-street-view"></i> </span>
                      <input type="text" class="form-control input-sm" name="editarCalle" id="editarCalle" placeholder="Calle y Número" required>
                    </div>
                </td>
               </tr>
                <tr>
                <td>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-map-o"></i> </span>
                    <select class="form-control input-sm" name="editarMunicipio" id="editarCbmunicipio" required>
                      <option value="" selected hidden>--Seleccionar Municipio--</option>
                    </select>
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
                    <span class="input-group-addon"><i class="fa fa-map-marker"></i> </span>
                    <select class="form-control input-sm" name="editarColonia" id="editarCbcolonia">
                      <option value="" selected hidden>--Seleccionar Colonia--</option>
                    </select>
                    <input type="text" class="form-control input-sm" name="editarColoniaTexto" id="editarCbcoloniaTexto" placeholder="Escribe la colonia (no está en el catálogo)" style="display:none;">
                  </div>
                </td>
                <td>
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-envelope"></i> </span>
                      <input type="email" class="form-control input-sm" name="editarCorreo" id="editarCorreo" placeholder="Correo electrónico" required>
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
                      <span class="input-group-addon"><i class="fa fa-user-circle-o"></i> </span>
                      <input type="text" class="form-control input-sm" name="editarUsuario" id="editarUsuario" placeholder="Ingresar Usuario" required readonly>
                    </div>
                </td>
               </tr>
               <tr>
                <td>
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-lock"></i> </span>
                      <input type="password" class="form-control input-sm" name="editarPassword" placeholder="Ingresar Password">
                    </div>
                </td>
                <td>
                 <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-users"></i> </span>
                    <select class="form-control input-sm" name="editarPerfil" id="editarPerfil">
                      <option value="">Seleccionar Perfil</option>
                      <?php foreach ($PerfilesDisponibles as $perfilOpcion): ?>
                        <option value="<?php echo (int) $perfilOpcion["Id_perfil"]; ?>"><?php echo htmlspecialchars($perfilOpcion["Nombre"]); ?></option>
                      <?php endforeach; ?>
                    </select>
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

<style>
.content-wrapper.gestion-usuarios{
  --sand:#dfc6a2;
  --espresso:#3f342e;
  --rust:#b96a37;
  --sienna:#81412d;
  --accent:#81412d;
  --accent-hover:#b96a37;
  background-color:#f4efe4;
}

.gestion-usuarios .content-header h1{ display:none; }
.gu-titulo{ font-weight:800; text-transform:uppercase; letter-spacing:.5px; margin:0 0 6px; font-size:28px; color:var(--espresso); }
.gu-rule{ width:60px; height:4px; border-radius:2px; background:var(--accent); margin-bottom:22px; }

.gu-tarjetas{ display:grid; grid-template-columns:repeat(auto-fit, minmax(220px,1fr)); gap:16px; margin-bottom:20px; }
.gu-tarjeta{ background:#fff; border:1px solid #eee3d2; border-radius:12px; padding:18px 20px; display:flex; align-items:center; gap:14px; box-shadow:0 1px 3px rgba(63,52,46,.06); }
.gu-tarjeta-icono{ width:46px; height:46px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:18px; flex-shrink:0; }
.gu-tarjeta-label{ color:#8d7a68; font-size:13px; }
.gu-tarjeta-valor{ font-size:24px; font-weight:700; color:var(--espresso); }

.gu-icono-sand{ background:var(--sand); color:var(--espresso); }
.gu-icono-espresso{ background:#5a4a40; color:#f6ecdb; }
.gu-icono-rust{ background:var(--rust); color:#fff; }
.gu-icono-sienna{ background:var(--sienna); color:#fff; }

.gu-caja{ background:#fff; border:1px solid #eee3d2; border-radius:12px; padding:18px 20px; box-shadow:0 1px 3px rgba(63,52,46,.06); }

.gu-toolbar{ display:flex; flex-wrap:wrap; gap:10px; align-items:center; margin-bottom:16px; }
.gu-buscar{ position:relative; flex:1; min-width:220px; }
.gu-buscar i{ position:absolute; left:12px; top:50%; transform:translateY(-50%); color:#9c8a76; }
.gu-buscar input{ width:100%; padding:8px 12px 8px 32px; border:1px solid #e5d8bf; border-radius:8px; }
.gu-select{ padding:8px 12px; border:1px solid #e5d8bf; border-radius:8px; background:#fff; min-width:160px; }
.gu-btn-registrar{ background:var(--accent); color:#fff; border:none; border-radius:8px; padding:9px 16px; font-weight:600; transition:background .15s; }
.gu-btn-registrar:hover{ background:var(--accent-hover); color:#fff; }

.gu-tabla-wrap{ overflow-x:auto; }
.gu-tabla{ width:100%; border-collapse:collapse; }
.gu-tabla thead th{ text-align:left; color:#ffffff; font-size:11px; text-transform:uppercase; letter-spacing:.6px; padding:11px 12px; font-weight:700; background:var(--espresso); }
.gu-tabla thead tr:first-child th:first-child{ border-top-left-radius:8px; }
.gu-tabla thead tr:first-child th:last-child{ border-top-right-radius:8px; }
.gu-tabla tbody td{ padding:12px; border-bottom:1px solid #f2ece0; vertical-align:middle; }

.gu-badge-rol, .gu-badge-estado{ display:inline-block; padding:4px 13px; border-radius:20px; font-size:11.5px; font-weight:700; letter-spacing:.2px; }
.gu-badge-activo{ background:var(--sienna); color:#fff; }
.gu-badge-inactivo{ background:#a99884; color:#3f342e; }

.gu-acciones{ display:flex; align-items:center; gap:8px; }
.gu-btn-editar{ background:var(--accent); border:none; color:#fff; border-radius:6px; padding:6px 13px; font-weight:600; transition:background .15s; }
.gu-btn-editar:hover{ background:var(--accent-hover); color:#fff; }
.gu-btn-kebab{ background:transparent; border:none; color:#8d7a68; font-size:18px; line-height:1; padding:4px 8px; }
.gu-dropdown{ display:inline-block; position:relative; }
.gu-dropdown .dropdown-menu{ min-width:220px; }

.gestion-usuarios .dataTables_filter,
.gestion-usuarios .dataTables_length{ display:none; }
.gestion-usuarios .dataTables_info{ color:#8d7a68; font-size:13px; }
.gestion-usuarios .dataTables_wrapper .col-sm-5,
.gestion-usuarios .dataTables_wrapper .col-sm-7{ float:none; width:100%; }
.gestion-usuarios .dataTables_wrapper .dataTables_paginate{ margin-top:12px; text-align:center; }
.gestion-usuarios .pagination > li > a{ color:var(--espresso); }
.gestion-usuarios .pagination > li > a:hover{ color:#fff; background:var(--accent-hover); border-color:var(--accent-hover); }
.gestion-usuarios .pagination > .active > a,
.gestion-usuarios .pagination > .active > a:hover,
.gestion-usuarios .pagination > .active > a:focus{ background:var(--accent); border-color:var(--accent); color:#fff; }

/* Modales Agregar/Editar Usuario: mismo lenguaje de color que el resto de la pantalla */
#modalAgregarUsuario .modal-header .close,
#modalEditarUsuario .modal-header .close{ color:#f6ecdb; opacity:.75; text-shadow:none; }
#modalAgregarUsuario .modal-header .close:hover,
#modalEditarUsuario .modal-header .close:hover{ color:#fff; opacity:1; }

#modalAgregarUsuario .modal-footer .btn-primary,
#modalEditarUsuario .modal-footer .btn-primary{ background:#fff; border:1px solid #81412d; color:#81412d; }
#modalAgregarUsuario .modal-footer .btn-primary:hover,
#modalEditarUsuario .modal-footer .btn-primary:hover{ background:#81412d; color:#fff; }

#modalAgregarUsuario .modal-footer .btn-success,
#modalEditarUsuario .modal-footer .btn-success{ background:#81412d; border-color:#81412d; }
#modalAgregarUsuario .modal-footer .btn-success:hover,
#modalEditarUsuario .modal-footer .btn-success:hover{ background:#b96a37; border-color:#b96a37; }

/* Recuadro con la foto del usuario, dentro de la tabla del modal de edición,
   ocupando el espacio de las primeras 3 filas de la columna derecha */
.gu-editar-foto-celda{ text-align:center; vertical-align:middle !important; }

.gu-editar-foto-preview{
  position:relative; display:flex; align-items:center; justify-content:center;
  width:90%; max-width:150px; aspect-ratio:1/1; margin:0 auto;
  border-radius:10px; overflow:hidden; cursor:pointer;
  border:2px solid #81412d; background:#f6ecdb;
}
.gu-editar-foto-preview img{ width:100%; height:100%; object-fit:cover; display:block; }
.gu-editar-foto-placeholder{
  display:flex; flex-direction:column; align-items:center; justify-content:center;
  color:#81412d; gap:4px;
}
.gu-editar-foto-placeholder i{ font-size:22px; }
.gu-editar-foto-placeholder small{ font-size:10px; text-align:center; line-height:1.1; }
.gu-editar-foto-overlay{
  position:absolute; right:0; bottom:0; left:0; padding:4px 0; text-align:center;
  background:rgba(129,65,45,.85); color:#fff; font-size:12px;
  opacity:0; transition:opacity .15s ease;
}
.gu-editar-foto-preview:hover .gu-editar-foto-overlay{ opacity:1; }
</style>
