  <div class="content-wrapper">

    <section class="content-header">
      <h1>
        Soporte Técnico 
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i>Inicio</a></li>
        <li class="active">Soporte Técnico</li>
      </ol>
    </section>


    <section class="content">

      <!-- Carga Masiva -->
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">Carga Masiva Domicilios</h3>

          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
                    title="Collapse">
              <i class="fa fa-minus"></i></button>
          </div>
        </div>
        <div class="box-body">
          Utilizar la opción de carga masiva para el catalogo de “cat_cp” solo si no se puede cargar mediante PHP MyAdmin, los campos a cargar son: 

          <ul>
            <li>Id_cp</li>
            <li>CódigoPostal</li>
            <li>Colonia</li>
            <li>Municipio</li>
            <li>Estado</li>
          </ul>

          Favor de dar click en "Seleccionar Archivo" para realizar la carga masiva
        </div>
        <div class="box-footer">
          <div class="progress">
            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width: 34%"></div>
          </div>
            <table class="table table-striped">
             <thead>
               <tr>
                <th><input type="file" id="Cargacp" name="Cp"></th>
                <th></th>
               </tr>
             </thead>
             <tbody>
               <tr>
                <td><i class="fa fa-exclamation-triangle"></i> Peso maximo del archivo 30MB</td>
                <td><button type="submit" class="btn btn-success pull-right" data-dismiss="modal">Importar</button></td>
               </tr>
             </tbody>
           </table> 
        </div>
      </div>
    </section>
  </div>