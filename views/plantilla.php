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
  <!-- Sin esto, en navegadores/SO con tema oscuro se ve un flash de pantalla
       negra al recargar (justo antes de que cargue el CSS del sitio, que es
       claro): el navegador asume fondo oscuro por defecto si no se le dice
       lo contrario. -->
  <meta name="color-scheme" content="light">

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
  <link rel="stylesheet" href="CSS/FrontExtra.css?v=<?php echo filemtime(__DIR__ . '/../CSS/FrontExtra.css'); ?>">
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
<body class="hold-transition skin-blue sidebar-collapse sidebar-mini<?php echo (isset($_SESSION["IniciarSesion"]) && $_SESSION["IniciarSesion"] == "ok") ? "" : " login-page"; ?>">

<style>
  /* Spinner global: cambio de pantalla (clic en un link), recarga (F5) y cualquier
     petición AJAX de cualquier módulo (búsquedas, filtros, guardados, etc.). */
  .global-load-overlay{
    position:fixed;
    inset:0;
    background:rgba(255,255,255,.55);
    z-index:999999;
    display:flex;
    align-items:center;
    justify-content:center;
    opacity:0;
    visibility:hidden;
    pointer-events:none;
    transition:opacity .15s ease;
  }
  .global-load-overlay.global-load-visible{
    opacity:1;
    visibility:visible;
    pointer-events:all;
  }
  .global-load-spinner{
    font-size:52px;
    color:#3f342e;
  }
</style>

<div class="global-load-overlay global-load-visible" id="globalLoadOverlay">
  <i class="fa fa-refresh fa-spin global-load-spinner"></i>
</div>
<script>
(function(){

  // Cuenta cuántas "razones" hay para seguir mostrando el spinner (la carga de esta
  // misma página, más cualquier petición AJAX en curso), y solo lo oculta cuando ya
  // no queda ninguna. Independiente de jQuery para que la parte de navegación/recarga
  // nunca se quede pegada si algo más en la página falla; jQuery se usa solo para
  // engancharse a los eventos globales de AJAX.
  var contador = 1; // 1 = "esta página todavía se está terminando de cargar"
  var _cargaInicialResuelta = false;
  var _inicioCarga = Date.now();
  var _minimoVisibleMs = 400;

  function pintar(){
    var _overlay = document.getElementById("globalLoadOverlay");
    if(!_overlay) return;
    if(contador > 0) _overlay.classList.add("global-load-visible");
    else _overlay.classList.remove("global-load-visible");
  }

  function iniciar(){
    contador++;
    pintar();
  }

  function terminar(){
    contador = Math.max(0, contador - 1);
    pintar();
  }

  // Con todo ya en caché del navegador la página puede cargar tan rápido que el
  // overlay se oculte casi al instante y no dé tiempo de percibirlo; se fuerza un
  // mínimo de tiempo visible, sin importar qué tan rápido cargue.
  function resolverCargaInicial(){
    if(_cargaInicialResuelta) return;
    _cargaInicialResuelta = true;

    var _faltante = _minimoVisibleMs - (Date.now() - _inicioCarga);

    if(_faltante > 0){
      setTimeout(terminar, _faltante);
    }else{
      terminar();
    }
  }

  document.addEventListener("DOMContentLoaded", resolverCargaInicial);
  window.addEventListener("load", resolverCargaInicial);
  setTimeout(resolverCargaInicial, 6000); // failsafe: nunca se queda pegado

  // Antes de navegar a otra pantalla (clic en un link, submit que redirige, F5).
  window.addEventListener("beforeunload", function(){
    contador = 1;
    pintar();
  });

  window.mostrarSpinnerGlobal = iniciar;
  window.ocultarSpinnerGlobal = terminar;

  // jQuery ya está cargado (viene en el <head>, antes de este <body>). Cualquier
  // $.ajax() de cualquier módulo (búsquedas, filtros, guardados...) prende el
  // spinner, salvo que se llame explícitamente con { global: false }.
  if(window.jQuery){
    jQuery(document).ajaxStart(iniciar).ajaxStop(terminar);
  }

})();
</script>

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
         $_GET["ruta"] == "preguntas" ||
         $_GET["ruta"] == "tareas" ||
         $_GET["ruta"] == "servicio" ||
         $_GET["ruta"] == "recepcion" ||
         $_GET["ruta"] == "reservas" ||
         $_GET["ruta"] == "mantenimiento" ||
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
<script src="views/js/plantilla.js?v=<?php echo @filemtime(__DIR__ . "/js/plantilla.js"); ?>"></script>
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
<script src="views/js/preguntas.js?v=<?php echo @filemtime(__DIR__ . "/js/preguntas.js"); ?>"></script>
<script src="views/js/tareas.js?v=<?php echo @filemtime(__DIR__ . "/js/tareas.js"); ?>"></script>
<script src="views/js/servicio.js?v=<?php echo @filemtime(__DIR__ . "/js/servicio.js"); ?>"></script>
<script src="views/js/recepcion.js?v=<?php echo @filemtime(__DIR__ . "/js/recepcion.js"); ?>"></script>
<script src="views/js/reservas.js?v=<?php echo @filemtime(__DIR__ . "/js/reservas.js"); ?>"></script>
<script src="views/js/mantenimiento.js?v=<?php echo @filemtime(__DIR__ . "/js/mantenimiento.js"); ?>"></script>
<script src="views/js/ganancias.js"></script>
<script src="views/js/recargas.js"></script>
<script src="views/js/clientes.js"></script>
<script src="views/js/bitacora.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>

</body>
</html>
