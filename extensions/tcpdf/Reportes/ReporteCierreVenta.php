<?php

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

        // Crear el archivo Excel
        $writer = new Xlsx($spreadsheet);

        // Guardar el archivo en la ruta definida
        $writer->save($rutaArchivo);

        $RutaEnvio = "https://posdit.com.mx/sistema.posdit.com.mx/reportes/" . $nombreArchivo;

        // Devolver la ruta del archivo para usarla en la API de WhatsApp
        echo json_encode(['filePath' => $RutaEnvio]);

    }
}


// Crear y ejecutar el reporte
$ReporteInventario = new generaReporteDia();
$ReporteInventario->CodigoUsuario = $_GET["Cu"];
$ReporteInventario->MontoCierre = $_GET["mCierre"];
$ReporteInventario->MontoCaja = $_GET["mCaja"];
$ReporteInventario->traerReporteCierreDia();
