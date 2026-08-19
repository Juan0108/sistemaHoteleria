<?php


class hotel {

  private  $id_hotel;
  private  $Razon_social;
  private  $Responsable;
  private  $Telefono;
  private  $Correo;
  private  $Id_giro;
  private  $Calle;
  private  $Id_estado;
  private  $Id_municipio;
  private  $id_colonia;
  private  $id_tipopago;
  private  $Fecha_alta;
  private  $Fecha_baja;
  private  $id_estatus;
  private  $SAire;
  private  $SServicios;

  function __construct($id_hotel, $Razon_social,  $Responsable, $Telefono,  $Correo,  $Id_giro, $Calle, $Id_estado, $Id_municipio,  $id_colonia,  $id_tipopago, $Fecha_alta,  $Fecha_baja,  $id_estatus, $SAire, $SServicios )

  {
    $this->id_hotel=$id_hotel;
    $this->Razon_social=$Razon_social;
    $this->Responsable=$Responsable;
    $this->Telefono=$Telefono;
    $this->Correo=$Correo;
    $this->Id_giro=$Id_giro;
    $this->Calle=$Calle;
    $this->Id_estado=$Id_estado;
    $this->Id_municipio=$Id_municipio;
    $this->id_colonia=$id_colonia;
    $this->id_tipopago=$id_tipopago;
    $this->Fecha_alta=$Fecha_alta;
    $this->Fecha_baja=$Fecha_baja;
    $this->id_estatus=$id_estatus;
    $this->SAire=$SAire;
    $this->SServicios=$SServicios;
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