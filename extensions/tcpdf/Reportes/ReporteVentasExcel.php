<?php

require_once "../../../controllers/ventas.controlador.php";
require_once "../../../models/ventas.modelo.php";
require_once "../../../controllers/hoteles.controlador.php";
require_once "../../../models/hoteles.modelo.php";
require_once "../../../vendor/autoload.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Table;
use PhpOffice\PhpSpreadsheet\Worksheet\Table\TableStyle;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

// Misma información que ya genera ReporteVentas.php en PDF, pero en Excel editable:
// mismo rango de fechas, mismos datos (ObtenerVentasFecha) y los mismos totales.
class reporteVentasExcel {

    public $CodigoUsuario;
    public $FechaInicio;
    public $FechaFin;

    public function generar() {

        $idUsuario = $this->CodigoUsuario;
        $fi = $this->FechaInicio;
        $ff = $this->FechaFin;

        $fiFormato = date('d/m/Y', strtotime($fi));
        $ffFormato = date('d/m/Y', strtotime($ff));

        $Ventas = ControladorVentas::crtObtenerVentasFecha($idUsuario, $fi, $ff);
        $Negocio = ControladorHoteles::crtObtenerNegocioUsuarioReporte($idUsuario);

        $Tienda = $Negocio[0]["Razon_Social"] ?? "";
        $Estado = $Negocio[0]["Estado"] ?? "";
        $Municipio = $Negocio[0]["Municipio"] ?? "";
        $Colonia = $Negocio[0]["Colonia"] ?? "";
        $Calle = $Negocio[0]["Calle"] ?? "";
        $Telefono = $Negocio[0]["Telefono"] ?? "";
        $Correo = $Negocio[0]["Correo"] ?? "";
        $FechaConsulta = date('d/m/Y');

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Reporte de Ventas');

        $sheet->setCellValue('B2', 'Hotel:');
        $sheet->setCellValue('C2', $Tienda);
        $sheet->setCellValue('B3', 'Estado:');
        $sheet->setCellValue('C3', $Estado);
        $sheet->setCellValue('B4', 'Municipio:');
        $sheet->setCellValue('C4', $Municipio);
        $sheet->setCellValue('B5', 'Colonia:');
        $sheet->setCellValue('C5', $Colonia);
        $sheet->setCellValue('B6', 'Calle:');
        $sheet->setCellValue('C6', $Calle);
        $sheet->setCellValue('B7', 'Teléfono:');
        $sheet->setCellValue('C7', $Telefono);
        $sheet->setCellValue('B8', 'Correo:');
        $sheet->setCellValue('C8', $Correo);
        $sheet->setCellValue('B9', 'Fecha consulta:');
        $sheet->setCellValue('C9', $FechaConsulta);
        $sheet->setCellValue('B10', 'Periodo:');
        $sheet->setCellValue('C10', "$fiFormato - $ffFormato");

        $tablaResumen = new Table('B2:C10');
        $estiloResumen = new TableStyle();
        $estiloResumen->setTheme(TableStyle::TABLE_STYLE_DARK10);
        $estiloResumen->setShowRowStripes(true);
        $tablaResumen->setStyle($estiloResumen);
        $sheet->addTable($tablaResumen);

        $sheet->getStyle('C2:C10')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $filaEncabezado = 12;
        $sheet->setCellValue("B$filaEncabezado", 'Fecha Compra');
        $sheet->setCellValue("C$filaEncabezado", 'Vendedor');
        $sheet->setCellValue("D$filaEncabezado", 'Sub-Marca');
        $sheet->setCellValue("E$filaEncabezado", 'Producto');
        $sheet->setCellValue("F$filaEncabezado", 'Cantidad');
        $sheet->setCellValue("G$filaEncabezado", 'Precio Compra');
        $sheet->setCellValue("H$filaEncabezado", 'Precio Venta');
        $sheet->setCellValue("I$filaEncabezado", 'Ganancia');
        $sheet->setCellValue("J$filaEncabezado", 'Total');

        $row = $filaEncabezado + 1;
        $totalVenta = 0;
        $totalGanancia = 0;

        foreach ($Ventas as $item) {
            $cantidad = (float) $item['Cantidad'];
            $precioCompra = (float) $item['PrecioCompra'];
            $precioVenta = (float) $item['PrecioVenta'];
            $ganancia = ($precioVenta - $precioCompra) * $cantidad;
            $total = $precioVenta * $cantidad;

            $totalVenta += $total;
            $totalGanancia += $ganancia;

            $sheet->setCellValue("B$row", $item['Fecha_Compra'] ? date('d/m/Y', strtotime($item['Fecha_Compra'])) : '');
            $sheet->setCellValue("C$row", $item['Vendedor'] ?? '');
            $sheet->setCellValue("D$row", $item['Submarca'] ?? '');
            $sheet->setCellValue("E$row", $item['Producto'] ?? '');
            $sheet->setCellValue("F$row", $cantidad);
            $sheet->setCellValue("G$row", $precioCompra);
            $sheet->setCellValue("H$row", $precioVenta);
            $sheet->setCellValue("I$row", $ganancia);
            $sheet->setCellValue("J$row", $total);

            $row++;
        }

        $ultimaFila = $row - 1;
        $totalVentas = count($Ventas);

        if ($totalVentas > 0) {
            $sheet->getStyle("G" . ($filaEncabezado + 1) . ":J$ultimaFila")->getNumberFormat()->setFormatCode('$#,##0.00');
            $sheet->getStyle("G" . ($filaEncabezado + 1) . ":J$ultimaFila")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

            $tabla = new Table("B$filaEncabezado:J$ultimaFila");
            $estiloTabla = new TableStyle();
            $estiloTabla->setTheme(TableStyle::TABLE_STYLE_MEDIUM9);
            $estiloTabla->setShowRowStripes(true);
            $tabla->setStyle($estiloTabla);
            $sheet->addTable($tabla);

            $sheet->getStyle("B$filaEncabezado:J$ultimaFila")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        }

        $sheet->getStyle("B$filaEncabezado:J$filaEncabezado")->getFont()->setBold(true);

        $filaTotales = $ultimaFila + 2;
        $sheet->setCellValue("B$filaTotales", 'Concepto');
        $sheet->setCellValue("C$filaTotales", 'Monto');
        $sheet->setCellValue("B" . ($filaTotales + 1), 'Ventas');
        $sheet->setCellValue("C" . ($filaTotales + 1), $totalVenta);
        $sheet->setCellValue("B" . ($filaTotales + 2), 'Ganancias');
        $sheet->setCellValue("C" . ($filaTotales + 2), $totalGanancia);

        $tablaTotales = new Table("B$filaTotales:C" . ($filaTotales + 2));
        $estiloTotales = new TableStyle();
        $estiloTotales->setTheme(TableStyle::TABLE_STYLE_DARK10);
        $estiloTotales->setShowRowStripes(true);
        $tablaTotales->setStyle($estiloTotales);
        $sheet->addTable($tablaTotales);

        $sheet->getStyle("C" . ($filaTotales + 1) . ":C" . ($filaTotales + 2))->getNumberFormat()->setFormatCode('$#,##0.00');
        $sheet->getStyle("C" . ($filaTotales + 1) . ":C" . ($filaTotales + 2))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        foreach (range('B', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $nombreArchivo = 'ReporteVentas_' . date('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $nombreArchivo . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    }
}

$Reporte = new reporteVentasExcel();
$Reporte->CodigoUsuario = $_GET["Cu"];
$Reporte->FechaInicio = $_GET["fi"];
$Reporte->FechaFin = $_GET["ff"];
$Reporte->generar();

//============================================================+
// END OF FILE
//============================================================+
