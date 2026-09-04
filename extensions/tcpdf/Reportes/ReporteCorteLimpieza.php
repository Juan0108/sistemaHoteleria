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
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

// Corte diario de Limpieza en Excel: limpiezas que iniciaron y/o terminaron HOY, solo para
// el perfil Administrador — se manda directo al teléfono que ya tiene guardado en su propia
// sesión, sin pedirlo (a diferencia del ticket de checkout, que sí es PDF).
class reporteCorteLimpieza {

    public function enviar() {

        if (!in_array($_SESSION["Perfil"] ?? "", ["Administrador", "Soporte Tecnico"], true)) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(["ok" => false, "mensaje" => "Este reporte solo lo puede generar el Administrador o Soporte Técnico."]);
            return;
        }

        $idUsuario = (int) ($_SESSION["IdUsuario"] ?? 0);
        $telefono = trim((string) ($_SESSION["Telefono"] ?? ""));

        if ($telefono === "") {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(["ok" => false, "mensaje" => "Tu usuario no tiene un teléfono guardado para recibir el reporte."]);
            return;
        }

        // Filtros de pantalla (habitación/usuario/Desde/Hasta); sin filtro, es el corte de HOY.
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

        // Con filtro de rango, la etiqueta deja de decir "diario" y muestra el rango real.
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

        $sheet->setCellValue('B2', 'Hotel:');
        $sheet->setCellValue('C2', $Tienda);
        $sheet->setCellValue('B3', $EtiquetaCorte);
        $sheet->setCellValue('C3', $Fecha);
        $sheet->setCellValue('B4', 'Total de limpiezas:');
        $sheet->setCellValue('C4', count($limpiezas));

        // Mismo estilo de tabla dinámica (DARK10) que ya usa el resumen de
        // ReporteCierreVenta.php, para que los 3 reportes se vean consistentes.
        $tablaResumen = new Table('B2:C4');
        $estiloResumen = new TableStyle();
        $estiloResumen->setTheme(TableStyle::TABLE_STYLE_DARK10);
        $estiloResumen->setShowRowStripes(true);
        $tablaResumen->setStyle($estiloResumen);
        $sheet->addTable($tablaResumen);

