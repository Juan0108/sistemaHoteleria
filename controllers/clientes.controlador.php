<?php

class ControladorClientes
{
    /*=============================================
    CREAR CLIENTE
    =============================================*/
    public static function ctrCrearCliente()
    {
        if (isset($_POST["nuevoNombre"])) {
            $tabla = "cat_clientes";

            $datos = [
                "Nombre"   => $_POST["nuevoNombre"],
                "AMaterno" => $_POST["nuevoAMaterno"],
                "APaterno" => $_POST["nuevoAPaterno"],
                "Telefono" => $_POST["nuevoTelefono"],
                "Negocio" => $_POST["nuevoNegocio"]
            ];

            $respuesta = ModeloClientes::mdlIngresarCliente($tabla, $datos);

            return $respuesta;
        }
    }

    /*=============================================
    MOSTRAR CLIENTES
    =============================================*/
    public static function ctrMostrarClientes($item, $valor)
    {
        $tabla = "cat_clientes";
        return ModeloClientes::mdlMostrarClientes($tabla, $item, $valor);
    }

    /*=============================================
    EDITAR CLIENTE
    =============================================*/
    public static function ctrEditarCliente()
    {
        if (isset($_POST["editarNombre"])) {
            $tabla = "cat_clientes";

            $datos = [
                "id_Cliente" => $_POST["idCliente"],
                "Nombre"     => $_POST["editarNombre"],
                "AMaterno"   => $_POST["editarAMaterno"],
                "APaterno"   => $_POST["editarAPaterno"],
                "Telefono"   => $_POST["editarTelefono"]
            ];

            $respuesta = ModeloClientes::mdlEditarCliente($tabla, $datos);

            return $respuesta;
        }
    }

    /*=============================================
    ELIMINAR CLIENTE
    =============================================*/
    public static function ctrEliminarCliente()
    {
        if (isset($_POST["idEliminarCliente"])) {
            $tabla = "cat_clientes";
            $respuesta = ModeloClientes::mdlEliminarCliente($tabla, $_POST["idEliminarCliente"]);
            return $respuesta;
        }
    }
}
