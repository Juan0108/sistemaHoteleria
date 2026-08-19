<?php

class habitacion {

	private $id_habitacion;
	private $id_hotel;
	private $numeroHabitacion;
	private $descripcion;
	private $tipoHabitacion;
	private $capacidad;
	private $precioNoche;
	private $foto;
	private $id_estatus;

	function __construct($id_habitacion, $id_hotel, $numeroHabitacion, $descripcion, $tipoHabitacion, $capacidad, $precioNoche, $foto, $id_estatus)
	{
		$this->id_habitacion = $id_habitacion;
		$this->id_hotel = $id_hotel;
		$this->numeroHabitacion = $numeroHabitacion;
		$this->descripcion = $descripcion;
		$this->tipoHabitacion = $tipoHabitacion;
		$this->capacidad = $capacidad;
		$this->precioNoche = $precioNoche;
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