        // "Total de limpiezas" es numérico y Excel lo alinea a la derecha por default; se
        // fuerza a la izquierda para que quede parejo con Hotel/la fecha (texto).
        $sheet->getStyle('C2:C4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        // Resumen de horas trabajadas por empleado (E2:F..), sumando todas sus limpiezas
        // del corte sin importar la fecha.
        $minutosPorUsuario = [];
        foreach ($limpiezas as $item) {
            $usuario = $item['NombreUsuario'] ?: 'Sin asignar';
            if (!isset($minutosPorUsuario[$usuario])) {
                $minutosPorUsuario[$usuario] = 0;
            }
            if ($item['Fecha_Inicio'] && $item['Fecha_Fin']) {
                $minutosPorUsuario[$usuario] += (strtotime($item['Fecha_Fin']) - strtotime($item['Fecha_Inicio'])) / 60;
            }
        }

        $sheet->setCellValue('E2', 'Empleado');
        $sheet->setCellValue('F2', 'Hora Trabajadas');

        $filaEmpleado = 3;
        foreach ($minutosPorUsuario as $usuario => $minutosUsuario) {
            $minutosUsuario = round($minutosUsuario);
            $sheet->setCellValue("E$filaEmpleado", $usuario);
            $sheet->setCellValue("F$filaEmpleado", intdiv($minutosUsuario, 60) . 'hrs ' . ($minutosUsuario % 60) . 'Minutos');
            $filaEmpleado++;
        }

        if (count($minutosPorUsuario) > 0) {
            $ultimaFilaEmpleado = $filaEmpleado - 1;
            $tablaEmpleados = new Table("E2:F$ultimaFilaEmpleado");
            $estiloEmpleados = new TableStyle();
            $estiloEmpleados->setTheme(TableStyle::TABLE_STYLE_DARK10);
            $estiloEmpleados->setShowRowStripes(true);
            $tablaEmpleados->setStyle($estiloEmpleados);
            $sheet->addTable($tablaEmpleados);
        }

        $sheet->setCellValue('B6', 'Habitación');
        $sheet->setCellValue('C6', 'Usuario');
        $sheet->setCellValue('D6', 'Fecha');
        $sheet->setCellValue('E6', 'Hora inicio');
        $sheet->setCellValue('F6', 'Hora fin');
        $sheet->setCellValue('G6', 'Hrs Trabajada');
        $sheet->setCellValue('H6', 'Tareas realizadas');

        // Se agrupa por usuario (conservando el orden original — más reciente primero —
        // dentro de cada grupo) para que las limpiezas de un mismo empleado queden juntas y
        // se le pueda poner su total de minutos trabajados al final de cada bloque de fecha.
        $limpiezasPorUsuario = [];
        foreach ($limpiezas as $item) {
            $usuario = $item['NombreUsuario'] ?: 'Sin asignar';
            $limpiezasPorUsuario[$usuario][] = $item;
        }

        $row = 7;
        foreach ($limpiezasPorUsuario as $usuario => $itemsUsuario) {
            $minutosBloque = 0;
            $fechaBloque = null;

            foreach ($itemsUsuario as $item) {
                $horasTrabajadas = null;
                $fechaItem = $item['Fecha_Inicio'] ? date('Y-m-d', strtotime($item['Fecha_Inicio'])) : null;

                // Cambio de día dentro del bloque del mismo usuario: se cierra el subtotal
                // acumulado antes de arrancar el nuevo día.
                if ($fechaBloque !== null && $fechaItem !== $fechaBloque) {
                    $this->pintarSubtotal($sheet, $row - 1, $minutosBloque);
                    $minutosBloque = 0;
                }
                $fechaBloque = $fechaItem;

                if ($item['Fecha_Inicio'] && $item['Fecha_Fin']) {
                    $minutos = (strtotime($item['Fecha_Fin']) - strtotime($item['Fecha_Inicio'])) / 60;
                    $horasTrabajadas = $minutos / 60;
                    $minutosBloque += $minutos;
                }

                $sheet->setCellValue("B$row", $item['TipoHabitacion'] ?: $item['NumeroHabitacion']);
                $sheet->setCellValue("C$row", $usuario);
                $sheet->setCellValue("D$row", $item['Fecha_Inicio'] ? date('d/m/Y', strtotime($item['Fecha_Inicio'])) : '—');
                $sheet->setCellValue("E$row", $item['Fecha_Inicio'] ? date('H:i', strtotime($item['Fecha_Inicio'])) : '—');
                $sheet->setCellValue("F$row", $item['Fecha_Fin'] ? date('H:i', strtotime($item['Fecha_Fin'])) : 'En proceso');
                $sheet->setCellValue("G$row", $horasTrabajadas !== null ? round($horasTrabajadas, 2) : '');
                $sheet->setCellValue("H$row", $item['TareasRealizadas'] ?: '');
                $row++;
            }

            // Subtotal del último bloque de fecha del usuario.
            $this->pintarSubtotal($sheet, $row - 1, $minutosBloque);
        }

        $ultimaFilaDetalle = $row - 1;

        if (count($limpiezas) > 0) {
            $table = new Table("B6:H$ultimaFilaDetalle");
            $tableStyle = new TableStyle();
            $tableStyle->setTheme(TableStyle::TABLE_STYLE_MEDIUM9);
            $tableStyle->setShowRowStripes(true);
            $table->setStyle($tableStyle);
            $sheet->addTable($table);

            $sheet->getStyle("B6:H$ultimaFilaDetalle")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        }

        $sheet->getStyle('B6:H6')->getFont()->setBold(true);

        foreach (range('B', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $nombreArchivo = 'corte_limpieza_' . date('Ymd') . '_' . $idUsuario . '.xlsx';
        $rutaArchivo = dirname(__DIR__, 3) . '/tickets/' . $nombreArchivo;

        $writer = new Xlsx($spreadsheet);
        $writer->save($rutaArchivo);

        $urlArchivo = "https://posdit.com.mx/sistema.posdit.com.mx/tickets/" . $nombreArchivo;

        $apiUrl = 'https://apiwsp.factiliza.com/api/v1/message/sendMedia/NTI1NTI1MzI3MzA0';
        $token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiI2MjciLCJuYW1lIjoiSnVhbiBEYXZpZCBBZ3VpbGFyIEJhcnJvbiAiLCJlbWFpbCI6ImFndWlsYXJiYXJyb25qdWFuZGF2aWRAZ21haWwuY29tIiwiaHR0cDovL3NjaGVtYXMubWljcm9zb2Z0LmNvbS93cy8yMDA4LzA2L2lkZW50aXR5L2NsYWltcy9yb2xlIjoiY29uc3VsdG9yIn0.r5cvSNgCntPbf4OCjqx1JlS885CxHSN7FyxCLlVBAus';
        $data = array(
            "number" => $Celular,
            "mediatype" => "document",
            "media" => $urlArchivo,
            "filename" => "Corte diario Limpieza " . date('d-m-Y') . ".xlsx",
            "caption" => "Corte diario de Limpieza — " . $Tienda
        );

        $resultado = $this->enviarMensajeAPI($apiUrl, $token, $data);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
    }

    // Escribe el subtotal de "Minutos Laborados" de un bloque (columna I), resaltado en celeste.
    private function pintarSubtotal($sheet, $fila, $minutos) {
        $sheet->setCellValue("I$fila", round($minutos) . ' Minutos Laborados');
        $sheet->getStyle("I$fila")->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('40C4E8');
        $sheet->getStyle("I$fila")->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
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
