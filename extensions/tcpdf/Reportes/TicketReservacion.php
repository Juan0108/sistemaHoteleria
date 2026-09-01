<?php

session_start();

require_once "../../../controllers/habitaciones.controlador.php";
require_once "../../../models/habitaciones.modelo.php";
require_once "../../../controllers/reservaciones.controlador.php";
require_once "../../../models/reservaciones.modelo.php";
require_once "../../../controllers/hoteles.controlador.php";
require_once "../../../models/hoteles.modelo.php";
require_once "../../../models/usuarios.modelo.php";
require_once "../../../models/clientes.modelo.php";

// Ticket de checkout (folio completo de la estadía: hospedaje + consumo), a diferencia de
// Ticket.php que arma el ticket de UN solo NTicket de Punto de Venta. Aquí se agrupa por
// Id_Reservacion, así que junta todo lo que se le cargó al huésped durante toda su estadía
// (el hospedaje ya queda registrado como un renglón más de Tb_Consumo al hacer el checkout).
// El envío es automático (sin preguntar ni pedir el teléfono a mano): se manda SIEMPRE justo
// después de terminar el check-out, usando el teléfono ya guardado del cliente en
// cat_clientes.
class imprimirTicketReservacion {

    public $IdReservacion;

    public function enviar() {

        header('Content-Type: application/json; charset=utf-8');

        $id_hotel = ControladorHabitaciones::crtObtenerIdHotelSesion();

        if ($id_hotel === null) {
            echo json_encode(["ok" => false, "mensaje" => "Sin hotel en sesión"]);
            return;
        }

        $idReservacion = trim((string) $this->IdReservacion);
        $idUsuario = (int) ($_SESSION["IdUsuario"] ?? 0);

        // MdlObtenerReservacionParaCheckout ya limita por Id_Hotel de la sesión, así que un
        // usuario de un negocio nunca puede pedir el ticket de una reservación de otro.
        $reservacion = ModeloReservaciones::MdlObtenerReservacionParaCheckout($idReservacion, $id_hotel);

        if (!$reservacion) {
            echo json_encode(["ok" => false, "mensaje" => "Reservación no encontrada"]);
            return;
        }

        $telefono = !empty($reservacion['Id_Cliente'])
            ? trim((string) (ModeloClientes::MdlObtenerCliente((int) $reservacion['Id_Cliente'])['Telefono'] ?? ''))
            : '';

        if ($telefono === '') {
            echo json_encode(["ok" => false, "mensaje" => "El cliente de esta reservación no tiene un teléfono guardado."]);
            return;
        }

        $Prefijo = 52;
        $Celular = $Prefijo . str_replace([' ', '(', ')', '-'], '', $telefono);

        $consumo = ModeloReservaciones::MdlObtenerConsumoReservacion($idReservacion);
        $Negocio = ControladorHoteles::crtObtenerNegocioUsuarioReporte($idUsuario);
        $Usuario = ModeloUsuarios::MdlObtenerUsuarioNombre($idUsuario);

        $Tienda = $Negocio[0]["Razon_Social"] ?? "";
        $Estado = $Negocio[0]["Estado"] ?? "";
        $Municipio = $Negocio[0]["Municipio"] ?? "";
        $Colonia = $Negocio[0]["Colonia"] ?? "";
        $Calle = $Negocio[0]["Calle"] ?? "";
        $Telefono2 = $Negocio[0]["Telefono"] ?? "";
        $Correo = $Negocio[0]["Correo"] ?? "";
        // Fecha y hora por separado (no en un solo string): así se pueden acomodar en columnas
        // de tabla (fecha a la izquierda, hora a la derecha) sin depender de que el HTML de
        // TCPDF respete white-space/nowrap, que es poco confiable en su parser.
        $FechaCheckoutFecha = date('d/m/Y');
        $FechaCheckoutHora = date('H:i');
        $FechaCheckinFecha = !empty($reservacion['FechaEntrada']) ? date('d/m/Y', strtotime($reservacion['FechaEntrada'])) : '';
        $FechaCheckinHora = !empty($reservacion['FechaEntrada']) ? date('H:i', strtotime($reservacion['FechaEntrada'])) : '';

        require_once('tcpdf_include.php');

        $widthOriginal = 140 + (count($consumo) * 3);
        $width = 100;
        $height = $widthOriginal;

        $pageLayout = array($width, $height);
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, $pageLayout, true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Sistema PosDit');
        $pdf->SetTitle('Ticket de estadía');
        $pdf->setPrintHeader(false);
        $pdf->SetMargins(20, 10, 20, false);
        $pdf->SetAutoPageBreak(TRUE, 0);
        $pdf->SetFont('Helvetica', '', 10);
        $pdf->SetPrintFooter(false);

        $pdf->AddPage();

        $Cabecera = <<<EOF
<br style="color:red" align="center"> $Tienda </br>
<br style="font-size:8px" align="center"> $Calle </br>
<br style="font-size:8px" align="center"> $Colonia </br>
<br style="font-size:8px" align="center"> $Municipio </br>
<br style="font-size:8px" align="center"> $Estado </br>
<br style="font-size:8px" align="center"> $Telefono2 </br>
<br style="font-size:8px" align="center"> $Correo </br>
<div style="font-size:6px; text-align:center; "></div>
<br style="font-size:9px" align="center"> Folio: $idReservacion </br>
EOF;

        $pdf->writeHTML($Cabecera, false, false, false, false, '');

        $Contenido = <<<EOF
<div style="font-size:6px; text-align:center; "></div>

<table border="1" style="width: 180px; font-size: 8.5px; color: black; font-weight: bold;">
    <tbody>
        <tr>
           <td align="center" style="width: 18px;">N°</td>
           <td align="center" style="width: 52px;">Concepto</td>
           <td align="center" style="width: 55px;">Precio</td>
           <td align="center" style="width: 55px;">Total</td>
        </tr>
    </tbody>
</table>
EOF;

        $pdf->writeHTML($Contenido, false, false, false, false, '');

        $totalEstadia = 0;

        foreach ($consumo as $item) {
            $totalEstadia += (float) $item['Total'];

            // number_format con separador de miles y 2 decimales fijos, para que las cifras
            // (Precio y Total) queden alineadas entre renglones sin importar cuántos dígitos
            // tenga cada una.
            $precioFormateado = number_format((float) $item['PrecioVenta'], 2);
            $totalFormateado = number_format((float) $item['Total'], 2);

            // $&nbsp;$cifra (espacio duro) en vez de "$ $cifra": con espacio normal, si la
            // celda no alcanza a caber la cifra completa, TCPDF corta la línea justo ahí y el
            // "$" queda solo arriba y el monto abajo — el &nbsp; evita ese salto.
            $Complemento = <<<EOF
<table>
    <tbody>
        <tr>
            <td style="width: 18px; font-size: 7px;">$item[Cantidad]</td>
            <td style="width: 52px; font-size: 7px;">$item[Producto]</td>
            <td align="right" style="width: 55px; font-size: 7px; white-space: nowrap;">$&nbsp;$precioFormateado</td>
            <td align="right" style="width: 55px; font-size: 7px; white-space: nowrap;">$&nbsp;$totalFormateado</td>
        </tr>
    </tbody>
</table>
EOF;

            $pdf->writeHTML($Complemento, false, false, false, false, '');
        }

        $totalFormateadoGeneral = number_format($totalEstadia, 2);

        // Misma tabla de 180px (18+52+55+55, igual que la de los conceptos) para que la cifra
        // del Total quede exactamente debajo de la columna "Total" de arriba, en vez de en una
        // tabla aparte de otro ancho que no se alinea con las demás cifras.
        $Totales = <<<EOF
<div style="font-size:6px; text-align:center; "></div>
<table style="border: none; width: 180px;">
<tbody>
    <tr>
        <td align="right" style="border: none; width: 125px; font-size: 7px;">Total:</td>
        <td align="right" style="border: none; width: 55px; font-size: 7px; white-space: nowrap;">$&nbsp;$totalFormateadoGeneral</td>
    </tr>
</tbody>
</table>
EOF;

        $pdf->writeHTML($Totales, false, false, false, false, '');

        $nombreUsuario = $Usuario["Nombre"] ?? "";

        // Tabla de 2 columnas (fecha a la izquierda, hora a la derecha) en vez de un solo
        // texto: writeHTML de TCPDF no respeta bien white-space/nowrap, así que la única forma
        // confiable de que la hora nunca se vaya a la línea de abajo es ponerla en su propia
        // celda. El renglón de Check-in queda arriba del de Checkout, en el mismo bloque.
        $Final = <<<EOF
<div></div>
<br align="left">Le atendió: $nombreUsuario </br>
<div style="font-size:6px;"></div>
<table style="border: none; width: 200px;">
<tbody>
    <tr>
        <td align="left" style="border: none; width: 150px; font-size: 7px; white-space: nowrap;">Fecha de check-in: $FechaCheckinFecha</td>
        <td align="right" style="border: none; width: 50px; font-size: 7px;">$FechaCheckinHora</td>
    </tr>
    <tr>
        <td align="left" style="border: none; width: 150px; font-size: 7px; white-space: nowrap;">Fecha de checkout: $FechaCheckoutFecha</td>
        <td align="right" style="border: none; width: 50px; font-size: 7px;">$FechaCheckoutHora</td>
    </tr>
</tbody>
</table>
<div style="font-size:6px;"></div>
<br align="center"> ¡Gracias por su estadía! </br>
EOF;

        $pdf->writeHTML($Final, false, false, false, false, '');

        $nombreArchivo = 'reserva_' . $idReservacion . '.pdf';
        $pdfFilePath = dirname(__DIR__, 3) . '/tickets/' . $nombreArchivo;
        $pdf->Output($pdfFilePath, 'F');

        // Se manda el PDF embebido en base64 (en vez de una URL pública): así funciona
        // igual en producción y en local, ya que la API de WhatsApp no necesita alcanzar
        // ningún archivo por su cuenta — el contenido va directo en la petición.
        $mediaBase64 = base64_encode(file_get_contents($pdfFilePath));

        $apiUrl = 'https://apiwsp.factiliza.com/api/v1/message/sendMedia/NTI1NTI1MzI3MzA0';
        $token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiI2MjciLCJuYW1lIjoiSnVhbiBEYXZpZCBBZ3VpbGFyIEJhcnJvbiAiLCJlbWFpbCI6ImFndWlsYXJiYXJyb25qdWFuZGF2aWRAZ21haWwuY29tIiwiaHR0cDovL3NjaGVtYXMubWljcm9zb2Z0LmNvbS93cy8yMDA4LzA2L2lkZW50aXR5L2NsYWltcy9yb2xlIjoiY29uc3VsdG9yIn0.r5cvSNgCntPbf4OCjqx1JlS885CxHSN7FyxCLlVBAus';
        $data = array(
            "number" => $Celular,
            "mediatype" => "document",
            "media" => $mediaBase64,
            "filename" => "Ticket de estadia.pdf",
            "caption" => "¡Gracias por su estadía en " . $Tienda . "!"
        );

        $resultado = $this->enviarMensajeAPI($apiUrl, $token, $data);

        echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
    }

