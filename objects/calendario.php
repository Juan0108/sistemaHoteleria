<?php

class calendario {

private $id_evento;
private $nombreevento;
private $diaevento;
private $fechanotificacion;
private $comentario;
private $idAsistir;
private $idUsuario;
private $idNegocio;
private $idEstatus;


function __construct($id_evento, $nombreevento, $diaevento, $fechanotificacion, $comentario, $idAsistir, $idUsuario, $idNegocio,$idEstatus) {
    $this->id_evento = $id_evento;
    $this->nombreevento = $nombreevento;
    $this->diaevento = $diaevento;
    $this->fechanotificacion = $fechanotificacion;
    $this->comentario = $comentario;
    $this->idAsistir = $idAsistir;
    $this->idUsuario = $idUsuario;
    $this->idNegocio = $idNegocio;
    $this->idEstatus = $idEstatus;
}

function __get($propiedad) {
    if(property_exists($this, $propiedad)) {
        return $this->$propiedad;
    }
}

function __set($propiedad, $valor) {
    if(property_exists($this, $propiedad)) {
        $this->$propiedad = $valor;
    }
}

}
?>