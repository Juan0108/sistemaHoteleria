<div id="back"></div>
<div class="login-box">

    <div class="login-box-body">

        <div class="login-logo">
            <h1 style="color:Black" ; align="center"><b>Sistema Hotelería</b></h1>
        </div>
        
        <form method="post">
            <div class="form-group has-feedback">
                <input type="text" name="ingUsuario" class="form-control" placeholder="Usuario" required>
                <span class="glyphicon glyphicon-user form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <input type="password" name="ingPassword" class="form-control" placeholder="Contraseña" required>
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>
            <button type="submit" class="btn btn-primary btn-block btn-flat">Ingresar</button>
            <?php
            $login = new ControladorUsuarios();
            $login->ctrIngresoUsuario();
            ?>
        </form>
    </div>
</div>
</div>