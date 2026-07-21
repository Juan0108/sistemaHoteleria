<?php


class usuario {

  private  $id_usuario;
  private  $nombre;
  private  $Apaterno;
  private  $Amaterno;
  private  $id_negocio;
  private  $Calle;
  private  $Id_estado;
  private  $Id_municipio;
  private  $id_colonia;
  private  $id_perfil;
  private  $usuario;
  private  $password;
  private  $foto;
  private  $id_estatus;

  function __construct($id_usuario,$nombre,$Apaterno,$Amaterno,$id_negocio,$Calle,$Id_estado,$Id_municipio,$id_colonia,$id_perfil,$usuario,$password,$foto,$id_estatus)
  {
    $this->id_usuario = $id_usuario;
    $this->nombre = $nombre;
    $this->Apaterno = $Apaterno;
    $this->Amaterno = $Amaterno;
    $this->id_negocio = $id_negocio;
    $this->Calle = $Calle;
    $this->Id_estado = $Id_estado;
    $this->Id_municipio = $Id_municipio;
    $this->id_colonia = $id_colonia;
    $this->id_perfil = $id_perfil;
    $this->usuario = $usuario;
    $this->password = $password;
    $this->foto = $foto;
    $this->id_estatus = $id_estatus;
  }


  function __get($propiedad){

    if(property_exists($this, $propiedad)){
      return $this->$propiedad;
    }
  }

  function __set($propiedad, $valor){

    if(property_exists($this, $propiedad)){

      $this->$propiedad = $valor;
    }
  }

}


?>