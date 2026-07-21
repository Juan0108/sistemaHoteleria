<?php

require_once "../controllers/calendarios.controlador.php";
require_once "../models/calendarios.modelo.php";

class TablaCalendarios
{

    public function mostrarTablaCalendarios()
    {
        // Obtener los parámetros enviados vía POST o asignar valores predeterminados
        $idNegocio = $_POST['idNegocio'];
        $idEvento = isset($_POST['idEvento']) ? intval($_POST['idEvento']) : null;

        // Llamar al controlador para obtener los calendarios
        $calendarios = ControladorCalendarios::crtObtenerCalendarios($idNegocio, $idEvento);

        // Crear el JSON con los datos
        $datosJson = '{
            "data": [';

        for ($i = 0; $i < count($calendarios); $i++) {
            // Botón de acción
            $Boton = "<div class='checkbox'><button class='btn btn-info btnEditarCalendario' Id_Evento='" . $calendarios[$i]["Id_Evento"] . "' data-toggle='modal' data-target='#modalEditarCalendario'><i class='fa fa-pencil'></i> </button></div>";

            // Determinar el estado con base en "Id_Asistir"
            $imagen = ($calendarios[$i]["Id_Asistir"] == 1)
                ? "<a class='buttonActivo'>Si</a>"
                : "<a class='btn btn-info'>No</a>";

            // Concatenar los datos en el JSON
            $datosJson .= '[
                "' . $calendarios[$i]["Id_Evento"] . '",
                "' . $calendarios[$i]["Nombre"] . '",
                "' . $calendarios[$i]["DiaEvento"] . '",
                "' . $calendarios[$i]["FechaNotificacion"] . '",
                "' . $calendarios[$i]["Comentario"] . '",
                "' . $imagen . '",
                "' . $Boton . '"
            ],';
        }

        // Remover la última coma
        $datosJson = substr($datosJson, 0, -1);

        $datosJson .= ']
        }';

        // Devolver el JSON
        echo $datosJson;
    }
}

// Instanciar la clase y ejecutar el método
$Mostar = new TablaCalendarios();
$Mostar-> mostrarTablaCalendarios();
