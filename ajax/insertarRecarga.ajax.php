<?php

require_once "../controllers/ventas.controlador.php";
require_once "../models/ventas.modelo.php";

class AjaxInsertarRecarga
{
    public $NTicket;
    public $Id_Producto;
    public $Cantidad;
    public $PrecioCompra;
    public $PrecioVenta;
    public $Id_Usuario;
    public $Fecha_Compra;
    public $id_Nombre;
    public $id_Codigo;
    public $Descripcion;
    public $Numero;
    public $Folio;

    public function AjaxInsertarRecarga()
    {
        $respuesta = ModeloVentas::MdlInsertarVentaYServicio(
            $this->NTicket,
            $this->Id_Producto,
            $this->Cantidad,
            $this->PrecioCompra,
            $this->PrecioVenta,
            $this->Id_Usuario,
            $this->Fecha_Compra,
            $this->id_Nombre,
            $this->id_Codigo,
            $this->Descripcion,
            $this->Numero,
            $this->Folio
        );
        echo json_encode($respuesta);
    }
}

if (isset($_POST["NTicket"])) {
    $insertarRecarga = new AjaxInsertarRecarga();
    $insertarRecarga->NTicket = $_POST["NTicket"];
    $insertarRecarga->Id_Producto = $_POST["Id_Producto"];
    $insertarRecarga->Cantidad = $_POST["Cantidad"];
    $insertarRecarga->PrecioCompra = $_POST["PrecioCompra"];
    $insertarRecarga->PrecioVenta = $_POST["PrecioVenta"];
    $insertarRecarga->Id_Usuario = $_POST["Id_Usuario"];
    $insertarRecarga->Fecha_Compra = $_POST["Fecha_Compra"];
    $insertarRecarga->id_Nombre = $_POST["id_Nombre"];
    $insertarRecarga->id_Codigo = $_POST["id_Codigo"];
    $insertarRecarga->Descripcion = $_POST["Descripcion"];
    $insertarRecarga->Numero = $_POST["Numero"];
    $insertarRecarga->Folio = $_POST["Folio"];
    $insertarRecarga->AjaxInsertarRecarga();
}
