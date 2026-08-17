<?php

header('Content-Type: application/json');

require_once "../models/usuarios.modelo.php";
require_once "../controllers/usuarios.controlador.php";

class resetContraAjax
{
    public $idUsuario;
    public $newPassword;

    public function ajaxResetContra()
    {
        $id_usuario = (int) $this->idUsuario;
        $password = crypt($this->newPassword, '$2a$07$asxx54ahjppf45sd87a5a4dDDGsystemdev$');

        $respuesta = ModeloUsuarios::MdlResetPassword($id_usuario, $password);

        if ($respuesta) {
            echo json_encode([
                "status" => "success",
                "message" => "Contraseña reseteada correctamente"
            ]);
        } else {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => "No se pudo resetear la contraseña"
            ]);
        }
    }
}

if (isset($_POST["idUsuario"]) && isset($_POST["newPassword"])) {
    $reset = new resetContraAjax();
    $reset->idUsuario = $_POST["idUsuario"];
    $reset->newPassword = $_POST["newPassword"];
    $reset->ajaxResetContra();
} else {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "message" => "Faltan datos para resetear la contraseña"
    ]);
}