<?php


class inventario{
  
  private  $id_usuario;
	private  $id_inventario;
	private  $id_producto;
  private  $stock;
  private  $preciocompra;
  private  $precioventa;
  private  $id_negocio;

  function __construct($id_usuario,$id_inventario,$id_producto,$stock,$preciocompra,$precioventa,$id_negocio)
  {
    $this->id_usuario = $id_usuario;
  	$this->id_inventario = $id_inventario;
    $this->id_producto = $id_producto;
    $this->stock = $stock;
    $this->preciocompra = $preciocompra;
  	$this->precioventa = $precioventa;
    $this->id_negocio = $id_negocio;
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