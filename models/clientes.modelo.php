<?php

require_once "conexion.php";

class ModeloClientes
{
    /*=============================================
    CREAR CLIENTE
    =============================================*/
    public static function mdlIngresarCliente($tabla, $datos)
    {
        $stmt = Conexion::conectar()->prepare(
            "INSERT INTO $tabla (Nombre, AMaterno, APaterno, Telefono, Negocio) 
             VALUES (:Nombre, :AMaterno, :APaterno, :Telefono, :Negocio)"
        );

        $stmt->bindParam(":Nombre", $datos["Nombre"], PDO::PARAM_STR);
        $stmt->bindParam(":AMaterno", $datos["AMaterno"], PDO::PARAM_STR);
        $stmt->bindParam(":APaterno", $datos["APaterno"], PDO::PARAM_STR);
        $stmt->bindParam(":Telefono", $datos["Telefono"], PDO::PARAM_STR);
        $stmt->bindParam(":Telefono", $datos["Telefono"], PDO::PARAM_STR);
        $stmt->bindParam(":Negocio",  $datos["Negocio"], PDO::PARAM_STR);

        if ($stmt->execute()) {
            return "ok";
        } else {
            return "error";
        }

        $stmt = null;
    }

    /*=============================================
    MOSTRAR CLIENTES
    =============================================*/
    public static function mdlMostrarClientes($tabla, $item, $valor)
    {
        if ($item != null) {
            $stmt = Conexion::conectar()->prepare(
                "SELECT * FROM $tabla WHERE $item = $valor"
            );
            $stmt->execute();
            return $stmt->fetchAll();
        } else {
            $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla");
            $stmt->execute();
            return $stmt->fetchAll();
        }

        $stmt = null;
    }

    /*=============================================
    EDITAR CLIENTE
    =============================================*/
    public static function mdlEditarCliente($tabla, $datos)
    {
        $stmt = Conexion::conectar()->prepare(
            "UPDATE $tabla 
             SET Nombre = :Nombre, AMaterno = :AMaterno, APaterno = :APaterno, Telefono = :Telefono 
             WHERE id_Cliente = :id_Cliente"
        );

        $stmt->bindParam(":Nombre", $datos["Nombre"], PDO::PARAM_STR);
        $stmt->bindParam(":AMaterno", $datos["AMaterno"], PDO::PARAM_STR);
        $stmt->bindParam(":APaterno", $datos["APaterno"], PDO::PARAM_STR);
        $stmt->bindParam(":Telefono", $datos["Telefono"], PDO::PARAM_STR);
        $stmt->bindParam(":id_Cliente", $datos["id_Cliente"], PDO::PARAM_INT);

        if ($stmt->execute()) {
            return "ok";
        } else {
            return "error";
        }

        $stmt = null;
    }

    /*=============================================
    ELIMINAR CLIENTE
    =============================================*/
    public static function mdlEliminarCliente($tabla, $id)
    {
        $stmt = Conexion::conectar()->prepare(
            "DELETE FROM $tabla WHERE id_Cliente = :id_Cliente"
        );

        $stmt->bindParam(":id_Cliente", $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return "ok";
        } else {
            return "error";
        }

        $stmt = null;
    }

    /*=============================================
    OBTENER CLIENTE POR ID
    =============================================*/
    public static function MdlObtenerCliente($valor)
    {
        $stmt = Conexion::conectar()->prepare(
            "SELECT * FROM cat_clientes WHERE id_Cliente = :id_Cliente"
        );
        $stmt->bindParam(":id_Cliente", $valor, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }
}
