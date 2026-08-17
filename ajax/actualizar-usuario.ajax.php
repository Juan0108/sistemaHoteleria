<?php

session_start();
header('Content-Type: application/json');

require_once "../controllers/usuarios.controlador.php";
require_once "../models/usuarios.modelo.php";
require_once "../objects/usuario.php";

echo json_encode(ControladorUsuarios::crtActualizarUsuarioAjax());
