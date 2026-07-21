<?php


class categoria {

	private  $id_categoria;
	private  $nombre;
  private  $descripcion;
	private  $id_estatus;

  function __construct($id_categoria,$nombre,$descripcion,$id_estatus)
  {
  	$this->id_categoria = $id_categoria;
  	$this->nombre = $nombre;
    $this->descripcion = $descripcion;
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