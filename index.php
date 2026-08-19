<?php

require_once "controllers/plantilla.controlador.php";
require_once "controllers/usuarios.controlador.php";
require_once "controllers/categorias.controlador.php";
require_once "controllers/marcas.controlador.php";
require_once "controllers/submarcas.controlador.php";
require_once "controllers/clasificaciones.controlador.php";
require_once "controllers/productos.controlador.php";
require_once "controllers/ventas.controlador.php";
require_once "controllers/hoteles.controlador.php";
require_once "controllers/inventarios.controlador.php";
require_once "controllers/webservice.controlador.php";
require_once "controllers/clientes.controlador.php";
require_once "controllers/habitaciones.controlador.php";
require_once "controllers/reservaciones.controlador.php";

require_once "models/usuarios.modelo.php";
require_once "models/categorias.modelo.php";
require_once "models/marcas.modelo.php";
require_once "models/submarcas.modelo.php";
require_once "models/clasificaciones.modelo.php";
require_once "models/productos.modelo.php";
require_once "models/ventas.modelo.php";
require_once "models/hoteles.modelo.php";
require_once "models/inventarios.modelo.php";
require_once "models/webservice.modelo.php";
require_once "models/clientes.modelo.php";
require_once "models/habitaciones.modelo.php";
require_once "models/reservaciones.modelo.php";

require_once "objects/usuario.php";
require_once "objects/categoria.php";
require_once "objects/marca.php";
require_once "objects/submarca.php";
require_once "objects/clasificacion.php";
require_once "objects/producto.php";
require_once "objects/inventario.php";
require_once "objects/hotel.php";
require_once "objects/habitaciones.php";

$plantilla = new ControladorPlantilla();
$plantilla -> ctrPlantilla();