    // Antes esta función solo regresaba el texto crudo de la respuesta y todo se trataba
    // como "éxito" mientras el navegador no viera un error de red — así que un rechazo de
    // la API (token vencido, número mal formateado, instancia de WhatsApp desconectada...)
    // se mostraba igual como "Ticket enviado". Ahora se revisa el código HTTP y el cuerpo
    // de la respuesta para regresar un "ok" real, y siempre se manda la respuesta cruda
    // para poder diagnosticar si algo falla.
    private function enviarMensajeAPI($url, $token, $data) {
        $ch = curl_init($url);

        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token
            ),
        ));

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            return ["ok" => false, "mensaje" => "Error de conexión con la API de WhatsApp: " . $error, "raw" => null];
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode < 200 || $httpCode >= 300) {
            return ["ok" => false, "mensaje" => "La API de WhatsApp respondió con error (HTTP $httpCode)", "raw" => $response];
        }

        $decodificado = json_decode($response, true);

        if (is_array($decodificado)) {
            $reportaError = (isset($decodificado["error"]) && $decodificado["error"])
                || (isset($decodificado["success"]) && $decodificado["success"] === false)
                || (isset($decodificado["status"]) && in_array($decodificado["status"], [false, "error", "ERROR"], true));

            if ($reportaError) {
                $mensajeApi = $decodificado["message"] ?? $decodificado["mensaje"] ?? $decodificado["error"] ?? "La API de WhatsApp reportó un error";
                return ["ok" => false, "mensaje" => is_string($mensajeApi) ? $mensajeApi : "La API de WhatsApp reportó un error", "raw" => $response];
            }
        }

        return ["ok" => true, "mensaje" => "Ticket enviado", "raw" => $response];
    }
}

$Ticket = new imprimirTicketReservacion();
$Ticket->IdReservacion = $_GET["IdReservacion"] ?? "";
$Ticket->enviar();

//============================================================+
// END OF FILE
//============================================================+
