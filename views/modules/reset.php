<?php

  $ctlUsuarioPass = new ControladorUsuarios();

  ?>

<div class="content-wrapper">
    <section class="content-header">
        <h1>
            Restablecer Contraseña
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
            <li class="active">Restablecer Contraseña</li>
        </ol>
    </section>

    <section class="content">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Formulario de Restablecimiento</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                        <i class="fa fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="box-body">
                <form id="resetForm" method="post">
                    <!-- Campo para la nueva contraseña -->
                    <div class="form-group">
                        <label for="password">Nueva Contraseña:</label>
                        <input type="password" class="form-control" id="password" name="newPassword" placeholder="Ingrese su nueva contraseña" required>
                        <p id="errorPassword" class="text-danger"></p>
                    </div>

                    <!-- Campo para confirmar la nueva contraseña -->
                    <div class="form-group">
                        <label for="confirmPassword">Confirmar Contraseña:</label>
                        <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" placeholder="Confirme su nueva contraseña" required>
                        <p id="errorConfirmPassword" class="text-danger"></p>
                    </div>

                    <!-- Campo oculto para el id_usuario -->
                    <input type="hidden" name="id_usuario" value="<?php echo $_SESSION['IdUsuario']; ?>">

                </form>
            </div>
            <div class="box-footer">
                <div class="buttons">
                    <a href="salir" class="btn btn-danger">Salir</a>
                    <button type="submit" class="btn btn-success" id="btnGuardar" form="resetForm">Guardar</button>
                </div>
                <?php

                $ctlUsuarioPass -> ctrActualizarContrasena();

                ?>
            </div>
        </div>
    </section>
</div>

<script src="views/js/usuarios.js"></script>
