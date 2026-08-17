<?php


require_once "../controllers/negocios.controlador.php";
require_once "../models/negocios.modelo.php";

class TablaHoteles
{
    public function mostrarTablaHoteles() {


    $Hoteles = ControladorHoteles::crtObtenerHoteles();	   
    $filas = array();

    if (!empty($Hoteles) && is_array($Hoteles)) {

        foreach ($Hoteles as $hotel) {

            $Boton = "<div class='checkbox'>"
                    ."<button class='btn btn-info btnEditarHotel' data-id-hotel='".$hotel["Id_Hotel"]."' data-toggle='modal' data-target='#modalEditarHotel'><i class='fa fa-pencil'></i></button> "
                    ."<button class='btn btn-danger btnEliminarHotel' data-id-hotel='".$hotel["Id_Hotel"]."'><i class='fa fa-times'></i></button>"
                    ."</div>";

            if ($hotel["Estatus"] == "checked") {
                $imagen = "<a class='buttonActivo'>Activo</a>";
            } elseif ($hotel["Estatus"] == "prueba") {
                $imagen = "<a class='buttonPeriodo'>Periodo Prueba</a>";
            } else {
                $imagen = "<a class='button'>In-Activo</a>";
            }	

            $filas[] = [
                    $hotel["Id_Hotel"] ?? '',
                    $hotel["Razon_Social"] ?? '',
                    $hotel["Responsable"] ?? '',
                    $hotel["Telefono"] ?? '',
                    $hotel["Estado"] ?? '',
                    $hotel["Municipio"] ?? '',
                    $hotel["Colonia"] ?? '',
                    $hotel["Calle"] ?? '',
                    $hotel["Correo"] ?? '',
                    $hotel["Giro"] ?? '',
                    $hotel["TipoPago"] ?? '',
                    $hotel["Fecha_Alta"] ?? '',
                    $hotel["Fecha_Baja"] ?? '',
                    $hotel["SAire"] ?? '',
                    $hotel["SServicios"] ?? '',
                    $imagen,
                    $Boton
                ];
            }
        }

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(["data" => $filas], JSON_UNESCAPED_UNICODE);
    }
}

$Mostar = new TablaHoteles();
$Mostar->mostrarTablaHoteles();