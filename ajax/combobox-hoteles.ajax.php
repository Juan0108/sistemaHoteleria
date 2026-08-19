<?php


require_once "../controllers/hoteles.controlador.php";
require_once "../models/hoteles.modelo.php";

class ComboHoteles
{

    public function mostrarComboHoteles(){

        // Definimos el encabezado JSON antes de imprimir cualquier dato
        header('Content-Type: application/json; charset=utf-8');

        // Llamamos al controlador (el modelo interno se encarga del echo json_encode)
        ControladorHoteles::crtJsonObtenerHoteles(); 
        
    }
}

$Mostar = new ComboHoteles();
$Mostar->mostrarComboHoteles();