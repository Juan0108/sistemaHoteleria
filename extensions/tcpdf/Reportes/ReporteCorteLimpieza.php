<?php

session_start();

require_once "../../../controllers/servicio.controlador.php";
require_once "../../../models/servicio.modelo.php";
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

// Corte diario de Limpieza en Excel: limpiezas que iniciaron y/o terminaron HOY, solo para
// el perfil Administrador — se manda directo al teléfono que ya tiene guardado en su propia
// sesión, sin pedirlo (a diferencia del ticket de checkout, que sí es PDF).
class reporteCorteLimpieza {

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

        // Mismos filtros que ya existen en pantalla (habitación/usuario/Desde/Hasta): si no
        // se mandan, se comporta igual que antes (corte de HOY).
        $fechaDesde = trim((string) ($_GET["fechaDesde"] ?? ""));
        $fechaHasta = trim((string) ($_GET["fechaHasta"] ?? ""));
        $idHabitacion = isset($_GET["idHabitacion"]) ? (int) $_GET["idHabitacion"] : null;
        $nombreUsuarioFiltro = trim((string) ($_GET["nombreUsuarioFiltro"] ?? ""));

        $limpiezas = ControladorServicio::crtObtenerCorteDiarioLimpieza($fechaDesde ?: null, $fechaHasta ?: null, $idHabitacion, $nombreUsuarioFiltro ?: null);

        if ($limpiezas === null) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(["ok" => false, "mensaje" => "No se pudo obtener el corte del día."]);
            return;
        }

        $Prefijo = 52;
        $Celular = $Prefijo . str_replace([' ', '(', ')', '-'], '', $telefono);

        $Negocio = ControladorHoteles::crtObtenerNegocioUsuarioReporte($idUsuario);
        $Tienda = $Negocio[0]["Razon_Social"] ?? "";

        // Con filtro de rango, la etiqueta deja de decir "diario" y muestra el rango real;
        // sin filtro (ambas fechas vacías) se ve exactamente igual que antes (corte de hoy).
        if ($fechaDesde !== "" || $fechaHasta !== "") {
            $EtiquetaCorte = 'Corte de Limpieza:';
            $inicioMostrar = $fechaDesde !== "" ? date('d/m/Y', strtotime($fechaDesde)) : date('d/m/Y');
            $finMostrar = $fechaHasta !== "" ? date('d/m/Y', strtotime($fechaHasta)) : date('d/m/Y');
            $Fecha = $inicioMostrar === $finMostrar ? $inicioMostrar : "$inicioMostrar - $finMostrar";
        } else {
            $EtiquetaCorte = 'Corte diario de Limpieza:';
            $Fecha = date('d/m/Y');
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Corte Limpieza');

        $sheet->setCellValue('B2', 'Negocio:');
        $sheet->setCellValue('C2', $Tienda);
        $sheet->setCellValue('B3', $EtiquetaCorte);
        $sheet->setCellValue('C3', $Fecha);
        $sheet->setCellValue('B4', 'Total de limpiezas:');
        $sheet->setCellValue('C4', count($limpiezas));
        $sheet->getStyle('B2:B4')->getFont()->setBold(true);

        $sheet->setCellValue('B6', 'Habitación');
        $sheet->setCellValue('C6', 'Usuario');
        $sheet->setCellValue('D6', 'Inicio');
        $sheet->setCellValue('E6', 'Fin');
        $sheet->setCellValue('F6', 'Tareas realizadas');

        $row = 7;
        foreach ($limpiezas as $item) {
            $sheet->setCellValue("B$row", $item['TipoHabitacion'] ?: $item['NumeroHabitacion']);
            $sheet->setCellValue("C$row", $item['NombreUsuario'] ?: 'Sin asignar');
            $sheet->setCellValue("D$row", $item['Fecha_Inicio'] ? date('H:i', strtotime($item['Fecha_Inicio'])) : '—');
            $sheet->setCellValue("E$row", $item['Fecha_Fin'] ? date('H:i', strtotime($item['Fecha_Fin'])) : 'En proceso');
            $sheet->setCellValue("F$row", $item['TareasRealizadas'] ?: '');
            $row++;
        }

        if (count($limpiezas) > 0) {
            $table = new Table("B6:F" . ($row - 1));
            $tableStyle = new TableStyle();
            $tableStyle->setTheme(TableStyle::TABLE_STYLE_MEDIUM9);
            $tableStyle->setShowRowStripes(true);
            $table->setStyle($tableStyle);
            $sheet->addTable($table);

            $sheet->getStyle("B6:F" . ($row - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        }

        $sheet->getStyle('B6:F6')->getFont()->setBold(true);

        foreach (range('B', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $nombreArchivo = 'corte_limpieza_' . date('Ymd') . '_' . $idUsuario . '.xlsx';
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
            "filename" => "Corte diario Limpieza " . date('d-m-Y') . ".xlsx",
            "caption" => "Corte diario de Limpieza — " . $Tienda
        );

        $resultado = $this->enviarMensajeAPI($apiUrl, $token, $data);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
    }

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

$Reporte = new reporteCorteLimpieza();
$Reporte->enviar();

//============================================================+
// END OF FILE
//============================================================+
