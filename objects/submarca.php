<?php


class submarca{

	private  $id_submarca;
	private  $nombre;
  private  $id_marca;
	private  $id_estatus;

  function __construct($id_submarca,$nombre,$id_marca,$id_estatus)
  {
  	$this->id_submarca = $id_submarca;
  	$this->nombre = $nombre;
    $this->id_marca = $id_marca;
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