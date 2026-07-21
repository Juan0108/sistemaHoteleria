<?php

/**
 * 
 */
class ControladorCalendarios{

static public function crtObtenerCalendarios($idNegocio, $idEvento) {
        // Obtener calendarios desde el modelo
        $respuesta = ModeloCalendarios::MdlObtenerCalendarios($idNegocio, $idEvento);
        return $respuesta;

}

    static public function crtActualizarComentario() {

        if(isset($_POST["editarComentario"])) {

            // Validar el comentario (puedes ajustarlo según tus necesidades)
            if(preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["editarComentario"])){

                $id_evento = $_POST["idEvento"];
                $editarComentario = $_POST["editarComentario"];

                // Crear el objeto evento con los datos del formulario
                $evento = new calendario($id_evento,
                                        0,
                                        0,
                                        0, 
                                        $editarComentario,
                                        0,
                                        0,
                                        0,
                                        0,
                                        0);


                // Llamar al modelo para actualizar el comentario
                $respuesta = ModeloCalendarios::MdlUpdateCalendario($evento);

                if($respuesta == "ok") {
                    echo'<script>
                        Swal.fire({
                            icon: "success",
                            title : "Sistema PosDit",
                            text: "El comentario se ha actualizado correctamente",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                        }).then(function(result){
                            if (result.value) {
                                window.location = "calendarios";
                            }
                        })
                    </script>';
                }
            } else {
                echo'<script>
                    Swal.fire({
                        icon: "error",
                        title : "Sistema PosDit",
                        text: "¡El comentario contiene caracteres no permitidos!",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then(function(result){
                        if (result.value) {
                            window.location = "calendarios";
                        }
                    })
                </script>';
            }
        }
    }


    static public function ctrInsertarEvento(){

        if (isset($_POST["nombreEvento"])) {
            if (preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ ]+$/', $_POST["nombreEvento"])) {
        
                $fechaDesdeModelo = ModeloCalendarios::MdlObtenerFechaHoy();
                if (is_array($fechaDesdeModelo) && isset($fechaDesdeModelo["FechaHoy"])) {
                    $FechaHoy = new DateTime($fechaDesdeModelo["FechaHoy"]); // Convertir a DateTime
                } else {
                    throw new Exception("Error: No se pudo obtener la fecha actual.");
                }

                $fechaFin = clone $FechaHoy;

                $ocurrencia =  (int)$_POST["Ocurrencia"];

                if ($ocurrencia == 0 ){
                    $fechaFin->modify("+1 week");
                }else{
                    $fechaFin->modify("+{$_POST["Ocurrencia"]} months");
                }

                $dias = $_POST["diasEvento"];

                foreach ($dias as $diaBuscado) {

                    $horaEvento = $_POST["horaNotificacion"];
                    $hrsAntes = (int)$_POST["hrsAntes"];

                    $contadorDias = 0;
                    $fechasEncontradas = [];
        
                    // Recorrer todas las fechas en el rango
                    $intervalo = new DateInterval('P1D'); // Intervalo de 1 día
                    $periodo = new DatePeriod($FechaHoy, $intervalo, $fechaFin);
        
                    foreach ($periodo as $fecha) {
                        if ($fecha->format('N') == $diaBuscado) {
                            $contadorDias++;
                            $fechasEncontradas[] = $fecha->format('Y-m-d');
                        }
                    }

                    foreach ($fechasEncontradas as $fecha) {
                        $fechaEvento = new DateTime("$fecha $horaEvento:00");

                        $fechaNotif = new DateTime("$fecha $horaEvento:00");
                        $fechaNotif->sub(new DateInterval("PT{$hrsAntes}H"));

                        // Obtener la fecha y hora resultante en el formato deseado
                        $fechaNotificar = $fechaNotif->format('Y-m-d H:i:s');
                        $fechaEvento = $fechaEvento->format('Y-m-d H:i:s');
            
                        // Crear el objeto evento con los datos del formulario
                        $evento = new calendario(
                            0, // id_evento
                            $_POST["nombreEvento"], // nombreevento
                            $fechaEvento, // diaevento
                            $fechaNotificar, // fechanotificacion
                            $_POST["comentario"], // comentario
                            0, // idAsistir
                            $_POST["idusuario"], // idUsuario
                            $_POST["idnegocio"], // idNegocio
                            6 // idEstatus
                        );

                        $respuesta = ModeloCalendarios::MdlInsertarEvento($evento);

                        if (is_array($respuesta) && isset($respuesta['validar']) && $respuesta['validar'] === 0) {

                            echo'<script>

                            Swal.fire({
                                icon: "success",
                                title : "Sistema PosDit",
                                text: "El Evento fue creado correctamente",
                                showConfirmButton: true,
                                confirmButtonText: "Cerrar"
                                }).then(function(result){
                                            if (result.value) {

                                            window.location = "calendarios";

                                            }
                                        })

                            </script>';

                        }else{
                        echo'<script>

                            Swal.fire({
                                icon: "error",
                                title : "Sistema PosDit",
                                text: "¡El evento ya existe, favor de validar!",
                                showConfirmButton: true,
                                confirmButtonText: "Cerrar"
                                }).then(function(result){
                                            if (result.value) {

                                            window.location = "calendarios";

                                            }
                                        })

                            </script>';
                        }
                        
                    }
   
                }
 

            }else{

                echo'<script>
    
                    Swal.fire({
                          icon: "error",
                          title : "Sistema PosDit",
                          text: "¡El Evento no puede ir vací0 o llevar caracteres especiales!",
                          showConfirmButton: true,
                          confirmButtonText: "Cerrar"
                          }).then(function(result){
                            if (result.value) {
    
                            window.location = "calendarios";
    
                            }
                        })
    
                  </script>';
    
            }
        }        
		
    }
    
}