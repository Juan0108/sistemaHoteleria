<?php

require_once "../controllers/ventas.controlador.php";
require_once "../models/ventas.modelo.php";

class AjaxCrearVenta 
{
/**
 *Editar Marca
 */
	public $IdVenta;
	public $Cantidad;
	public $NTicket;
	public $Idusuario;
	public $PrecioFinal;
	public $Cliente;
	public $Reservacion;

	public function AjaxInsertarVenta(){

		$vIdVenta = $this->IdVenta;
		$vCantidad = $this->Cantidad;
		$vNTicket = $this->NTicket;
		$vIdUsuario = $this->Idusuario;
		$vPrecioFinal = $this->PrecioFinal;
		$vCliente= $this->Cliente;
		$vReservacion = $this->Reservacion;
		$respuesta = ModeloVentas::MdlInsertarVenta($vIdVenta,$vCantidad,$vNTicket,$vIdUsuario,$vPrecioFinal,$vCliente,$vReservacion);
		echo json_encode($respuesta);
	}

}

if(isset($_POST["id"])){

	$Editar = new AjaxCrearVenta();
	$Editar -> IdVenta = $_POST["id"];
	$Editar -> Cantidad = $_POST["cantidad"];
	$Editar -> NTicket = $_POST["nTicket"];
	$Editar -> Idusuario = $_POST["idUsuario"];
	$Editar -> PrecioFinal = $_POST["precioFinal"];
	$Editar -> Cliente = $_POST["cliente"];
	$Editar -> Reservacion = $_POST["reservacion"] ?? 0;
	$Editar -> AjaxInsertarVenta();

}