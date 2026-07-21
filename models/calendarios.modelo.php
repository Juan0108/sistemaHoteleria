<?php

require_once "conexion.php";
/**
 * 
 */
class ModeloCalendarios{

    static public function MdlObtenerCalendarios($idNegocio, $idEvento) {
        // Preparar la llamada al procedimiento almacenado con los parámetros
        $stmt = Conexion::conectar()->prepare("CALL GetEventos(:idNegocio, :idEvento)");

        // Asignar valores a los parámetros
        $stmt->bindParam(":idNegocio", $idNegocio, PDO::PARAM_INT);
        $stmt->bindParam(":idEvento", $idEvento, PDO::PARAM_INT);
        
        // Ejecutar y retornar los resultados
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Retorna un arreglo asociativo
    }

    static public function MdlObtenerCalendario($idNegocio, $idEvento){

        $stmt = Conexion::conectar()->prepare("CALL GetEventos('$idNegocio', '$idEvento')");
        $stmt -> execute();
        return $stmt -> fetch();
    }

    static public function MdlUpdateCalendario($evento) {
        $stmt = Conexion::conectar()->prepare("CALL UpdateComentario('$evento->id_evento','$evento->comentario')");

        if($stmt->execute()){
            return "ok";
    
        }else{
            return "error";
        }
    }

    static public function MdlInsertarEvento($evento){

        $stmt = Conexion::conectar()->prepare("CALL InsertEvento('$evento->nombreevento','$evento->diaevento','$evento->fechanotificacion','$evento->comentario','$evento->idAsistir','$evento->idUsuario','$evento->idNegocio','$evento->idEstatus')");
    
        $stmt -> execute();
    
        return $stmt -> fetch();
    }

    static public function MdlObtenerFechaHoy(){

        $stmt = Conexion::conectar()->prepare("CALL ObtenerFechaHoy()");
        $stmt -> execute();
        return $stmt -> fetch();
    }

}


