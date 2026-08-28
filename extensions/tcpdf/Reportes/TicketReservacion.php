<?php

session_start();

require_once "../../../controllers/habitaciones.controlador.php";
require_once "../../../models/habitaciones.modelo.php";
require_once "../../../controllers/reservaciones.controlador.php";
require_once "../../../models/reservaciones.modelo.php";
require_once "../../../controllers/hoteles.controlador.php";
require_once "../../../models/hoteles.modelo.php";
require_once "../../../models/usuarios.modelo.php";

// Ticket de checkout (folio completo de la estadía: hospedaje + consumo), a diferencia de
// Ticket.php que arma el ticket de UN solo NTicket de Punto de Venta. Aquí se agrupa por
// Id_Reservacion, así que junta todo lo que se le cargó al huésped durante toda su estadía
// (el hospedaje ya queda registrado como un renglón más de Tb_Consumo al hacer el checkout).
class imprimirTicketReservacion {

    public $IdReservacion;
    public $Telefono;

    public function enviar() {

        $id_hotel = ControladorHabitaciones::crtObtenerIdHotelSesion();

        if ($id_hotel === null) {
            echo "Sin hotel en sesión";
            return;
        }

        $idReservacion = trim((string) $this->IdReservacion);
        $idUsuario = (int) ($_SESSION["IdUsuario"] ?? 0);
        $Prefijo = 52;
        $Celular = $Prefijo . str_replace([' ', '(', ')', '-'], '', (string) $this->Telefono);

        // MdlObtenerReservacionParaCheckout ya limita por Id_Hotel de la sesión, así que un
        // usuario de un negocio nunca puede pedir el ticket de una reservación de otro.
        $reservacion = ModeloReservaciones::MdlObtenerReservacionParaCheckout($idReservacion, $id_hotel);

        if (!$reservacion) {
            echo "Reservación no encontrada";
            return;
        }

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
        $Fecha = date('d/m/Y H:i');

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

<table border="1" style="font-size: 8.5px; color: black; font-weight: bold;">
    <thead>
        <tr>
           <th align="center" style="width: 20px;">N°</th>
           <th align="center" style="width: 65px;">Concepto</th>
           <th align="center">Precio</th>
           <th align="center">Total</th>
        </tr>
    </thead>
</table>
EOF;

        $pdf->writeHTML($Contenido, false, false, false, false, '');

        $totalEstadia = 0;

        foreach ($consumo as $item) {
            $totalEstadia += (float) $item['Total'];

            $Complemento = <<<EOF
<table>
    <tbody>
        <tr>
            <td style="width: 20px;">$item[Cantidad]</td>
            <td style="width: 65px;">$item[Producto]</td>
            <td align="right">$ $item[PrecioVenta]</td>
            <td align="right">$ $item[Total]</td>
        </tr>
    </tbody>
</table>
EOF;

            $pdf->writeHTML($Complemento, false, false, false, false, '');
        }

        $Totales = <<<EOF
<div style="font-size:6px; text-align:center; "></div>
<table style="border: none;">
<tbody>
    <tr>
        <td style="width: 50%; border: none;"></td>
        <td style="border: none;">
            <table style="width: 90px;">
            <tbody>
                <tr>
                    <td>Total:</td>
                    <td align="right">$ $totalEstadia</td>
                </tr>
            </tbody>
            </table>
        </td>
    </tr>
</tbody>
</table>
EOF;

        $pdf->writeHTML($Totales, false, false, false, false, '');

        $nombreUsuario = $Usuario["Nombre"] ?? "";

        $Final = <<<EOF
<div></div>
<br align="left">Le atendió: $nombreUsuario </br>
<div align="left">Fecha de checkout: $Fecha </div>
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

        header('Content-Type: application/json; charset=utf-8');
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
$Ticket->Telefono = $_GET["Telefono"] ?? "";
$Ticket->enviar();

//============================================================+
// END OF FILE
//============================================================+
