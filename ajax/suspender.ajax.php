<?php

header('Content-Type: application/json');

require_once "../models/usuarios.modelo.php";

class SuspenderUsuarioAjax
{
    public $idUsuario;

    private function obtenerIdUsuarioDesdeSolicitud()
    {
        if (isset($this->idUsuario) && $this->idUsuario !== "") {
            return $this->idUsuario;
        }

        if (isset($_POST["idUsuario"]) && $_POST["idUsuario"] !== "") {
            return $_POST["idUsuario"];
        }

        if (isset($_POST["id"]) && $_POST["id"] !== "") {
            return $_POST["id"];
        }

        if (isset($_REQUEST["idUsuario"]) && $_REQUEST["idUsuario"] !== "") {
            return $_REQUEST["idUsuario"];
        }

        if (isset($_REQUEST["id"]) && $_REQUEST["id"] !== "") {
            return $_REQUEST["id"];
        }

        $rawInput = file_get_contents("php://input");
        if ($rawInput !== false && $rawInput !== "") {
            $decoded = json_decode($rawInput, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                if (!empty($decoded["idUsuario"])) {
                    return $decoded["idUsuario"];
                }

                if (!empty($decoded["id"])) {
                    return $decoded["id"];
                }
            }

            parse_str($rawInput, $parsedInput);
            if (!empty($parsedInput["idUsuario"])) {
                return $parsedInput["idUsuario"];
            }

            if (!empty($parsedInput["id"])) {
                return $parsedInput["id"];
            }
        }

        return null;
    }

    public function ajaxSuspenderUsuario()
    {
        $id_usuario = (int) $this->obtenerIdUsuarioDesdeSolicitud();

        if ($id_usuario <= 0) {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => "Faltan datos para suspender el usuario"
            ]);
            return;
        }

        $respuesta = ModeloUsuarios::MdlSuspenderUsuario($id_usuario);

        if ($respuesta) {
            echo json_encode([
                "status" => "success",
                "message" => "Usuario suspendido correctamente"
            ]);
        } else {
            http_response_code(400);
            echo json_encode([
                "status" => "error",
                "message" => "No se pudo suspender el usuario"
            ]);
        }
    }
}

if (!empty($_POST) || !empty($_REQUEST) || !empty(file_get_contents("php://input"))) {
    $suspender = new SuspenderUsuarioAjax();
    $suspender->idUsuario = null;
    $suspender->ajaxSuspenderUsuario();
} else {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "message" => "Faltan datos para suspender el usuario"
    ]);
}
