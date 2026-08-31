<?php

session_start();

require_once "../../../controllers/ventas.controlador.php";
require_once "../../../models/ventas.modelo.php";
require_once "../../../controllers/hoteles.controlador.php";
require_once "../../../models/hoteles.modelo.php";
require_once '../../../vendor/autoload.php';
require_once "../../../controllers/usuarios.controlador.php";
require_once "../../../models/usuarios.modelo.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Table;
use PhpOffice\PhpSpreadsheet\Worksheet\Table\TableStyle;


class generaReporteDia
{
    public $CodigoUsuario;
    public $MontoCierre;
    public $MontoCaja;

    public function traerReporteCierreDia()
    {
        header('Content-Type: application/json; charset=utf-8');

        $idusuario = $this->CodigoUsuario;
        $ValorCierre = $this->MontoCierre;
        $ValorCaja = $this->MontoCaja;

        // // Obtener datos de negocio y reporte
         $Negocio = ControladorHoteles::crtObtenerNegocioUsuarioReporte($idusuario);
         $Reporte = ControladorVentas::crtObtenerCierreDia($idusuario);
         $Usuario = ModeloUsuarios::MdlObtenerUsuarioNombre($idusuario);

        // Crear el archivo Excel
         $spreadsheet = new Spreadsheet();
         $sheet = $spreadsheet->getActiveSheet();

        // Establecer Encabezado Información 
         $sheet->setCellValue('B2', 'Negocio:');
         $sheet->setCellValue('B3', 'Vendedor:');
         $sheet->setCellValue('B4', 'Venta Reportada:');
         $sheet->setCellValue('B5', 'Caja:');
         $sheet->setCellValue('B6', 'Venta Sistema:');
         $sheet->setCellValue('B7', 'Diferencia:');

         $totalSistema = 0;
         $diferencia = 0;

         //Carga Info de ventas
         foreach ($Reporte as $valor){
             $ValorVenta = $valor['VentaDia'];
             $totalSistema += $ValorVenta;
         }

         $diferencia = $ValorCierre - $totalSistema;

        //Información 
         $sheet->setCellValue('C2', $Negocio[0]["Razon_Social"]);
         $sheet->setCellValue('C3', $Usuario["Nombre"]);
         $sheet->setCellValue('C4', $ValorCierre);
         $sheet->setCellValue('C5', $ValorCaja);
         $sheet->setCellValue('C6', $totalSistema);
         $sheet->setCellValue('C7', $diferencia);

         // Crear una tabla con estilo azul
         $tableRang2 = "B2:C7"; // Rango completo de la tabla
         $table2 = new Table($tableRang2);

        // Optional: apply some styling to the table
         $tableStyle2 = new TableStyle();
         $tableStyle2->setTheme(TableStyle::TABLE_STYLE_DARK10);
         $tableStyle2->setShowRowStripes(true);
         $table2->setStyle($tableStyle2);

         $sheet->getStyle("C4:C7")->getNumberFormat()->setFormatCode('$#,##0.00');

         // Add the table to the sheet
         $sheet->addTable($table2);


         // Establecer títulos
         $sheet->setCellValue('B9', 'Categoria');
         $sheet->setCellValue('C9', 'Ticket');
         $sheet->setCellValue('D9', 'Producto');
         $sheet->setCellValue('E9', 'Cantidad');
         $sheet->setCellValue('F9', 'Precio Compra');
         $sheet->setCellValue('G9', 'Precio Venta');
         $sheet->setCellValue('H9', 'Venta del Día');
         $sheet->setCellValue('I9', 'Ganancia');
         $sheet->setCellValue('J9', 'Descuento');
         $sheet->setCellValue('K9', 'Fecha');
         $sheet->setCellValue('L9', 'Cliente');
       
        // // Escribir datos
         $row = 10; // Iniciar desde la segunda fila
         $totalVenta = 0;
         $totalGanancia = 0;

         foreach ($Reporte as $venta) {
             $categoria = $venta['Categoria'];
             $ticket = $venta['NumeroTicket'];
             $producto = $venta['Producto'];
             $cantidad = $venta['Cantidad'];
             $precioCompra = $venta['PrecioCompra'];
             $precioVenta = $venta['PrecioVenta'];
             $ventaDia = $cantidad * $precioVenta;
             $ganancia = $ventaDia - ($cantidad * $precioCompra);
             $descuento = $venta['Descuento'];
             $fechaVenta = $venta['Fecha_Compra'];
             $cliente = $venta['Cliente'];

             // Agregar datos a cada columna
             $sheet->setCellValue("B$row", $categoria);
             $sheet->setCellValue("C$row", $ticket);
             $sheet->setCellValue("D$row", $producto);
             $sheet->setCellValue("E$row", $cantidad);
             $sheet->setCellValue("F$row", $precioCompra);
             $sheet->setCellValue("G$row", $precioVenta);
             $sheet->setCellValue("H$row", $ventaDia);
             $sheet->setCellValue("I$row", $ganancia);
             $sheet->setCellValue("J$row", $descuento);
             $sheet->setCellValue("K$row", $fechaVenta);
             $sheet->setCellValue("L$row", $cliente);

             $totalVenta += $ventaDia;
             $totalGanancia += $ganancia;
             $row++;
         }

         // Agregar totales en la última fila
         $sheet->setCellValue("G$row", 'Totales');
         $sheet->setCellValue("H$row", $totalVenta);
         $sheet->setCellValue("I$row", $totalGanancia);

        
         // Aplicar formato de moneda mexicana a las columnas relevantes
         $sheet->getStyle("F9:F$row")->getNumberFormat()->setFormatCode('$#,##0.00');
         $sheet->getStyle("G9:G$row")->getNumberFormat()->setFormatCode('$#,##0.00');
         $sheet->getStyle("H9:H$row")->getNumberFormat()->setFormatCode('$#,##0.00');
         $sheet->getStyle("I9:I$row")->getNumberFormat()->setFormatCode('$#,##0.00');

         // Aplicar formato de moneda a los totales
         $sheet->getStyle("H$row")->getNumberFormat()->setFormatCode('$#,##0.00');
         $sheet->getStyle("I$row")->getNumberFormat()->setFormatCode('$#,##0.00');

         // Crear una tabla con estilo azul
         $tableRange = "B9:K$row"; // Rango completo de la tabla
         $table = new Table($tableRange);

         // Optional: apply some styling to the table
         $tableStyle = new TableStyle();
         $tableStyle->setTheme(TableStyle::TABLE_STYLE_MEDIUM9);
         $tableStyle->setShowRowStripes(true);
         $table->setStyle($tableStyle);

         // Add the table to the sheet
         $sheet->addTable($table);

         // Establecer ancho de columna automático
         foreach (range('B', 'K') as $col) {
             $sheet->getColumnDimension($col)->setAutoSize(true);
         }

         // Dar formato a la hoja
         $sheet->getStyle("B9:L9")->getFont()->setBold(true);
         $sheet->getStyle("B9:L$row")->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Definir el nombre del archivo
        $nombreArchivo = "Reporte_Cierre_Venta_" . date('Ymd_His') . ".xlsx";

        // Ruta completa para guardar el archivo en el servidor
        $rutaArchivo = $_SERVER['DOCUMENT_ROOT'] . "/sistema.posdit.com.mx/reportes/" . $nombreArchivo;

        // En local (XAMPP) esta carpeta no existe todavía; en producción normalmente ya está
        // creada, pero crearla aquí si falta evita que Xlsx::save() truene con una excepción
        // sin capturar (eso era lo que mandaba HTML de error en vez de JSON al navegador).
        $dirReportes = dirname($rutaArchivo);
        if (!is_dir($dirReportes)) {
            mkdir($dirReportes, 0755, true);
        }

        // Crear el archivo Excel
        $writer = new Xlsx($spreadsheet);

        // Guardar el archivo en la ruta definida
        $writer->save($rutaArchivo);

        // Antes el navegador armaba y mandaba esta petición él mismo, usando la URL pública
        // del archivo (https://posdit.com.mx/.../reportes/...) — eso nunca podía funcionar en
        // local, porque el archivo solo existe en la máquina del que lo genera. Igual que en
        // TicketReservacion.php y los reportes de Mantenimiento/Limpieza, se manda embebido en
        // base64: así funciona igual en producción y en local, sin depender de que la API de
        // WhatsApp pueda alcanzar el archivo por su cuenta. No es un ajuste temporal para
        // pruebas: se queda así también en producción.
        $telefono = trim((string) ($_SESSION["Telefono"] ?? ""));

        if ($telefono === "") {
            echo json_encode(["ok" => false, "mensaje" => "Tu usuario no tiene un teléfono guardado para recibir el reporte."]);
            return;
        }

        $Prefijo = 52;
        $Celular = $Prefijo . str_replace([' ', '(', ')', '-'], '', $telefono);
        $Tienda = $Negocio[0]["Razon_Social"] ?? "";

        $mediaBase64 = base64_encode(file_get_contents($rutaArchivo));

        $apiUrl = 'https://apiwsp.factiliza.com/api/v1/message/sendMedia/NTI1NTI1MzI3MzA0';
        $token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiI2MjciLCJuYW1lIjoiSnVhbiBEYXZpZCBBZ3VpbGFyIEJhcnJvbiAiLCJlbWFpbCI6ImFndWlsYXJiYXJyb25qdWFuZGF2aWRAZ21haWwuY29tIiwiaHR0cDovL3NjaGVtYXMubWljcm9zb2Z0LmNvbS93cy8yMDA4LzA2L2lkZW50aXR5L2NsYWltcy9yb2xlIjoiY29uc3VsdG9yIn0.r5cvSNgCntPbf4OCjqx1JlS885CxHSN7FyxCLlVBAus';
        $data = array(
            "number" => $Celular,
            "mediatype" => "document",
            "media" => $mediaBase64,
            "filename" => $nombreArchivo,
            "caption" => "Venta Reportada: $" . number_format((float) $ValorCierre, 2) . ", Caja: $" . number_format((float) $ValorCaja, 2) . ", para más detalles favor de consultar el archivo adjunto."
        );

        $resultado = $this->enviarMensajeAPI($apiUrl, $token, $data);
        echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
    }

    // Mismo criterio que TicketReservacion.php: se revisa el HTTP code y el cuerpo de la
    // respuesta antes de decir que se envió, en vez de asumir éxito solo porque cURL no truene.
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

        return ["ok" => true, "mensaje" => "Reporte enviado", "raw" => $response];
    }
}


// Crear y ejecutar el reporte
$ReporteInventario = new generaReporteDia();
$ReporteInventario->CodigoUsuario = $_GET["Cu"];
$ReporteInventario->MontoCierre = $_GET["mCierre"];
$ReporteInventario->MontoCaja = $_GET["mCaja"];
$ReporteInventario->traerReporteCierreDia();
