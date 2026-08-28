<?php

session_start();

require_once "../../../controllers/mantenimiento.controlador.php";
require_once "../../../models/mantenimiento.modelo.php";
require_once "../../../controllers/habitaciones.controlador.php";
require_once "../../../models/habitaciones.modelo.php";
require_once "../../../controllers/hoteles.controlador.php";
require_once "../../../models/hoteles.modelo.php";
require_once "../../../vendor/autoload.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Table;
use PhpOffice\PhpSpreadsheet\Worksheet\Table\TableStyle;
use PhpOffice\PhpSpreadsheet\Style\Border;

// Corte diario de Mantenimiento en Excel: cualquier movimiento de HOY (registro, cambio de
// estatus, reapertura...), solo para el perfil Administrador — se manda directo al teléfono
// que ya tiene guardado en su propia sesión, sin pedirlo (a diferencia del ticket de
// checkout, que sí es PDF).
class reporteCorteMantenimiento {

    public function enviar() {

        if (($_SESSION["Perfil"] ?? "") !== "Administrador") {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(["ok" => false, "mensaje" => "Este reporte solo lo puede generar el Administrador."]);
            return;
        }

        $idUsuario = (int) ($_SESSION["IdUsuario"] ?? 0);
        $telefono = trim((string) ($_SESSION["Telefono"] ?? ""));

        if ($telefono === "") {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(["ok" => false, "mensaje" => "Tu usuario no tiene un teléfono guardado para recibir el reporte."]);
            return;
        }

        $movimientos = ControladorMantenimiento::crtObtenerCorteDiarioMantenimiento();

        if ($movimientos === null) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(["ok" => false, "mensaje" => "No se pudo obtener el corte del día."]);
            return;
        }

        $Prefijo = 52;
        $Celular = $Prefijo . str_replace([' ', '(', ')', '-'], '', $telefono);

        $Negocio = ControladorHoteles::crtObtenerNegocioUsuarioReporte($idUsuario);
        $Tienda = $Negocio[0]["Razon_Social"] ?? "";
        $Fecha = date('d/m/Y');

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Corte Mantenimiento');

        $sheet->setCellValue('B2', 'Negocio:');
        $sheet->setCellValue('C2', $Tienda);
        $sheet->setCellValue('B3', 'Corte diario de Mantenimiento:');
        $sheet->setCellValue('C3', $Fecha);
        $sheet->setCellValue('B4', 'Total de movimientos:');
        $sheet->setCellValue('C4', count($movimientos));
        $sheet->getStyle('B2:B4')->getFont()->setBold(true);

        $sheet->setCellValue('B6', 'Hora');
        $sheet->setCellValue('C6', 'Habitación');
        $sheet->setCellValue('D6', 'Estatus');
        $sheet->setCellValue('E6', 'Descripción');
        $sheet->setCellValue('F6', 'Proveedor');
        $sheet->setCellValue('G6', 'Nota');
        $sheet->setCellValue('H6', 'Costo reparación');

        $row = 7;
        foreach ($movimientos as $item) {
            $sheet->setCellValue("B$row", date('H:i', strtotime($item['Fecha'])));
            $sheet->setCellValue("C$row", $item['TipoHabitacion'] ?: $item['NumeroHabitacion']);
            $sheet->setCellValue("D$row", ControladorMantenimiento::NOMBRES_ESTATUS[(int) $item['Id_Estatus']] ?? 'Otro');
            $sheet->setCellValue("E$row", $item['Descripcion'] ?: '');
            $sheet->setCellValue("F$row", $item['Proveedor'] ?: '');
            $sheet->setCellValue("G$row", $item['Nota'] ?: '');
            $sheet->setCellValue("H$row", (float) $item['CostoReparacion']);
            $row++;
        }

        if (count($movimientos) > 0) {
            $sheet->getStyle("H7:H" . ($row - 1))->getNumberFormat()->setFormatCode('$#,##0.00');

            $table = new Table("B6:H" . ($row - 1));
            $tableStyle = new TableStyle();
            $tableStyle->setTheme(TableStyle::TABLE_STYLE_MEDIUM9);
            $tableStyle->setShowRowStripes(true);
            $table->setStyle($tableStyle);
            $sheet->addTable($table);

            $sheet->getStyle("B6:H" . ($row - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        }

        $sheet->getStyle('B6:H6')->getFont()->setBold(true);

        foreach (range('B', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $nombreArchivo = 'corte_mantenimiento_' . date('Ymd') . '_' . $idUsuario . '.xlsx';
        $rutaArchivo = dirname(__DIR__, 3) . '/tickets/' . $nombreArchivo;

        $writer = new Xlsx($spreadsheet);
        $writer->save($rutaArchivo);

        // Se manda el archivo embebido en base64 (en vez de una URL pública): así funciona
        // igual en producción y en local, ya que la API de WhatsApp no necesita alcanzar
        // ningún archivo por su cuenta — el contenido va directo en la petición.
        $mediaBase64 = base64_encode(file_get_contents($rutaArchivo));

        $apiUrl = 'https://apiwsp.factiliza.com/api/v1/message/sendMedia/NTI1NTI1MzI3MzA0';
        $token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiI2MjciLCJuYW1lIjoiSnVhbiBEYXZpZCBBZ3VpbGFyIEJhcnJvbiAiLCJlbWFpbCI6ImFndWlsYXJiYXJyb25qdWFuZGF2aWRAZ21haWwuY29tIiwiaHR0cDovL3NjaGVtYXMubWljcm9zb2Z0LmNvbS93cy8yMDA4LzA2L2lkZW50aXR5L2NsYWltcy9yb2xlIjoiY29uc3VsdG9yIn0.r5cvSNgCntPbf4OCjqx1JlS885CxHSN7FyxCLlVBAus';
        $data = array(
            "number" => $Celular,
            "mediatype" => "document",
            "media" => $mediaBase64,
            "filename" => "Corte diario Mantenimiento " . date('d-m-Y') . ".xlsx",
            "caption" => "Corte diario de Mantenimiento — " . $Tienda
        );

        $resultado = $this->enviarMensajeAPI($apiUrl, $token, $data);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
    }

    // Mismo criterio que TicketReservacion.php: se revisa el HTTP code y el cuerpo de la
    // respuesta antes de decir que se envió, en vez de asumir éxito solo porque cURL no
    // truene.
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

$Reporte = new reporteCorteMantenimiento();
$Reporte->enviar();

//============================================================+
// END OF FILE
//============================================================+
