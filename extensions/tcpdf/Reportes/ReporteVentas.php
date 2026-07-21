<?php

require_once "../../../controllers/ventas.controlador.php";
require_once "../../../models/ventas.modelo.php";
require_once "../../../controllers/negocios.controlador.php";
require_once "../../../models/negocios.modelo.php";

class imprimirReporteVentas{

Public $CodigoUsuario;
Public $FechaInicio;
Public $FechaFin;

Public function traerImpresionReporteVentas(){

$idusuario = $this->CodigoUsuario;
$fi = $this->FechaInicio;
$ff = $this->FechaFin;
$fiFormat=date("d-m-Y", strtotime($fi));
$ffFormat=date("d-m-Y", strtotime($ff));
$Inventarios = ControladorVentas::crtObtenerVentasFecha($idusuario, $fi, $ff);
$Negocio = ControladorNegocios::crtObtenerNegocioUsuarioReporte($idusuario);

$Tienda = $Negocio[0]["Razon_Social"];
$Estado = $Negocio[0]["Estado"];
$Municipio = $Negocio[0]["Municipio"];
$Colonia = $Negocio[0]["Colonia"];
$Calle = $Negocio[0]["Calle"];
$Telefono = $Negocio[0]["Telefono"];
$Correo = $Negocio[0]["Correo"];
$Fecha = date('d/m/Y');

 
// Include the main TCPDF library (search for installation path).
require_once('tcpdf_include.php');


// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor('David Aguilar');
	$pdf->SetTitle('Reporte ReporteVentas');
	$pdf->setPrintHeader(false); 
	$pdf->SetMargins(20, 20, 20, false); 
	$pdf->SetAutoPageBreak(true, 20); 
	$pdf->SetFont('Helvetica', '', 10);
	$pdf->setFooterData(array(0,64,0), array(0,64,128));

	$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// add a page
$pdf->AddPage();

// set some text to print
$Cabecera = <<<EOF

	<table>		
		<tr>			
			<td style="width:110px"><img src="images/logo.png">
			</td>
		</tr>
		<tr>
			<td style="background-color:white; width:180px">
				
				<div style="font-size:8.5px; text-align:left; line-height:15px;">
					
					<br>
						Estado: $Estado

					<br>
						Municipio: $Municipio

					<br>
						Colonia: $Colonia

				</div>

			</td>

			<td style="background-color:white; width:180px">

				<div style="font-size:8.5px; text-align:left; line-height:15px;">

					<br>
					Calle: $Calle
					
					<br>
					Teléfono: $Telefono
					
					<br>
					Correo: $Correo

				</div>
				
			</td>

			<td style="background-color:white; width:110px; text-align:center; color:red"><br>
			<br>
			Razon Social:
			
			<br>
			$Tienda

			<br style="font-size:8.5x; text-align:left; line-height:17px; color:black">
			Fecha Consulta: $Fecha
			</td>

		</tr>

	</table>

EOF;

// print a block of text using Write()
$pdf->writeHTML($Cabecera, false, false, false, false, '');

// ---------------------------------------------------------

$Rango = <<<EOF

<style>
    td {
        border: 2px solid #2E86C1;
        background-color: #F4F6F6;
    } 
</style>

<div style="font-size:6px; text-align:center; "></div>

<table style="width: 175px;">
<tbody>
	<tr>
		<td colspan="2" align="center";>Periodo de Consulta</td>
	</tr>
	<tr>
		<td>Fecha Inicio</td>
		<td>Fecha Fin</td>
	</tr>
	<tr>
		<td>$fiFormat</td>
		<td>$ffFormat</td>
	</tr>
</tbody>
</table>

EOF;

// print a block of text using Write()
$pdf->writeHTML($Rango, false, false, false, false, '');

$Contenido = <<<EOF


<table>
		<tr>
			
			<td style="background-color:white";>

				<div style="font-size:18px; text-align:center; color:gray">
					<br>
					Reporte de Ventas
								
				</div>
				
			</td>
			
		</tr>
</table>
 

<div style="font-size:6px; text-align:center; "></div>

<style>
    th {
        border: 2px solid #2E86C1;
        background-color: #2E86C1;
    } 
</style>

<table border="1" style="font-size: 8.5px; color: black; font-weight: bold;">
    <thead>
        <tr>  
		   <th align="center" style="width: 74px;">Fecha Compra</th>
           <th align="center">Vendedor</th>
           <th align="center">Sub-Marca</th>
           <th align="center">Producto</th>
           <th align="center" style="width: 43px;">Cant.</th>
           <th align="center" style="width: 70px;">Prec. Compra</th>
           <th align="center">Prec. Venta</th>
           <th align="center">Ganancias</th>
           <th align="center" style="width: 40px;">Total</th>
        </tr>
    </thead>
</table> 
EOF;

// print a block of text using Write()
$pdf->writeHTML($Contenido, false, false, false, false, '');

$superVenta=0;
$superGanancia=0;

foreach ($Inventarios as $key => $value) {

$ganancia=($value['PrecioVenta']-$value['PrecioCompra'])*$value['Cantidad'];
$total=$value['PrecioVenta']*$value['Cantidad'];

$superVenta=$superVenta+$total;
$superGanancia=$superGanancia+$ganancia;

$Complemento = <<<EOF

<style>
    td {
        border: 2px solid #2E86C1;
        background-color: #F4F6F6;
    } 
</style>

<table border="1" style="font-size:8.5px;">
    <tbody>
        <tr>
			<td style="width: 74px;">$value[Fecha_Compra]</td>
            <td>$value[Vendedor]</td>
            <td>$value[Submarca]</td>
            <td>$value[Producto]</td>
            <td align="right" style="width: 43px;">$value[Cantidad]</td>
            <td align="right" style="width: 70px;">$ $value[PrecioCompra]</td>
            <td align="right">$ $value[PrecioVenta]</td>
            <td align="right">$ $ganancia</td>
            <td align="right" style="width: 40px;">$ $total</td>
        </tr>
    </tbody>
 </table> 

EOF;

// print a block of text using Write()
$pdf->writeHTML($Complemento, false, false, false, false, '');

}


$Totales = <<<EOF

<style>
    td {
        border: 2px solid #2E86C1;
        background-color: #F4F6F6;
    } 
</style>

<div style="font-size:6px; text-align:center; "></div>
<table style="border: none;">
<tbody>
	<tr>
		<td style="width: 66%; border: none;"></td>
		<td style="border: none;">
			<table style="width: 175px;">
			<tbody>
				<tr>
					<td>Ventas</td>
					<td align="right">$ $superVenta</td>
				</tr>
				<tr>
					<td>Ganancias</td>
					<td align="right">$ $superGanancia</td>
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

//Close and output PDF document
$pdf->Output('ReporteVentas.pdf', 'I');

	}
}

$ReporteInventario = new imprimirReporteVentas();
$ReporteInventario -> CodigoUsuario = $_GET["Cu"];
$ReporteInventario -> FechaInicio = $_GET["fi"];
$ReporteInventario -> FechaFin = $_GET["ff"];
$ReporteInventario -> traerImpresionReporteVentas();	


//============================================================+
// END OF FILE
//============================================================+


