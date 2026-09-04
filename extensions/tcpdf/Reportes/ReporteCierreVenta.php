<?php

session_start();

require_once "../../../controllers/ventas.controlador.php";
require_once "../../../models/ventas.modelo.php";
require_once "../../../controllers/hoteles.controlador.php";
require_once "../../../models/hoteles.modelo.php";
require_once "../../../controllers/habitaciones.controlador.php";
require_once "../../../models/habitaciones.modelo.php";
require_once "../../../controllers/mantenimiento.controlador.php";
require_once "../../../models/mantenimiento.modelo.php";
require_once '../../../vendor/autoload.php';
require_once "../../../controllers/usuarios.controlador.php";
require_once "../../../models/usuarios.modelo.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Table;
use PhpOffice\PhpSpreadsheet\Worksheet\Table\TableStyle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Chart\Axis;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Layout;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;


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

         // Cargos de mantenimiento del día: lo que REALMENTE se cobró/pagó hoy hacia
         // reparaciones (suma de los abonos de hoy, sin importar de qué incidencia sean, cada
         // una contada una sola vez el día que se abonó) — no el costo total estimado de las
         // incidencias en las que hubo movimiento, que es otra cosa. Mismos datos que la hoja
         // "Mantenimiento" de este mismo libro.
         $id_hotel = ControladorHabitaciones::crtObtenerIdHotelSesion();
         $cargosMantenimiento = 0;
         $abonosMtto = [];

         if ($id_hotel !== null) {
             $abonosMtto = ModeloMantenimiento::MdlObtenerAbonosCorteDiario($id_hotel);

             foreach ($abonosMtto as $abono) {
                 $cargosMantenimiento += (float) $abono['TotalAbonadoDia'];
             }
         }

        // Crear el archivo Excel
         $spreadsheet = new Spreadsheet();
         $sheet = $spreadsheet->getActiveSheet();

        // Establecer Encabezado Información
         $sheet->setCellValue('B2', 'Hotel:');
         $sheet->setCellValue('B3', 'Vendedor:');
         $sheet->setCellValue('B4', 'Venta Reportada:');
         $sheet->setCellValue('B5', 'Venta Sistema:');
         $sheet->setCellValue('B6', 'Caja:');
         $sheet->setCellValue('B7', 'Cargos mantenimiento:');
         $sheet->setCellValue('B8', 'Diferencia:');

         $totalSistema = 0;
         $diferencia = 0;

         //Carga Info de ventas
         foreach ($Reporte as $valor){
             $ValorVenta = $valor['VentaDia'];
             $totalSistema += $ValorVenta;
         }

         // Los cargos de mantenimiento salen del efectivo igual que cualquier otro gasto de
         // caja, así que también se restan al calcular la diferencia.
         $diferencia = $ValorCierre - $totalSistema - $cargosMantenimiento;

        //Información
         $sheet->setCellValue('C2', $Negocio[0]["Razon_Social"]);
         $sheet->setCellValue('C3', $Usuario["Nombre"]);
         $sheet->setCellValue('C4', $ValorCierre);
         $sheet->setCellValue('C5', $totalSistema);
         $sheet->setCellValue('C6', $ValorCaja);
         $sheet->setCellValue('C7', $cargosMantenimiento);
         $sheet->setCellValue('C8', $diferencia);

         // Crear una tabla con estilo azul
         $tableRang2 = "B2:C8"; // Rango completo de la tabla
         $table2 = new Table($tableRang2);

        // Optional: apply some styling to the table
         $tableStyle2 = new TableStyle();
         $tableStyle2->setTheme(TableStyle::TABLE_STYLE_DARK10);
         $tableStyle2->setShowRowStripes(true);
         $table2->setStyle($tableStyle2);

         $sheet->getStyle("C4:C8")->getNumberFormat()->setFormatCode('$#,##0.00');
         // Los valores numéricos se alinean a la derecha por default en Excel; se fuerzan a
         // la izquierda para que queden parejos con Hotel/Vendedor (texto).
         $sheet->getStyle("C4:C8")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

         // Add the table to the sheet
         $sheet->addTable($table2);

         // Tabla independiente de Ganancia/Cargos/Total (E2:G3), mismo tema de color que la
         // tabla del Hotel para que se vea consistente con la identidad del reporte. El valor
         // de "Total Ganancia" se llena más abajo, una vez calculado $totalGanancia con el
         // detalle de ventas del día.
         $sheet->setCellValue('E2', 'Total Ganancia');
         $sheet->setCellValue('F2', 'Total Cargos');
         $sheet->setCellValue('G2', 'Total');
         $sheet->setCellValue('F3', $cargosMantenimiento);

         $tableGanancias = new Table('E2:G3');
         $estiloGanancias = new TableStyle();
         $estiloGanancias->setTheme(TableStyle::TABLE_STYLE_DARK10);
         $estiloGanancias->setShowRowStripes(true);
         $tableGanancias->setStyle($estiloGanancias);
         $sheet->addTable($tableGanancias);

         $sheet->getStyle('E3:G3')->getNumberFormat()->setFormatCode('$#,##0.00');
         $sheet->getStyle('E3:G3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);


         // Establecer títulos (fila 10, con un renglón de separación después de la tabla de
         // resumen que ahora termina en la fila 8)
         $filaEncabezado = 10;
         $sheet->setCellValue("B$filaEncabezado", 'Habitación');
         $sheet->setCellValue("C$filaEncabezado", 'Ticket');
         $sheet->setCellValue("D$filaEncabezado", 'Producto');
         $sheet->setCellValue("E$filaEncabezado", 'Cantidad');
         $sheet->setCellValue("F$filaEncabezado", 'Precio Compra');
         $sheet->setCellValue("G$filaEncabezado", 'Precio Venta');
         $sheet->setCellValue("H$filaEncabezado", 'Venta del Día');
         $sheet->setCellValue("I$filaEncabezado", 'Ganancia');
         $sheet->setCellValue("J$filaEncabezado", 'Descuento');
         $sheet->setCellValue("K$filaEncabezado", 'Fecha');
         $sheet->setCellValue("L$filaEncabezado", 'Cliente');

        // // Escribir datos
         $row = $filaEncabezado + 1;
         $totalVenta = 0;
         $totalGanancia = 0;

         foreach ($Reporte as $venta) {
             $habitacion = $venta['Habitacion'];
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
             $sheet->setCellValue("B$row", $habitacion ?: '—');
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

         // Termina de llenar la tabla de Ganancia/Cargos/Total (E2:G3): ya se conoce
         // $totalGanancia (suma de la columna Ganancia del detalle) y $cargosMantenimiento
         // (calculado al inicio del método).
         $sheet->setCellValue('E3', $totalGanancia);
         $sheet->setCellValue('G3', $totalGanancia - $cargosMantenimiento);


         // Aplicar formato de moneda mexicana a las columnas relevantes
         $sheet->getStyle("F$filaEncabezado:F$row")->getNumberFormat()->setFormatCode('$#,##0.00');
         $sheet->getStyle("G$filaEncabezado:G$row")->getNumberFormat()->setFormatCode('$#,##0.00');
         $sheet->getStyle("H$filaEncabezado:H$row")->getNumberFormat()->setFormatCode('$#,##0.00');
         $sheet->getStyle("I$filaEncabezado:I$row")->getNumberFormat()->setFormatCode('$#,##0.00');

         // Aplicar formato de moneda a los totales
         $sheet->getStyle("H$row")->getNumberFormat()->setFormatCode('$#,##0.00');
         $sheet->getStyle("I$row")->getNumberFormat()->setFormatCode('$#,##0.00');

         // Crear una tabla con estilo azul
         $tableRange = "B$filaEncabezado:L$row"; // Rango completo de la tabla
         $table = new Table($tableRange);

         // Optional: apply some styling to the table
         $tableStyle = new TableStyle();
         $tableStyle->setTheme(TableStyle::TABLE_STYLE_MEDIUM9);
         $tableStyle->setShowRowStripes(true);
         $table->setStyle($tableStyle);

         // Add the table to the sheet
         $sheet->addTable($table);

         // Establecer ancho de columna automático
         foreach (range('B', 'L') as $col) {
             $sheet->getColumnDimension($col)->setAutoSize(true);
         }

         // Dar formato a la hoja
         $sheet->getStyle("B$filaEncabezado:L$filaEncabezado")->getFont()->setBold(true);
         $sheet->getStyle("B$filaEncabezado:L$row")->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Nombre de la hoja: "Corte dd-mm-aaaa" (día de la consulta, no el de la venta —
        // este reporte siempre es del día en que se genera).
        $sheet->setTitle('Corte ' . date('d-m-Y'));

        // Gráfica de columnas verticales debajo de las tablas: eje X = las 4 etiquetas del
        // resumen (Venta Reportada, Venta Sistema, Caja, Cargos mantenimiento — Diferencia
        // se deja fuera a propósito, no es una cifra "de origen" sino un cálculo derivado),
        // eje Y = dinero, con el máximo = Venta Reportada + $1,500 de margen. Cada barra con
        // su propio color, y la cifra en formato moneda arriba de cada una.
        $hojaNombre = $sheet->getTitle();
        $filaInicioGraficas = $row + 2;

        $etiquetas = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'$hojaNombre'!\$B\$4:\$B\$7", null, 4);
        $valores = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'$hojaNombre'!\$C\$4:\$C\$7", null, 4);
        $valores->setFillColor(['4472C4', '70AD47', 'FFC000', 'C00000']);

        $etiquetasDatos = new Layout();
        $etiquetasDatos->setShowVal(true);
        $etiquetasDatos->setNumFmtCode('$#,##0.00');
        $etiquetasDatos->setNumFmtLinked(false);
        $etiquetasDatos->setDLblPos('outEnd');
        $valores->setLabelLayout($etiquetasDatos);

        $serie = new DataSeries(
            DataSeries::TYPE_BARCHART,
            DataSeries::GROUPING_CLUSTERED,
            [0],
            [],
            [$etiquetas],
            [$valores]
        );
        $serie->setPlotDirection(DataSeries::DIRECTION_COL);

        $plotArea = new PlotArea(null, [$serie]);
        $legend = new Legend(Legend::POSITION_RIGHT, null, false);
        $titulo = new Title('Resumen del corte');

        $grafica = new Chart('graficaCorte', $titulo, $legend, $plotArea);
        $grafica->getChartAxisY()->setAxisOptionsProperties(
            Axis::AXIS_LABELS_NEXT_TO,
            null, null, null, null, null,
            0,
            (float) $ValorCierre + 1500
        );
        $grafica->setTopLeftPosition('B' . $filaInicioGraficas);
        $grafica->setBottomRightPosition('F' . ($filaInicioGraficas + 24));

        $sheet->addChart($grafica);

        // Gráfica de pastel: Ingresos (Total Ganancia) vs Egresos (Total Cargos), tomando la
        // tabla E2:G3 de arriba. Al lado de la de barras, mismo alto (24 filas), columnas H a L.
        $etiquetasPastel = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'$hojaNombre'!\$E\$2:\$F\$2", null, 2);
        $valoresPastel = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'$hojaNombre'!\$E\$3:\$F\$3", null, 2);
        $valoresPastel->setFillColor(['70AD47', 'C00000']);

        $etiquetasDatosPastel = new Layout();
        $etiquetasDatosPastel->setShowVal(true);
        $etiquetasDatosPastel->setShowCatName(true);
        $etiquetasDatosPastel->setNumFmtCode('$#,##0.00');
        $etiquetasDatosPastel->setNumFmtLinked(false);
        $valoresPastel->setLabelLayout($etiquetasDatosPastel);

        $seriePastel = new DataSeries(
            DataSeries::TYPE_PIECHART,
            null,
            [0],
            [],
            [$etiquetasPastel],
            [$valoresPastel]
        );
        $plotAreaPastel = new PlotArea(null, [$seriePastel]);
        $legendPastel = new Legend(Legend::POSITION_RIGHT, null, false);
        $tituloPastel = new Title('Ingresos vs Egresos');

        $graficaPastel = new Chart('graficaIngresosEgresos', $tituloPastel, $legendPastel, $plotAreaPastel);
        $graficaPastel->setTopLeftPosition('H' . $filaInicioGraficas);
        $graficaPastel->setBottomRightPosition('L' . ($filaInicioGraficas + 24));

        $sheet->addChart($graficaPastel);

        // ===== Hoja "Mantenimiento": abonos de HOY, uno por incidencia =====
        // Ya trae $abonosMtto calculado arriba (mismos datos que "Cargos mantenimiento" del
        // resumen). Una fila por incidencia con abono hoy — no el historial completo — con
        // el estado, Inicio y Fin correspondientes al momento en que se hizo ESE abono.
        $hojaMtto = $spreadsheet->createSheet();
        $hojaMtto->setTitle('Mantenimiento');

        $hojaMtto->setCellValue('B2', 'Hotel:');
        $hojaMtto->setCellValue('C2', $Negocio[0]["Razon_Social"] ?? '');
        $hojaMtto->setCellValue('B3', 'Vendedor:');
        $hojaMtto->setCellValue('C3', $Usuario["Nombre"] ?? '');
        $hojaMtto->setCellValue('B4', 'Venta Reportada:');
        $hojaMtto->setCellValue('C4', $ValorCierre);
        $hojaMtto->setCellValue('B5', 'Venta Sistema:');
        $hojaMtto->setCellValue('C5', $totalSistema);
        $hojaMtto->setCellValue('B6', 'Caja:');
        $hojaMtto->setCellValue('C6', $ValorCaja);
        $hojaMtto->setCellValue('B7', 'Abonos del día:');
        $hojaMtto->setCellValue('C7', date('d/m/Y'));
        $hojaMtto->setCellValue('B8', 'Cargos mantenimiento:');
        $hojaMtto->setCellValue('C8', $cargosMantenimiento);
        $hojaMtto->setCellValue('B9', 'Diferencia:');
        $hojaMtto->setCellValue('C9', $diferencia);
        $hojaMtto->getStyle('C4:C9')->getNumberFormat()->setFormatCode('$#,##0.00');
        $hojaMtto->getStyle('C4:C9')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        // Tabla azul para identificar la hoja de Mantenimiento (la de resumen usa morado/verde).
        $tablaResumenMtto = new Table('B2:C9');
        $estiloResumenMtto = new TableStyle();
        $estiloResumenMtto->setTheme(TableStyle::TABLE_STYLE_MEDIUM9);
        $estiloResumenMtto->setShowRowStripes(true);
        $tablaResumenMtto->setStyle($estiloResumenMtto);
        $hojaMtto->addTable($tablaResumenMtto);

        $filaEncabezadoMtto = 11;
        $hojaMtto->setCellValue("B$filaEncabezadoMtto", 'Habitación');
        $hojaMtto->setCellValue("C$filaEncabezadoMtto", 'Proveedor');
        $hojaMtto->setCellValue("D$filaEncabezadoMtto", 'Inicio estimado');
        $hojaMtto->setCellValue("E$filaEncabezadoMtto", 'Fin estimado');
        $hojaMtto->setCellValue("F$filaEncabezadoMtto", 'Costo estimado');
        $hojaMtto->setCellValue("G$filaEncabezadoMtto", 'Total abonado');
        $hojaMtto->setCellValue("H$filaEncabezadoMtto", 'Saldo restante');
        $hojaMtto->setCellValue("I$filaEncabezadoMtto", 'Estado');
        $hojaMtto->setCellValue("J$filaEncabezadoMtto", 'Inicio');
        $hojaMtto->setCellValue("K$filaEncabezadoMtto", 'Fin');
        $hojaMtto->setCellValue("L$filaEncabezadoMtto", 'Descripción');

        $rowMtto = $filaEncabezadoMtto + 1;

        foreach ($abonosMtto as $abono) {
            $hojaMtto->setCellValue("B$rowMtto", $abono['TipoHabitacion'] ?: $abono['NumeroHabitacion']);
            $hojaMtto->setCellValue("C$rowMtto", $abono['Proveedor'] ?: '');
            $hojaMtto->setCellValue("D$rowMtto", $abono['Fecha_InicioEstimado'] ? date('d/m/Y', strtotime($abono['Fecha_InicioEstimado'])) : '');
            $hojaMtto->setCellValue("E$rowMtto", $abono['Fecha_FinEstimado'] ? date('d/m/Y', strtotime($abono['Fecha_FinEstimado'])) : '');
            $hojaMtto->setCellValue("F$rowMtto", (float) $abono['CostoReparacion']);
            $hojaMtto->setCellValue("G$rowMtto", (float) $abono['TotalAbonadoDia']);
            $hojaMtto->setCellValue("H$rowMtto", (float) $abono['SaldoRestante']);
            $hojaMtto->setCellValue("I$rowMtto", ControladorMantenimiento::NOMBRES_ESTATUS[(int) $abono['Id_Estatus']] ?? 'Otro');
            $hojaMtto->setCellValue("J$rowMtto", $abono['FechaInicioEstado'] ? date('d/m/Y H:i', strtotime($abono['FechaInicioEstado'])) : '');
            $hojaMtto->setCellValue("K$rowMtto", $abono['FechaFinEstado'] ? date('d/m/Y H:i', strtotime($abono['FechaFinEstado'])) : '');
            $hojaMtto->setCellValue("L$rowMtto", $abono['Descripcion'] ?: '');
            $rowMtto++;
        }

        $ultimaFilaMtto = $rowMtto - 1;

        if (count($abonosMtto) > 0) {
            $hojaMtto->getStyle("F$filaEncabezadoMtto:H$ultimaFilaMtto")->getNumberFormat()->setFormatCode('$#,##0.00');

            // Azul: color de identidad de la hoja Mantenimiento; morado/verde queda para "Corte dd-mm-aaaa".
            $tablaMtto = new Table("B$filaEncabezadoMtto:L$ultimaFilaMtto");
            $estiloMtto = new TableStyle();
            $estiloMtto->setTheme(TableStyle::TABLE_STYLE_MEDIUM9);
            $estiloMtto->setShowRowStripes(true);
            $tablaMtto->setStyle($estiloMtto);
            $hojaMtto->addTable($tablaMtto);

            $hojaMtto->getStyle("B$filaEncabezadoMtto:L$ultimaFilaMtto")->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        } else {
            $hojaMtto->setCellValue("B$filaEncabezadoMtto", 'Sin abonos registrados hoy.');
            $hojaMtto->getStyle("B$filaEncabezadoMtto")->getFont()->setItalic(true);
            $hojaMtto->setCellValue("C$filaEncabezadoMtto", '');
            foreach (range('D', 'L') as $col) {
                $hojaMtto->setCellValue("{$col}{$filaEncabezadoMtto}", '');
            }
        }

        $hojaMtto->getStyle("B$filaEncabezadoMtto:L$filaEncabezadoMtto")->getFont()->setBold(true);
        foreach (range('B', 'L') as $col) {
            $hojaMtto->getColumnDimension($col)->setAutoSize(true);
        }

        // La hoja de resumen ("Corte dd-mm-aaaa") se queda como la activa al abrir el archivo.
        $spreadsheet->setActiveSheetIndex(0);

        // Definir el nombre del archivo
        $nombreArchivo = "Reporte_Cierre_Venta_" . date('Ymd_His') . ".xlsx";

        // Ruta completa para guardar el archivo en el servidor
        $rutaArchivo = $_SERVER['DOCUMENT_ROOT'] . "/sistema.posdit.com.mx/reportes/" . $nombreArchivo;

        // Crear la carpeta si no existe evita que Xlsx::save() truene con una excepción sin
        // capturar, que mandaría HTML de error en vez de JSON al navegador.
        $dirReportes = dirname($rutaArchivo);
        if (!is_dir($dirReportes)) {
            mkdir($dirReportes, 0755, true);
        }

        // Crear el archivo Excel
        $writer = new Xlsx($spreadsheet);
        // Sin esto, el Xlsx Writer de PhpSpreadsheet omite las gráficas por completo del
        // archivo, aunque ya estén agregadas a la hoja.
        $writer->setIncludeCharts(true);

        // Guardar el archivo en la ruta definida
        $writer->save($rutaArchivo);

        // PhpSpreadsheet deja fijo el espacio entre barras de una gráfica de columnas en 150%
        // (c:gapWidth) — no hay ningún método público para cambiarlo. Se reduce aquí editando
        // directamente el XML de la gráfica ya guardada, para que las barras queden más juntas.
        self::crtEstrecharEspacioBarras($rutaArchivo, 40);

        // Antes de esto, los montos del corte solo vivían en el Excel/WhatsApp: no había
        // forma de consultar el corte de un día anterior. Se guarda aquí, ya con todos los
        // montos calculados y el Excel generado.
        if ($id_hotel !== null) {
            ControladorVentas::crtGuardarCorteDiario(
                $id_hotel,
                (int) $idusuario,
                (float) $ValorCierre,
                (float) $totalSistema,
                (float) $ValorCaja,
                (float) $cargosMantenimiento,
                (float) $diferencia,
                $nombreArchivo
            );
        }

        // Se manda embebido en base64 (no por URL pública) para que funcione igual en
        // producción y en local, sin depender de que la API de WhatsApp alcance el archivo.
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

    // Reemplaza el "c:gapWidth" de TODAS las gráficas de columnas/barras dentro del .xlsx ya
    // guardado (chart1.xml, chart2.xml, ...) por el valor pedido — PhpSpreadsheet no expone
    // ningún método público para configurarlo en este tipo de gráfica.
    private static function crtEstrecharEspacioBarras($rutaArchivo, $gapWidth) {
        $zip = new \ZipArchive();

        if ($zip->open($rutaArchivo) !== true) {
            return;
        }

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $nombre = $zip->getNameIndex($i);

            if ($nombre === false || strpos($nombre, 'xl/charts/chart') !== 0) {
                continue;
            }

            $contenido = $zip->getFromName($nombre);

            if ($contenido === false || strpos($contenido, '<c:gapWidth') === false) {
                continue;
            }

            $contenido = preg_replace('/<c:gapWidth val="\d+"\/>/', '<c:gapWidth val="' . (int) $gapWidth . '"/>', $contenido);
            $zip->addFromString($nombre, $contenido);
        }

        $zip->close();
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
