<?php

session_start();
require_once dirname(__DIR__) . "/models/usuarios.modelo.php";

if (isset($_SESSION["IniciarSesion"]) && $_SESSION["IniciarSesion"] == "ok" && isset($_SESSION["IdUsuario"])) {
  $usuarioSesion = ModeloUsuarios::MdlObtenerUsuario($_SESSION["IdUsuario"]);

  if ($usuarioSesion && isset($usuarioSesion["id_estatus"]) && $usuarioSesion["id_estatus"] == 2) {
    session_unset();
    session_destroy();
    echo '<script>window.location = "index.php";</script>';
    exit;
  }
}

?>


<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">

  <title>System POS DIT</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

  <!-- Plugin CSS -->
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="views/bower_components/bootstrap/dist/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="views/bower_components/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="views/bower_components/Ionicons/css/ionicons.min.css">
  <!-- Theme style --> 
  <link rel="stylesheet" href="views/dist/css/AdminLTE.css">
  <!-- AdminLTE Skins.-->
  <link rel="stylesheet" href="views/dist/css/skins/_all-skins.min.css">
    <!-- Theme style --> 
  <link rel="stylesheet" href="CSS/FrontExtra.css">
  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

     <!-- DataTables -->
  <link rel="stylesheet" href="views/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">

    <!-- Bootstrap time Picker -->
  <link rel="stylesheet" href="views/plugins/timepicker/bootstrap-timepicker.min.css">

   <!-- bootstrap datepicker -->
  <link rel="stylesheet" href="views/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">

     <!-- Daterange picker -->
  <link rel="stylesheet" href="views/bower_components/bootstrap-daterangepicker/daterangepicker.css">

  <!-- Morris chart -->
  <link rel="stylesheet" href="views/bower_components/morris.js/morris.css">

  <!-- Plugin JavaScript -->
  <!-- jQuery 3 -->
  <script src="views/bower_components/jquery/dist/jquery.min.js"></script>
  <!-- Bootstrap 3.3.7 -->
  <script src="views/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
  <!-- SlimScroll -->
  <script src="views/bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
  <!-- FastClick -->
  <script src="views/bower_components/fastclick/lib/fastclick.js"></script>
  <!-- AdminLTE App -->
  <script src="views/dist/js/adminlte.min.js"></script>

    <!-- DataTables -->
  <script src="views/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
  <script src="views/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>

  <!-- Alerts sweealert2 -->
  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
 

  <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">

    <!-- Select2 -->
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />


  <!-- FullCalendar CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css">
  
  <!-- InputMask -->
  <script src="views/plugins/input-mask/jquery.inputmask.js"></script>
  <script src="views/plugins/input-mask/jquery.inputmask.date.extensions.js"></script>
  <script src="views/plugins/input-mask/jquery.inputmask.extensions.js"></script>

  <!-- bootstrap time picker -->
  <script src="views/plugins/timepicker/bootstrap-timepicker.min.js"></script>

  <!-- bootstrap datepicker -->
  <script src="views/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
  <script src="views/bower_components/bootstrap-datepicker/dist/locales/bootstrap-datepicker.es.min.js"></script>

    <!-- daterangepicker http://www.daterangepicker.com/-->
  <script src="views/bower_components/moment/min/moment.min.js"></script>
  <script src="views/bower_components/bootstrap-daterangepicker/daterangepicker.js"></script>

  <!-- Morris.js charts http://morrisjs.github.io/morris.js/-->
  <script src="views/bower_components/raphael/raphael.min.js"></script>
  <script src="views/bower_components/morris.js/morris.min.js"></script>

  <!-- ChartJS http://www.chartjs.org/-->
  <script src="views/bower_components/chart.js/Chart.js"></script>

  <!-- Moment.js (dependencia de FullCalendar) -->
  

  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>



 

</head>

<!-- Cuerpo Documento -->
<body <?php echo '
class="hold-transition skin-red sidebar-collapse sidebar-mini login-page"'?>>

<?php

if(isset($_SESSION["IniciarSesion"]) && $_SESSION["IniciarSesion"] == "ok")
{
  echo '<div class="wrapper">';
    
    //Cabecera
    include "modules/cabezote.php";
    //Menu
    include "modules/menu.php";
    //inicio
    if(isset($_GET["ruta"])){

      if($_GET["ruta"] == "inicio" ||
         $_GET["ruta"] == "reset" ||
         $_GET["ruta"] == "hoteles" ||
         $_GET["ruta"] == "usuarios" ||
         $_GET["ruta"] == "categorias" ||
         $_GET["ruta"] == "marcas" ||
         $_GET["ruta"] == "submarcas" ||
         $_GET["ruta"] == "clasificaciones" ||
         $_GET["ruta"] == "productos" ||
         $_GET["ruta"] == "inventarios" ||
         $_GET["ruta"] == "habitaciones" ||
         $_GET["ruta"] == "recepcion" ||
         $_GET["ruta"] == "reservas" ||
         $_GET["ruta"] == "ventas" ||
         $_GET["ruta"] == "crearventas" ||
         $_GET["ruta"] == "reporte" ||
         $_GET["ruta"] == "soporte" ||
         $_GET["ruta"] == "ganancias" ||
         $_GET["ruta"] == "recargas" ||
         $_GET["ruta"] == "clientes" ||
         $_GET["ruta"] == "bitacoraInventario" ||
         $_GET["ruta"] == "salir"  ){
        include "modules/".$_GET["ruta"].".php";
      }else{

        include "modules/404.php";
      }
    } else{
      include "modules/inicio.php";
    }
    //Pie de pagina
    include "modules/footer.php";

    echo '</div>';
}else{

//Login
  include "modules/login.php";

}
?>
<script src="views/js/plantilla.js"></script>
<script src="views/js/conector.js"></script>
<script src="views/js/usuarios.js?v=<?php echo @filemtime(__DIR__ . "/js/usuarios.js"); ?>"></script>
<script src="views/js/categorias.js"></script>
<script src="views/js/marcas.js"></script>
<script src="views/js/submarcas.js"></script>
<script src="views/js/clasificaciones.js"></script>
<script src="views/js/productos.js"></script>
<script src="views/js/hoteles.js"></script>
<script src="views/js/inventarios.js"></script>
<script src="views/js/ventas.js?v=<?php echo @filemtime(__DIR__ . "/js/ventas.js"); ?>"></script>
<script src="views/js/habitaciones.js?v=<?php echo @filemtime(__DIR__ . "/js/habitaciones.js"); ?>"></script>
<script src="views/js/recepcion.js?v=<?php echo @filemtime(__DIR__ . "/js/recepcion.js"); ?>"></script>
<script src="views/js/reservas.js?v=<?php echo @filemtime(__DIR__ . "/js/reservas.js"); ?>"></script>
<script src="views/js/ventas.js"></script>
<script src="views/js/ganancias.js"></script>
<script src="views/js/recargas.js"></script>
<script src="views/js/clientes.js"></script>
<script src="views/js/bitacora.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>

</body>
</html>
