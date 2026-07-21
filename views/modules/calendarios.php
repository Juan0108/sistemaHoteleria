<style>
  /* Estilos para el calendario */
  #calendar {
    background-color: rgba(190, 202, 202, 0.63); /* Fondo blanco */
    border: 5px solid #ddd; /* Borde gris */
    padding: 15px; /* Espaciado interno */
    border-radius: 35px; /* Bordes redondeados */
    box-shadow: 0 2px 5px rgba(206, 71, 17, 0.1); /* Sombra suave */
    width: 100%; /* Reducir el ancho */
    margin: 0 auto; /* Centrar el calendario */
  }

  .fc-day:hover {
    background-color: rgb(230, 201, 149); /* Fondo gris claro al pasar el mouse */
  }

  .fc-toolbar-title {
    font-size: 1rem;
    color: rgb(71, 93, 216);
  }

  .fc-toolbar .fc-button {
    background-color: rgb(67, 118, 228);
    color: white;
  }

  .fc-toolbar {
    display: flex;
    flex-direction: column-reverse; /* Coloca el título abajo */
    align-items: center; /* Centrar el contenido horizontalmente */
    gap: 10px; /* Separación entre elementos */
  }

  /* Estilos para los modales */
  .modal-content {
    border-radius: 10px; /* Bordes redondeados para el modal */
  }

  .modal-header {
    background-color: #00a65a; /* Color de fondo del encabezado del modal */
    color: white; /* Color del texto */
    border-top-left-radius: 10px; /* Bordes redondeados en la parte superior */
    border-top-right-radius: 10px;
  }

  .modal-footer {
    border-bottom-left-radius: 10px; /* Bordes redondeados en la parte inferior */
    border-bottom-right-radius: 10px;
  }

  /* Estilos para los formularios dentro de los modales */
  .form-group {
    margin-bottom: 15px; /* Espacio entre los campos del formulario */
  }

  .form-group label {
    display: block; /* Asegura que el label esté encima del input */
    margin-bottom: 5px; /* Espacio entre el label y el input */
  }

  .form-control {
    width: 100%; /* Asegura que los inputs ocupen todo el ancho disponible */
    padding: 8px; /* Espaciado interno */
    border-radius: 5px; /* Bordes redondeados */
    border: 1px solid #ddd; /* Borde gris */
  }

  .select2 {
    width: 100% !important; /* Asegura que el select2 ocupe todo el ancho */
  }

  .select2-selection__choice {
    background-color: #f0f8ff; /* Color de fondo opcional */
    color:rgb(8, 8, 8) !important; /* Color del texto */
    border: 1px solid #007bff; /* Borde opcional */
  }

/* Cambia el color del icono "x" dentro de cada opción seleccionada */
  .select2-selection__choice__remove {
    color:rgb(13, 13, 14) !important;
  }

</style>

<div class="content-wrapper">
    <section class="content-header">
        <h1>Administrar Eventos</h1>
        <ol class="breadcrumb">
            <li><a href="inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
            <li class="active">Administrar Eventos</li>
        </ol>
    </section>

    <section class="content">
        <div class="box">
            <div class="box-header with-border">
                <button class="btn btn-primary" data-toggle="modal" data-target="#modalAgregarEvento">
                    Agregar Evento
                </button>
            </div>

            <input type="hidden" class="form-control" name="negocio" id="negocio" value="<?php echo $_SESSION["IdNegocio"]; ?>" readonly>
            <div class="table-responsive">
                <table class="table table-bordered table-striped tablaCalendario">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nombre</th>
                            <th>Día del Evento</th>
                            <th>Fecha de Notificación</th>
                            <th>Comentario</th>
                            <th>Estatus</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                </table>
            </div>
            <div class="table-responsive">
                <div id="calendar"></div>
            </div>
        </div>
    </section>
</div>

