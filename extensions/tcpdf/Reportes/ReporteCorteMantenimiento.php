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
use PhpOffice\PhpSpreadsheet\Style\Alignment;

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

        // Mismos filtros que ya existen en pantalla (habitación/Desde/Hasta del tab
        // Historial): si no se mandan, se comporta igual que antes (corte de HOY).
        $fechaDesde = trim((string) ($_GET["fechaDesde"] ?? ""));
        $fechaHasta = trim((string) ($_GET["fechaHasta"] ?? ""));
        $idHabitacion = isset($_GET["idHabitacion"]) ? (int) $_GET["idHabitacion"] : null;

        $movimientos = ControladorMantenimiento::crtObtenerCorteDiarioMantenimiento($fechaDesde ?: null, $fechaHasta ?: null, $idHabitacion);

        if ($movimientos === null) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(["ok" => false, "mensaje" => "No se pudo obtener el corte del día."]);
            return;
        }

        $id_hotel = ControladorMantenimiento::crtObtenerIdHotelSesion();

        $Prefijo = 52;
        $Celular = $Prefijo . str_replace([' ', '(', ')', '-'], '', $telefono);

        $Negocio = ControladorHoteles::crtObtenerNegocioUsuarioReporte($idUsuario);
        $Tienda = $Negocio[0]["Razon_Social"] ?? "";

        // Con filtro de rango, la etiqueta deja de decir "diario" y muestra el rango real;
        // sin filtro (ambas fechas vacías) se ve exactamente igual que antes (corte de hoy).
        if ($fechaDesde !== "" || $fechaHasta !== "") {
            $EtiquetaCorte = 'Corte de Mantenimiento:';
            $inicioMostrar = $fechaDesde !== "" ? date('d/m/Y', strtotime($fechaDesde)) : date('d/m/Y');
            $finMostrar = $fechaHasta !== "" ? date('d/m/Y', strtotime($fechaHasta)) : date('d/m/Y');
            $Fecha = $inicioMostrar === $finMostrar ? $inicioMostrar : "$inicioMostrar - $finMostrar";
        } else {
            $EtiquetaCorte = 'Corte diario de Mantenimiento:';
            $Fecha = date('d/m/Y');
        }

        // Se agrupa por incidencia (Id_Mantenimiento): el SP ya entrega los movimientos de
        // cada incidencia en orden cronológico, así que cada grupo refleja el progreso real
        // (Pendiente -> Proceso -> Resuelto, y si se reabrió, vuelve a Pendiente y sigue).
        $grupos = [];
        foreach ($movimientos as $item) {
            $idMtto = (int) $item['Id_Mantenimiento'];
            if (!isset($grupos[$idMtto])) {
                $grupos[$idMtto] = [];
            }
            $grupos[$idMtto][] = $item;
        }

        // La incidencia con el movimiento más reciente aparece primero.
        usort($grupos, function ($a, $b) {
            return strtotime(end($b)['Fecha']) <=> strtotime(end($a)['Fecha']);
        });

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Corte Mantenimiento');

        $totalIncidencias = count($grupos);

        $sheet->setCellValue('B2', 'Hotel:');
        $sheet->setCellValue('C2', $Tienda);
        $sheet->setCellValue('B3', $EtiquetaCorte);
        $sheet->setCellValue('C3', $Fecha);
        $sheet->setCellValue('B4', 'Total de incidencias:');
        $sheet->setCellValue('C4', $totalIncidencias);
        $sheet->setCellValue('B5', 'Total costo:');

        // Mismo estilo de tabla dinámica (DARK10) que ya usa el resumen de
        // ReporteCierreVenta.php, para que los 3 reportes se vean consistentes.
        $tablaResumen = new Table('B2:C5');
        $estiloResumen = new TableStyle();
        $estiloResumen->setTheme(TableStyle::TABLE_STYLE_DARK10);
        $estiloResumen->setShowRowStripes(true);
        $tablaResumen->setStyle($estiloResumen);
        $sheet->addTable($tablaResumen);

        // Los valores numéricos (Total de incidencias, Total costo) se alinean a la derecha
        // por default en Excel; se fuerzan a la izquierda para que queden parejos con el
        // Hotel/la fecha, que sí son texto.
        $sheet->getStyle('C2:C5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $sheet->setCellValue('B7', 'Habitación');
        $sheet->setCellValue('C7', 'Descripción');
        $sheet->setCellValue('D7', 'Proveedor');
        $sheet->setCellValue('E7', 'Inicio estimado');
        $sheet->setCellValue('F7', 'Fin estimado');
        $sheet->setCellValue('G7', 'Costo estimado');
        $sheet->setCellValue('H7', 'Total abonado');
        $sheet->setCellValue('I7', 'Saldo restante');
        $sheet->setCellValue('J7', 'Estado');
        $sheet->setCellValue('K7', 'Inicio');
        $sheet->setCellValue('L7', 'Fin');

        $row = 8;
        $totalCosto = 0;

        foreach ($grupos as $filasIncidencia) {
            $primera = $filasIncidencia[0];
            $idMtto = (int) $primera['Id_Mantenimiento'];
            $filaInicio = $row;

            $costo = (float) $primera['CostoReparacion'];
            $totalCosto += $costo;

            // Total abonado/saldo restante: mismo resumen que ya usa el tablero de
            // Mantenimiento (ObtenerResumenAbonos), una sola vez por incidencia.
            $resumenAbonos = ModeloMantenimiento::MdlObtenerResumenAbonos($idMtto, $id_hotel);
            $totalAbonado = $resumenAbonos ? ((float) $resumenAbonos['SaldoInicial'] - (float) $resumenAbonos['SaldoRestante']) : 0;
            $saldoRestante = $resumenAbonos ? (float) $resumenAbonos['SaldoRestante'] : $costo;

            // El SP ya trae el historial COMPLETO de cada incidencia con movimiento en el
            // rango pedido (no solo un día), así que cada renglón es un estado real de su
            // línea de tiempo. "Fin" de un estado = fecha Y HORA de inicio del SIGUIENTE
            // estado de esa misma incidencia (no hay una columna de "fin real" en la BD); el
            // último estado (normalmente Resuelto, o el más reciente si sigue abierta) no
            // tiene fin porque sigue vigente. Fecha+hora van juntas en una sola columna (acá
            // no hay riesgo de que se corten como en el PDF de TCPDF) para que quede claro
            // que cada hora es DE esa fecha, y no de la columna de al lado.
            foreach ($filasIncidencia as $indice => $item) {
                $siguiente = $filasIncidencia[$indice + 1] ?? null;

                $sheet->setCellValue("J$row", ControladorMantenimiento::NOMBRES_ESTATUS[(int) $item['Id_Estatus']] ?? 'Otro');
                $sheet->setCellValue("K$row", date('d/m/Y H:i', strtotime($item['Fecha'])));
                $sheet->setCellValue("L$row", $siguiente ? date('d/m/Y H:i', strtotime($siguiente['Fecha'])) : '');
                $row++;
            }

            $filaFin = $row - 1;

            $sheet->setCellValue("B$filaInicio", $primera['TipoHabitacion'] ?: $primera['NumeroHabitacion']);
            $sheet->setCellValue("C$filaInicio", $primera['Descripcion'] ?: '');
            $sheet->setCellValue("D$filaInicio", $primera['Proveedor'] ?: '');
            $sheet->setCellValue("E$filaInicio", $primera['Fecha_InicioEstimado'] ? date('d/m/Y', strtotime($primera['Fecha_InicioEstimado'])) : '');
            $sheet->setCellValue("F$filaInicio", $primera['Fecha_FinEstimado'] ? date('d/m/Y', strtotime($primera['Fecha_FinEstimado'])) : '');
            $sheet->setCellValue("G$filaInicio", $costo);
            $sheet->setCellValue("H$filaInicio", $totalAbonado);
            $sheet->setCellValue("I$filaInicio", $saldoRestante);

            // El costo (y el resto de datos de la incidencia) va en UNA sola celda combinada
            // que abarca todos los estados por los que pasó hoy — repetirlo en cada renglón
            // daría a entender que cada estado tuvo un costo distinto.
            if ($filaFin > $filaInicio) {
                foreach (['B', 'C', 'D', 'E', 'F', 'G', 'H', 'I'] as $col) {
                    $sheet->mergeCells("{$col}{$filaInicio}:{$col}{$filaFin}");
                    $sheet->getStyle("{$col}{$filaInicio}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                }
            }
        }

        $sheet->setCellValue('C5', $totalCosto);
        $sheet->getStyle('C5')->getNumberFormat()->setFormatCode('$#,##0.00');

        $ultimaFila = $row - 1;

        if ($totalIncidencias > 0) {
            $sheet->getStyle("G8:I$ultimaFila")->getNumberFormat()->setFormatCode('$#,##0.00');

            $table = new Table("B7:L$ultimaFila");
            $tableStyle = new TableStyle();
            $tableStyle->setTheme(TableStyle::TABLE_STYLE_MEDIUM9);
            $tableStyle->setShowRowStripes(true);
            $table->setStyle($tableStyle);
            $sheet->addTable($table);

            $sheet->getStyle("B7:L$ultimaFila")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        }

        $sheet->getStyle('B7:L7')->getFont()->setBold(true);

        foreach (range('B', 'L') as $col) {
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
