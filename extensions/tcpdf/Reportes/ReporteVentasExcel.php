<?php

session_start();

require_once "../../../controllers/ventas.controlador.php";
require_once "../../../models/ventas.modelo.php";
require_once "../../../controllers/hoteles.controlador.php";
require_once "../../../models/hoteles.modelo.php";
require_once "../../../controllers/habitaciones.controlador.php";
require_once "../../../models/habitaciones.modelo.php";
require_once "../../../vendor/autoload.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Table;
use PhpOffice\PhpSpreadsheet\Worksheet\Table\TableStyle;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Chart\Axis;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Layout;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;

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

        $telefono = trim((string) ($_SESSION["Telefono"] ?? ""));

        if ($telefono === "") {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(["ok" => false, "mensaje" => "Tu usuario no tiene un teléfono guardado para recibir el reporte."]);
            return;
        }

        $fiFormato = date('d/m/Y', strtotime($fi));
        $ffFormato = date('d/m/Y', strtotime($ff));

        $Ventas = ControladorVentas::crtObtenerVentasFecha($idUsuario, $fi, $ff);
        $Negocio = ControladorHoteles::crtObtenerNegocioUsuarioReporte($idUsuario);

        $idHotel = ControladorHabitaciones::crtObtenerIdHotelSesion();

        // Cortes diarios ya generados (ver ReporteCierreVenta.php) dentro del mismo periodo:
        // no se listan en este reporte, solo se usan para sumar los cargos de mantenimiento
        // del periodo (cada corte diario ya trae calculado el cargo de SU día).
        $Cortes = $idHotel !== null ? ControladorVentas::crtObtenerCortesDiarios($idHotel, $fi, $ff) : [];

        // Consumo de habitaciones del periodo, para la gráfica de barras de la hoja Dashboard.
        $ConsumoHabitaciones = $idHotel !== null
            ? ControladorHabitaciones::crtObtenerVentasPorTipoHabitacionRango($idHotel, $fi, $ff)
            : ["etiquetas" => [], "habitaciones" => [], "colores" => [], "datos" => [], "montos" => []];

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

        // ===== Detalle de ventas (calculado antes de escribir las tablas de resumen de
        // arriba, ya que "Concepto/Monto" y "Cargos" necesitan $totalVenta/$totalGanancia). =====
        $filaEncabezado = 12;
        $sheet->setCellValue("B$filaEncabezado", 'Habitación');
        $sheet->setCellValue("C$filaEncabezado", 'Fecha compra');
        $sheet->setCellValue("D$filaEncabezado", 'Vendedor');
        $sheet->setCellValue("E$filaEncabezado", 'Sub-Marca');
        $sheet->setCellValue("F$filaEncabezado", 'Producto');
        $sheet->setCellValue("G$filaEncabezado", 'Cantidad');
        $sheet->setCellValue("H$filaEncabezado", 'Precio Compra');
        $sheet->setCellValue("I$filaEncabezado", 'Precio Venta');
        $sheet->setCellValue("J$filaEncabezado", 'Ganancia');
        $sheet->setCellValue("K$filaEncabezado", 'Total');

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

            $sheet->setCellValue("B$row", $item['Habitacion'] ?? '');
            $sheet->setCellValue("C$row", $item['Fecha_Compra'] ? date('d/m/Y', strtotime($item['Fecha_Compra'])) : '');
            $sheet->setCellValue("D$row", $item['Vendedor'] ?? '');
            $sheet->setCellValue("E$row", $item['Submarca'] ?? '');
            $sheet->setCellValue("F$row", $item['Producto'] ?? '');
            $sheet->setCellValue("G$row", $cantidad);
            $sheet->setCellValue("H$row", $precioCompra);
            $sheet->setCellValue("I$row", $precioVenta);
            $sheet->setCellValue("J$row", $ganancia);
            $sheet->setCellValue("K$row", $total);

            $row++;
        }

        $ultimaFila = $row - 1;
        $totalVentas = count($Ventas);

        if ($totalVentas > 0) {
            $sheet->getStyle("H" . ($filaEncabezado + 1) . ":K$ultimaFila")->getNumberFormat()->setFormatCode('$#,##0.00');
            $sheet->getStyle("H" . ($filaEncabezado + 1) . ":K$ultimaFila")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

            $tabla = new Table("B$filaEncabezado:K$ultimaFila");
            $estiloTabla = new TableStyle();
            $estiloTabla->setTheme(TableStyle::TABLE_STYLE_MEDIUM9);
            $estiloTabla->setShowRowStripes(true);
            $tabla->setStyle($estiloTabla);
            $sheet->addTable($tabla);

            $sheet->getStyle("B$filaEncabezado:K$ultimaFila")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        }

        $sheet->getStyle("B$filaEncabezado:K$filaEncabezado")->getFont()->setBold(true);

        foreach (range('B', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Cargos de mantenimiento del periodo: suma de lo que cada corte diario ya calculó
        // para su propio día (mismo dato que "Cargos Mantenimiento" en la hoja Dashboard).
        // "Pagos nómina" se deja en 0 — todavía no existe esa fuente de datos en el sistema.
        $cargosMantenimientoPeriodo = 0;
        foreach ($Cortes as $corte) {
            $cargosMantenimientoPeriodo += (float) $corte['CargosMantenimiento'];
        }
        $pagosNomina = 0;

        // ===== Tabla "Concepto/Monto" (E2:F4) — Ventas y Ganancia del periodo. =====
        $sheet->setCellValue('E2', 'Concepto');
        $sheet->setCellValue('F2', 'Monto');
        $sheet->setCellValue('E3', 'Ventas');
        $sheet->setCellValue('F3', $totalVenta);
        $sheet->setCellValue('E4', 'Ganancias');
        $sheet->setCellValue('F4', $totalGanancia);

        $tablaConcepto = new Table('E2:F4');
        $estiloConcepto = new TableStyle();
        $estiloConcepto->setTheme(TableStyle::TABLE_STYLE_DARK10);
        $estiloConcepto->setShowRowStripes(true);
        $tablaConcepto->setStyle($estiloConcepto);
        $sheet->addTable($tablaConcepto);

        $sheet->getStyle('F3:F4')->getNumberFormat()->setFormatCode('$#,##0.00');
        $sheet->getStyle('F3:F4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        // ===== Tabla "Cargos" (E6:F8) — Mantenimiento y Nómina del periodo. =====
        $sheet->setCellValue('E6', 'Cargos');
        $sheet->setCellValue('F6', 'Monto');
        $sheet->setCellValue('E7', 'Mantenimiento');
        $sheet->setCellValue('F7', $cargosMantenimientoPeriodo);
        $sheet->setCellValue('E8', 'Pagos nómina');
        $sheet->setCellValue('F8', $pagosNomina);

        $tablaCargos = new Table('E6:F8');
        $estiloCargos = new TableStyle();
        $estiloCargos->setTheme(TableStyle::TABLE_STYLE_DARK10);
        $estiloCargos->setShowRowStripes(true);
        $tablaCargos->setStyle($estiloCargos);
        $sheet->addTable($tablaCargos);

        $sheet->getStyle('F7:F8')->getNumberFormat()->setFormatCode('$#,##0.00');
        $sheet->getStyle('F7:F8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        foreach (['E', 'F'] as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // ===== Hoja "Dashboard": solo las gráficas, sin tablas de cortes ni tablas de datos
        // a la vista (los datos que alimentan las gráficas van en columnas ocultas). =====
        $hojaDashboard = $spreadsheet->createSheet();
        $hojaDashboard->setTitle('Dashboard');

        $hojaDashboard->setCellValue('B2', 'Hotel:');
        $hojaDashboard->setCellValue('C2', $Tienda);
        $hojaDashboard->setCellValue('B3', 'Periodo:');
        $hojaDashboard->setCellValue('C3', "$fiFormato - $ffFormato");

        $tablaInfoDashboard = new Table('B2:C3');
        $estiloInfoDashboard = new TableStyle();
        $estiloInfoDashboard->setTheme(TableStyle::TABLE_STYLE_DARK10);
        $estiloInfoDashboard->setShowRowStripes(true);
        $tablaInfoDashboard->setStyle($estiloInfoDashboard);
        $hojaDashboard->addTable($tablaInfoDashboard);

        $hojaDashboard->getStyle('C2:C3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        foreach (['B', 'C'] as $col) {
            $hojaDashboard->getColumnDimension($col)->setAutoSize(true);
        }

        // ===== Datos ocultos de la gráfica de barras "Consumo de Habitaciones" (una columna
        // por tipo de habitación, una fila por día o mes del periodo — ver
        // ControladorHabitaciones::crtObtenerVentasPorTipoHabitacionRango). Se colocan bien
        // lejos del área visible (a partir de la columna Z) y las columnas quedan ocultas. =====
        $etiquetasHab = $ConsumoHabitaciones['etiquetas'];
        $nombresHab = $ConsumoHabitaciones['habitaciones'];
        $datosHab = $ConsumoHabitaciones['montos'];
        $coloresHab = $ConsumoHabitaciones['colores'];

        // Quita del eje los días/meses donde ninguna habitación vendió nada (no solo la
        // barra en $0, sino la categoría completa).
        $indicesConVentaHab = [];
        foreach ($etiquetasHab as $j => $etiqueta) {
            $totalPeriodo = 0;
            foreach ($nombresHab as $i => $nombre) {
                $totalPeriodo += $datosHab[$i][$j] ?? 0;
            }
            if ($totalPeriodo > 0) {
                $indicesConVentaHab[] = $j;
            }
        }
        $etiquetasHab = array_values(array_map(fn($j) => $etiquetasHab[$j], $indicesConVentaHab));
        $datosHabConVenta = [];
        foreach ($nombresHab as $i => $nombre) {
            $datosHabConVenta[$i] = array_values(array_map(fn($j) => $datosHab[$i][$j] ?? 0, $indicesConVentaHab));
        }
        $datosHab = $datosHabConVenta;

        $colInicioOcultas = 26; // columna Z
        $filaDatosHab = 2;
        $hojaDashboard->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colInicioOcultas) . $filaDatosHab, 'Periodo');

        foreach ($nombresHab as $i => $nombre) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colInicioOcultas + 1 + $i);
            $hojaDashboard->setCellValue("{$col}{$filaDatosHab}", $nombre);
        }

        $filaDatosHabInicio = $filaDatosHab + 1;
        foreach ($etiquetasHab as $j => $etiqueta) {
            $filaActual = $filaDatosHabInicio + $j;
            $hojaDashboard->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colInicioOcultas) . $filaActual, $etiqueta);

            foreach ($nombresHab as $i => $nombre) {
                $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colInicioOcultas + 1 + $i);
                $valorHab = $datosHab[$i][$j] ?? 0;

                // Se deja la celda vacía (en vez de escribir 0) para que Excel no dibuje
                // ni la barra ni la etiqueta "$0" de una habitación sin venta ese día/mes.
                if ($valorHab != 0) {
                    $hojaDashboard->setCellValue("{$col}{$filaActual}", $valorHab);
                }
            }
        }
        $filaDatosHabFin = $filaDatosHabInicio + count($etiquetasHab) - 1;

        $colCategoriaHab = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colInicioOcultas);
        $ultimaColHabOculta = $colInicioOcultas + count($nombresHab);

        if (count($etiquetasHab) > 0 && count($nombresHab) > 0) {

            // ----- Gráfica de barras: una serie por tipo de habitación. -----
            $categorias = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'Dashboard'!\${$colCategoriaHab}\${$filaDatosHabInicio}:\${$colCategoriaHab}\${$filaDatosHabFin}", null, count($etiquetasHab));

            $etiquetasSeries = [];
            $valoresSeries = [];

            foreach ($nombresHab as $i => $nombre) {
                $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colInicioOcultas + 1 + $i);
                $etiquetasSeries[] = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'Dashboard'!\${$col}\${$filaDatosHab}", null, 1);

                $valoresSerie = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'Dashboard'!\${$col}\${$filaDatosHabInicio}:\${$col}\${$filaDatosHabFin}", null, count($etiquetasHab));
                $valoresSerie->setFillColor([ltrim($coloresHab[$i] ?? '999999', '#')]);

                $etiquetaValorHab = new Layout();
                $etiquetaValorHab->setShowVal(true);
                $etiquetaValorHab->setNumFmtCode('$#,##0');
                $etiquetaValorHab->setNumFmtLinked(false);
                $valoresSerie->setLabelLayout($etiquetaValorHab);

                $valoresSeries[] = $valoresSerie;
            }

            $serieHab = new DataSeries(
                DataSeries::TYPE_BARCHART,
                DataSeries::GROUPING_CLUSTERED,
                range(0, count($nombresHab) - 1),
                $etiquetasSeries,
                array_fill(0, count($nombresHab), $categorias),
                $valoresSeries
            );
            $serieHab->setPlotDirection(DataSeries::DIRECTION_COL);

            $plotAreaHab = new PlotArea(null, [$serieHab]);
            $legendHab = new Legend(Legend::POSITION_BOTTOM, null, false);
            $tituloHab = new Title('Ventas por Habitaciones');

            $graficaHab = new Chart('graficaConsumoHabitaciones', $tituloHab, $legendHab, $plotAreaHab);
            $graficaHab->setTopLeftPosition('B5');
            $graficaHab->setBottomRightPosition('Q27');
            $graficaHab->setPlotVisibleOnly(false);

            $hojaDashboard->addChart($graficaHab);
        }

        // ===== Datos ocultos de la gráfica de pastel: Ingresos (Ganancias) vs Egresos
        // (Mantenimiento + Nómina). Un poco más allá de los datos de habitaciones para no
        // pisarlos, sin importar cuántos tipos de habitación tenga el hotel. =====
        $colPastel1 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($ultimaColHabOculta + 2);
        $colPastel2 = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($ultimaColHabOculta + 3);

        $hojaDashboard->setCellValue("{$colPastel1}{$filaDatosHab}", 'Ingresos');
        $hojaDashboard->setCellValue("{$colPastel2}{$filaDatosHab}", 'Egresos');
        $hojaDashboard->setCellValue("{$colPastel1}" . ($filaDatosHab + 1), $totalGanancia);
        $hojaDashboard->setCellValue("{$colPastel2}" . ($filaDatosHab + 1), $cargosMantenimientoPeriodo + $pagosNomina);
        $hojaDashboard->getStyle("{$colPastel1}" . ($filaDatosHab + 1) . ":{$colPastel2}" . ($filaDatosHab + 1))->getNumberFormat()->setFormatCode('$#,##0.00');

        $etiquetasPastel = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'Dashboard'!\${$colPastel1}\${$filaDatosHab}:\${$colPastel2}\${$filaDatosHab}", null, 2);
        $valoresPastel = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'Dashboard'!\${$colPastel1}\$" . ($filaDatosHab + 1) . ":\${$colPastel2}\$" . ($filaDatosHab + 1), null, 2);
        $valoresPastel->setFillColor(['70AD47', 'C00000']);

        $etiquetasDatosPastel = new Layout();
        $etiquetasDatosPastel->setShowVal(true);
        $etiquetasDatosPastel->setShowCatName(true);
        $etiquetasDatosPastel->setShowPercent(true);
        $etiquetasDatosPastel->setNumFmtLinked(true);
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

        $graficaPastel = new Chart('graficaIngresosEgresosPeriodo', $tituloPastel, $legendPastel, $plotAreaPastel);
        $graficaPastel->setTopLeftPosition('K29');
        $graficaPastel->setBottomRightPosition('Q49');
        $graficaPastel->setPlotVisibleOnly(false);

        $hojaDashboard->addChart($graficaPastel);

        // Oculta todo el bloque de columnas usado como fuente de datos de las gráficas.
        for ($c = $colInicioOcultas; $c <= $ultimaColHabOculta + 3; $c++) {
            $hojaDashboard->getColumnDimension(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c))->setVisible(false);
        }

        // ===== Hoja "Reporte anual": montos por tipo de habitación y egresos, mes a mes,
        // del año en curso en el servidor (solo los meses que ya empezaron). Sirve de fuente
        // para la "Gráfica Anual" y la "Grafica Ganancias VS Egresos (Anual)" del Dashboard. =====
        $anioActual = (int) date('Y');
        $VentasAnuales = $idHotel !== null
            ? ControladorHabitaciones::crtObtenerVentasPorTipoHabitacionMensual($anioActual)
            : ["meses" => [], "habitaciones" => [], "colores" => [], "datos" => [], "montos" => []];

        // Egresos (mantenimiento + nómina) agrupados por mes, mismo año en curso.
        $egresosPorMes = [];
        if ($idHotel !== null) {
            $cortesAnio = ControladorVentas::crtObtenerCortesDiarios($idHotel, "$anioActual-01-01", "$anioActual-12-31");
            foreach ($cortesAnio as $corte) {
                $mesCorte = substr($corte['Fecha_Corte'], 0, 7);
                $egresosPorMes[$mesCorte] = ($egresosPorMes[$mesCorte] ?? 0) + (float) $corte['CargosMantenimiento'];
            }
        }

        $hojaAnual = $spreadsheet->createSheet();
        $hojaAnual->setTitle('Reporte anual');

        $mesesAnual = $VentasAnuales['meses'];
        $habitacionesAnual = $VentasAnuales['habitaciones'];
        $montosAnual = $VentasAnuales['montos'];
        $coloresAnual = $VentasAnuales['colores'];

        $nombresMesesAnual = [
            '01' => 'Ene', '02' => 'Feb', '03' => 'Mar', '04' => 'Abr', '05' => 'May', '06' => 'Jun',
            '07' => 'Jul', '08' => 'Ago', '09' => 'Sep', '10' => 'Oct', '11' => 'Nov', '12' => 'Dic',
        ];

        $hojaAnual->setCellValue('B2', 'Mes');
        foreach ($habitacionesAnual as $i => $nombre) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(3 + $i);
            $hojaAnual->setCellValue("{$col}2", $nombre);
        }
        $colEgresosAnual = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(3 + count($habitacionesAnual));
        $hojaAnual->setCellValue("{$colEgresosAnual}2", 'Egresos');

        $filaAnualInicio = 3;
        foreach ($mesesAnual as $j => $mes) {
            $fila = $filaAnualInicio + $j;
            [$anioMes, $numMes] = explode('-', $mes);
            $hojaAnual->setCellValue("B{$fila}", $nombresMesesAnual[$numMes] . ' ' . $anioMes);

            foreach ($habitacionesAnual as $i => $nombre) {
                $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(3 + $i);
                $hojaAnual->setCellValue("{$col}{$fila}", $montosAnual[$i][$j] ?? 0);
            }

            $hojaAnual->setCellValue("{$colEgresosAnual}{$fila}", $egresosPorMes[$mes] ?? 0);
        }
        $filaAnualFin = $filaAnualInicio + count($mesesAnual) - 1;

        if (count($mesesAnual) > 0) {
            $hojaAnual->getStyle("C{$filaAnualInicio}:{$colEgresosAnual}{$filaAnualFin}")->getNumberFormat()->setFormatCode('$#,##0.00');
            $hojaAnual->getStyle("B2:{$colEgresosAnual}2")->getFont()->setBold(true);

            // Fila de totales (suma de cada habitación y de egresos, a lo largo de todos
            // los meses mostrados).
            $filaTotalAnual = $filaAnualFin + 1;
            $hojaAnual->setCellValue("B{$filaTotalAnual}", 'Total');
            foreach ($habitacionesAnual as $i => $nombre) {
                $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(3 + $i);
                $hojaAnual->setCellValue("{$col}{$filaTotalAnual}", "=SUM({$col}{$filaAnualInicio}:{$col}{$filaAnualFin})");
            }
            $hojaAnual->setCellValue("{$colEgresosAnual}{$filaTotalAnual}", "=SUM({$colEgresosAnual}{$filaAnualInicio}:{$colEgresosAnual}{$filaAnualFin})");

            $hojaAnual->getStyle("C{$filaTotalAnual}:{$colEgresosAnual}{$filaTotalAnual}")->getNumberFormat()->setFormatCode('$#,##0.00');
            $hojaAnual->getStyle("B{$filaTotalAnual}:{$colEgresosAnual}{$filaTotalAnual}")->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
            $hojaAnual->getStyle("B{$filaTotalAnual}:{$colEgresosAnual}{$filaTotalAnual}")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('4472C4');

            // La fila de Total se deja FUERA del rango de la tabla (B2:{fin de datos}, sin
            // incluir esta fila): usar la fila de totales nativa de una tabla de Excel junto
            // con el autofiltro corrompe el archivo (Excel reporta "reparaciones" al abrirlo),
            // por eso el "Total" se arma como una fila normal con estilo, no como parte de la tabla.
            $tablaAnual = new Table("B2:{$colEgresosAnual}{$filaAnualFin}");
            $estiloAnual = new TableStyle();
            $estiloAnual->setTheme(TableStyle::TABLE_STYLE_MEDIUM9);
            $estiloAnual->setShowRowStripes(true);
            $tablaAnual->setStyle($estiloAnual);
            $hojaAnual->addTable($tablaAnual);

            foreach (range('B', $colEgresosAnual) as $col) {
                $hojaAnual->getColumnDimension($col)->setAutoSize(true);
            }
        }

        // ===== "Gráfica Anual" (Dashboard): línea con marcadores, una serie por tipo de
        // habitación, mostrando el monto vendido cada mes del año en curso. =====
        if (count($mesesAnual) > 0 && count($habitacionesAnual) > 0) {
            $categoriasAnual = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'Reporte anual'!\$B\${$filaAnualInicio}:\$B\${$filaAnualFin}", null, count($mesesAnual));

            $etiquetasSeriesAnual = [];
            $valoresSeriesAnual = [];

            foreach ($habitacionesAnual as $i => $nombre) {
                $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(3 + $i);
                $etiquetasSeriesAnual[] = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'Reporte anual'!\${$col}\$2", null, 1);

                $valorSerieAnual = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'Reporte anual'!\${$col}\${$filaAnualInicio}:\${$col}\${$filaAnualFin}", null, count($mesesAnual));
                $valorSerieAnual->setFillColor([ltrim($coloresAnual[$i] ?? '999999', '#')]);
                $valorSerieAnual->setPointMarker('circle');

                $etiquetaValorAnual = new Layout();
                $etiquetaValorAnual->setShowVal(true);
                $etiquetaValorAnual->setNumFmtCode('$#,##0');
                $etiquetaValorAnual->setNumFmtLinked(false);
                $valorSerieAnual->setLabelLayout($etiquetaValorAnual);

                $valoresSeriesAnual[] = $valorSerieAnual;
            }

            $serieAnual = new DataSeries(
                DataSeries::TYPE_LINECHART,
                null,
                range(0, count($habitacionesAnual) - 1),
                $etiquetasSeriesAnual,
                array_fill(0, count($habitacionesAnual), $categoriasAnual),
                $valoresSeriesAnual
            );

            $plotAreaAnual = new PlotArea(null, [$serieAnual]);
            $legendAnual = new Legend(Legend::POSITION_BOTTOM, null, false);
            $tituloAnual = new Title('Gráfica Anual');

            $graficaAnual = new Chart('graficaAnualHabitaciones', $tituloAnual, $legendAnual, $plotAreaAnual);
            $graficaAnual->setTopLeftPosition('B29');
            $graficaAnual->setBottomRightPosition('J49');
            $graficaAnual->setPlotVisibleOnly(false);

            $hojaDashboard->addChart($graficaAnual);
        }

        // ===== "Grafica Ganancias VS Egresos (Anual)" (Dashboard): combinado de columnas
        // agrupadas (venta mensual por tipo de habitación) + línea (egresos mensuales). =====
        if (count($mesesAnual) > 0 && count($habitacionesAnual) > 0) {
            $categoriasCombo = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'Reporte anual'!\$B\${$filaAnualInicio}:\$B\${$filaAnualFin}", null, count($mesesAnual));

            $etiquetasBarrasCombo = [];
            $valoresBarrasCombo = [];

            foreach ($habitacionesAnual as $i => $nombre) {
                $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(3 + $i);
                $etiquetasBarrasCombo[] = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'Reporte anual'!\${$col}\$2", null, 1);

                $valorBarraCombo = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'Reporte anual'!\${$col}\${$filaAnualInicio}:\${$col}\${$filaAnualFin}", null, count($mesesAnual));
                $valorBarraCombo->setFillColor([ltrim($coloresAnual[$i] ?? '999999', '#')]);

                $etiquetaBarraCombo = new Layout();
                $etiquetaBarraCombo->setShowVal(true);
                $etiquetaBarraCombo->setNumFmtCode('$#,##0');
                $etiquetaBarraCombo->setNumFmtLinked(false);
                $valorBarraCombo->setLabelLayout($etiquetaBarraCombo);

                $valoresBarrasCombo[] = $valorBarraCombo;
            }

            $serieBarrasCombo = new DataSeries(
                DataSeries::TYPE_BARCHART,
                DataSeries::GROUPING_CLUSTERED,
                range(0, count($habitacionesAnual) - 1),
                $etiquetasBarrasCombo,
                array_fill(0, count($habitacionesAnual), $categoriasCombo),
                $valoresBarrasCombo
            );
            $serieBarrasCombo->setPlotDirection(DataSeries::DIRECTION_COL);

            $etiquetaEgresosCombo = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, "'Reporte anual'!\${$colEgresosAnual}\$2", null, 1);
            $valorEgresosCombo = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, "'Reporte anual'!\${$colEgresosAnual}\${$filaAnualInicio}:\${$colEgresosAnual}\${$filaAnualFin}", null, count($mesesAnual));
            $valorEgresosCombo->setFillColor(['C00000']);
            $valorEgresosCombo->setPointMarker('circle');

            $etiquetaEgresosValorCombo = new Layout();
            $etiquetaEgresosValorCombo->setShowVal(true);
            $etiquetaEgresosValorCombo->setNumFmtCode('$#,##0');
            $etiquetaEgresosValorCombo->setNumFmtLinked(false);
            $valorEgresosCombo->setLabelLayout($etiquetaEgresosValorCombo);

            $serieLineaCombo = new DataSeries(
                DataSeries::TYPE_LINECHART,
                null,
                [0],
                [$etiquetaEgresosCombo],
                [$categoriasCombo],
                [$valorEgresosCombo]
            );

            $plotAreaCombo = new PlotArea(null, [$serieBarrasCombo, $serieLineaCombo]);
            $legendCombo = new Legend(Legend::POSITION_BOTTOM, null, false);
            $tituloCombo = new Title('Grafica Ganancias VS Egresos (Anual)');

            $graficaCombo = new Chart('graficaGananciasEgresosAnual', $tituloCombo, $legendCombo, $plotAreaCombo);
            $graficaCombo->setTopLeftPosition('B52');
            $graficaCombo->setBottomRightPosition('Q72');
            $graficaCombo->setPlotVisibleOnly(false);

            $hojaDashboard->addChart($graficaCombo);
        }

        // La hoja de ventas se queda como la activa al abrir el archivo.
        $spreadsheet->setActiveSheetIndex(0);

        $nombreArchivo = 'ReporteVentas_' . date('Ymd_His') . '.xlsx';
        $rutaArchivo = $_SERVER['DOCUMENT_ROOT'] . "/sistema.posdit.com.mx/reportes/" . $nombreArchivo;

        $dirReportes = dirname($rutaArchivo);
        if (!is_dir($dirReportes)) {
            mkdir($dirReportes, 0755, true);
        }

        $writer = new Xlsx($spreadsheet);
        $writer->setIncludeCharts(true);
        $writer->save($rutaArchivo);

        $Prefijo = 52;
        $Celular = $Prefijo . str_replace([' ', '(', ')', '-'], '', $telefono);

        $urlArchivo = "https://posdit.com.mx/sistema.posdit.com.mx/reportes/" . $nombreArchivo;

        $apiUrl = 'https://apiwsp.factiliza.com/api/v1/message/sendMedia/NTI1NTI1MzI3MzA0';
        $token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiI2MjciLCJuYW1lIjoiSnVhbiBEYXZpZCBBZ3VpbGFyIEJhcnJvbiAiLCJlbWFpbCI6ImFndWlsYXJiYXJyb25qdWFuZGF2aWRAZ21haWwuY29tIiwiaHR0cDovL3NjaGVtYXMubWljcm9zb2Z0LmNvbS93cy8yMDA4LzA2L2lkZW50aXR5L2NsYWltcy9yb2xlIjoiY29uc3VsdG9yIn0.r5cvSNgCntPbf4OCjqx1JlS885CxHSN7FyxCLlVBAus';
        $data = array(
            "number" => $Celular,
            "mediatype" => "document",
            "media" => $urlArchivo,
            "filename" => $nombreArchivo,
            "caption" => "Reporte de Ventas: $" . number_format($totalVenta, 2) . ", Ganancias: $" . number_format($totalGanancia, 2) . " (" . $fiFormato . " - " . $ffFormato . ")"
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

$Reporte = new reporteVentasExcel();
$Reporte->CodigoUsuario = $_GET["Cu"];
$Reporte->FechaInicio = $_GET["fi"];
$Reporte->FechaFin = $_GET["ff"];
$Reporte->generar();

//============================================================+
// END OF FILE
//============================================================+
