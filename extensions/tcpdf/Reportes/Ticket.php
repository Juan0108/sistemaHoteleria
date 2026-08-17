<?php

require_once "../../../controllers/ventas.controlador.php";
require_once "../../../models/ventas.modelo.php";
require_once "../../../controllers/negocios.controlador.php";
require_once "../../../models/negocios.modelo.php";
require_once "../../../controllers/usuarios.controlador.php";
require_once "../../../models/usuarios.modelo.php";
require_once "../../../twilio-php-main/src/Twilio/autoload.php"; // Ajusta esta ruta
use Twilio\Rest\Client;

class imprimirTicket {

    public $CodigoUsuario;
    public $Ticket;
    public $Pago;
    public $Telefono;

    public function traerTicket() {
        $idusuario = $this->CodigoUsuario;
        $idTicket = $this->Ticket;
        $Efectivo = $this->Pago;
        $Prefijo = 52;
        $Celular = $Prefijo . $this->Telefono;

        $Negocio = ControladorHoteles::crtObtenerNegocioUsuarioReporte($idusuario);
        $ventas = ControladorVentas::crtObtenerTicket($idTicket, $idusuario);
        $Usuario = ModeloUsuarios::MdlObtenerUsuarioNombre($idusuario);

        $Tienda = $Negocio[0]["Razon_Social"];
        $Estado = $Negocio[0]["Estado"];
        $Municipio = $Negocio[0]["Municipio"];
        $Colonia = $Negocio[0]["Colonia"];
        $Calle = $Negocio[0]["Calle"];
        $Telefono2 = $Negocio[0]["Telefono"];
        $Correo = $Negocio[0]["Correo"];
        $Fecha = date('YmdHi');

        // Include the main TCPDF library (search for installation path).
        require_once('tcpdf_include.php');

        // create new PDF document
        $widthOriginal = 140 + (count($ventas) * 3);
        $width = 100;  
        $height = $widthOriginal;

        $pageLayout = array($width, $height);
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, $pageLayout, true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('David Aguilar');
        $pdf->SetTitle('Ticket Compra');
        $pdf->setPrintHeader(false); 
        $pdf->SetMargins(20, 10, 20, false); 
        $pdf->SetAutoPageBreak(TRUE, 0);
        $pdf->SetFont('Helvetica', '', 10);
        $pdf->SetPrintFooter(false);

        // add a page
        $pdf->AddPage();

        // set some text to print
        $Cabecera = <<<EOF
<div style="text-align: center;">
<img src="images/DIT slogan.png" width="80px" height="40px">
</div>

<br style="color:red" align="center"> $Tienda </br>
<br style="font-size:8px" align="center"> $Calle </br>
<br style="font-size:8px" align="center"> $Colonia </br>
<br style="font-size:8px" align="center"> $Municipio </br>
<br style="font-size:8px" align="center"> $Estado </br>
<br style="font-size:8px" align="center"> $Telefono2 </br>
<br style="font-size:8px" align="center"> $Correo </br>
<div style="font-size:6px; text-align:center; "></div>
EOF;

        // print a block of text using Write()
        $pdf->writeHTML($Cabecera, false, false, false, false, '');

        // ---------------------------------------------------------

        $Contenido = <<<EOF
<div style="font-size:6px; text-align:center; "></div>

<table border="1" style="font-size: 8.5px; color: black; font-weight: bold;">
    <thead>
        <tr>  
           <th align="center" style="width: 20px;">N°</th>
           <th align="center" style="width: 65px;">Articulos</th>
           <th align="center">Precio</th>
           <th align="center">Total</th>
        </tr>
    </thead>
</table> 
EOF;

        // print a block of text using Write()
        $pdf->writeHTML($Contenido, false, false, false, false, '');

        $superCompra = 0;
        $superEfectivo = $Efectivo;
        $superCambio = 0;

        foreach ($ventas as $key => $value) {
            $superCompra = $superCompra + $value['Total'];

            $Complemento = <<<EOF
<table>
    <tbody>
        <tr>
            <td style="width: 20px;">$value[Cantidad]</td>  
            <td style="width: 65px;">$value[Producto]</td>
            <td align="right">$ $value[PrecioVenta]</td>
            <td align="right">$ $value[Total]</td>
        </tr>
    </tbody>
</table> 
EOF;

            // print a block of text using Write()
            $pdf->writeHTML($Complemento, false, false, false, false, '');
        }

        $superCambio = $superEfectivo - $superCompra;

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
                    <td align="right">$ $superCompra</td>
                </tr>
                <tr>
                    <td>Efectivo:</td>
                    <td align="right">$ $superEfectivo</td>
                </tr>
                <tr>
                    <td>Cambio:</td>
                    <td align="right">$ $superCambio</td>
                </tr>
            </tbody>
            </table>
        </td>
    </tr>
</tbody>
</table>
EOF;

        // print a block of text using Write()
        $pdf->writeHTML($Totales, false, false, false, false, '');

        $Final = <<<EOF
<div></div>
<br align="left">Le atendió: $Usuario[Nombre] </br>
<div align="left">Fecha de Compra: $Fecha </div>
<br align="center"> ¡Gracias Por Su Compra! </br>
EOF;

        // print a block of text using Write()
        $pdf->writeHTML($Final, false, false, false, false, '');

        //Close and output PDF document
        $nombreArchivo = $idTicket . '_' . $idusuario .'.pdf';
        $pdfFilePath = dirname(__DIR__, 3) . '/tickets/' . $nombreArchivo; // Carpeta "tickets" en la raíz del proyecto, sin importar el servidor
        $pdf->Output($pdfFilePath, 'F');

        $pdfUrl = "https://posdit.com.mx/sistema.posdit.com.mx/tickets/" . $nombreArchivo;  // URL del PDF en tu servidor

        // Datos para la API
        $apiUrl = 'https://apiwsp.factiliza.com/api/v1/message/sendMedia/NTI1NTI1MzI3MzA0';
        $token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiI2MjciLCJuYW1lIjoiSnVhbiBEYXZpZCBBZ3VpbGFyIEJhcnJvbiAiLCJlbWFpbCI6ImFndWlsYXJiYXJyb25qdWFuZGF2aWRAZ21haWwuY29tIiwiaHR0cDovL3NjaGVtYXMubWljcm9zb2Z0LmNvbS93cy8yMDA4LzA2L2lkZW50aXR5L2NsYWltcy9yb2xlIjoiY29uc3VsdG9yIn0.r5cvSNgCntPbf4OCjqx1JlS885CxHSN7FyxCLlVBAus';
        $data = array(
            "number" => $Celular,
            "mediatype" => "document",
            "media" => $pdfUrl,
            "filename" => "Ticket de compra.pdf",
            "caption" => "¡Gracias por su compra!"
        );

        $result = $this->enviarMensajeAPI($apiUrl, $token, $data);

        echo "Resultado de la API: $result";
    }

    private function enviarMensajeAPI($url, $token, $data) {
        $ch = curl_init($url);

        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
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
            return 'Error en cURL: ' . curl_error($ch);
        } else {
            return $response;
        }

        curl_close($ch);
    }
}

$ReporteGanancias = new imprimirTicket();
$ReporteGanancias->CodigoUsuario = $_GET["Cu"];
$ReporteGanancias->Ticket = $_GET["NTicket"];
$ReporteGanancias->Pago = $_GET["Pago"];
$ReporteGanancias->Telefono = $_GET["Telefono"];
$ReporteGanancias->traerTicket();

//============================================================+
// END OF FILE
//============================================================+
