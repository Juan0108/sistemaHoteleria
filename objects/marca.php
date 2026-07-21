<?php


class marca{

	private  $id_marca;
	private  $nombre;
  private  $descripcion;
  private  $id_categoria;
	private  $id_estatus;

  function __construct($id_marca,$nombre,$descripcion,$id_categoria,$id_estatus)
  {
  	$this->id_marca = $id_marca;
  	$this->nombre = $nombre;
    $this->descripcion = $descripcion;
    $this->id_categoria = $id_categoria;
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