<!-- Modal agregar Evento -->
<div id="modalAgregarEvento" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <form role="form" method="post" enctype="multipart/form-data">
        <div class="modal-header" style="background:#00a65a; color:white">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Agregar Evento</h4>
          <input type="hidden" class="form-control" name="idnegocio" id="idnegocio" value="<?php echo $_SESSION["IdNegocio"]; ?>" readonly>
          <input type="hidden" class="form-control" name="idusuario" id="idusuario" value="<?php echo $_SESSION["IdUsuario"]; ?>" readonly>
        </div>
        <div class="modal-body">
          <div class="box-body">
            <div class="form-group">
              <label for="nombreEvento">Nombre del Evento</label>
              <input type="text" class="form-control" name="nombreEvento" placeholder="Ingresar nombre" required>
            </div>
            <div class="form-group">
              <label for="diasEvento">Días del Evento</label>
              <select class="select2" multiple data-placeholder="Selecciona los días de la semana" style="width: 100%;" id="diasEvento" name="diasEvento[]" required>
                <option value="1">Lunes</option>
                <option value="2">Martes</option>
                <option value="3">Miércoles</option>
                <option value="4">Jueves</option>
                <option value="5">Viernes</option>
                <option value="6">Sábado</option>
                <option value="7">Domingo</option>
              </select>
            </div>
            <div class="form-group">
              <div class="row">
                <div class="col-md-6">
                  <label for="horaNotificacion">Hora de Evento</label>
                  <input type="time" class="form-control" name="horaNotificacion" id="horaNotificacion" step="1800" placeholder="Seleccionar hora">
                </div>
                <div class="col-md-6">
                  <label for="recordatorio">Recordatorio Horas Antes</label>
                  <div class="input-group">
                            <input type="number" min=0 max=24 class="form-control input-sm" name="hrsAntes" id="hrsAntes" placeholder="Recordatorio Horas" required>
                            <span class="input-group-addon"><i class="fa fa-hourglass-end"></i></span>
                  </div>
                </div>
              </div>
            </div>

            <div class="form-group">
              <div class="row">
                <div class="col-md-6">
                <label for="ocurrencia">Ocurrencia</label>
                  <select class="form-control" name="Ocurrencia" id="Ocurrencia" required>
                    <option value="" selected hidden>--Selecciona tipo de Ocurrencia--</option>
                    <option value="Semanal">Semanal</option>
                    <option value="1">Mensual</option>
                    <option value="2">Bimestral</option>
                    <option value="6">Semestral</option>
                    <option value="12">Anual</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label for="comentario">Comentario</label>
              <textarea class="form-control" name="comentario" placeholder="Agregar un comentario" rows="3" required></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary pull-left" data-dismiss="modal">Salir</button>
          <button type="submit" class="btn btn-success">Guardar Evento</button>
        </div>

        <?php
        // Llamada al controlador para insertar el evento
         $InsertarEvento = new ControladorCalendarios();
         $InsertarEvento->ctrInsertarEvento();
        ?>

      </form>
    </div>
  </div>
</div>

<!-- Modal Editar Evento-->
<div id="modalEditarCalendario" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <form role="form" method="post" enctype="multipart/form-data">

        <div class="modal-header" style="background:#00a65a; color:white">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Editar Evento</h4>
        </div>

        <div class="modal-body">
          <div class="box-body">
            <!-- Campo oculto para enviar el ID del evento -->
            <input type="hidden" name="idEvento" id="idEvento" value="">

            <!-- Tabla para organizar los campos -->
            <table class="table table-striped">
              <tbody>
                <tr>
                  <td>
                    <!-- Campo para el Nombre -->
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-product-hunt"></i></span>
                      <input type="text" class="form-control input-sm" name="Nombre" id="Nombre" placeholder="Nombre" required disabled>
                    </div>
                  </td>
                </tr>
                <tr>
                  <td>
                    <!-- Campo para el Día del Evento -->
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                      <input type="text" class="form-control input-sm" name="DiaEvento" id="DiaEvento" placeholder="Día del Evento" required disabled>
                    </div>
                  </td>
                </tr>
                <tr>
                  <td>
                    <!-- Campo para editar el Comentario -->
                    <div class="form-group">
                      <label for="editarComentario">Comentario</label>
                      <input type="text" class="form-control" name="editarComentario" id="editarComentario" placeholder="Ingrese el comentario">
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-primary pull-left" data-dismiss="modal">Salir</button>
          <button type="submit" class="btn btn-success">Modificar Evento</button>
        </div>

        <?php

           $ActualizarMarca = new ControladorCalendarios();
           $ActualizarMarca -> crtActualizarComentario();
        
        ?>

      </form>
    </div>
  </div>
</div>