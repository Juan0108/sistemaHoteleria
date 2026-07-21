<?php


class producto{

	private  $id_producto;
	private  $nombre;
  private  $gramaje;
  private  $id_categoria;
  private  $id_marca;
  private  $id_submarca;
  private  $id_clasificacion;
	private  $id_estatus;

  function __construct($id_producto,$nombre,$gramaje,$id_categoria,$id_marca,$id_submarca,$id_clasificacion,$id_estatus)
  {
  	$this->id_producto = $id_producto;
    $this->nombre = $nombre;
    $this->gramaje = $gramaje;
    $this->id_categoria = $id_categoria;
  	$this->id_marca = $id_marca;
    $this->id_submarca = $id_submarca;
    $this->id_clasificacion = $id_clasificacion;
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