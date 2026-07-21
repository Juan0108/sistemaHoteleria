<?php

require_once "../../../controllers/ganancias.controlador.php";
require_once "../../../models/ganancias.modelo.php";
require_once "../../../controllers/negocios.controlador.php";
require_once "../../../models/negocios.modelo.php";

class imprimirReporteGanancias{

Public $CodigoUsuario;

Public function traerImpresionReporteGanancia(){

$idusuario = $this->CodigoUsuario;
$ganancias = ControladorGanancias::crtObtenerGanancias($idusuario);
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
	$pdf->SetTitle('Reporte ReporteInventario');
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
			<td style="width:130px"><img src="images/logo.png">
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

$Contenido = <<<EOF


<table>
		<tr>
			
			<td style="background-color:white";>

				<div style="font-size:18px; text-align:center; color:gray">
					<br>
					Reporte de Inversión VS Ganancia
								
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
           <th align="center">Marca</th>
           <th align="center">SubMarca</th>
           <th align="center" style="width: 80px;">Clasificación</th>
           <th align="center" style="width: 40px;">Stock</th>
           <th align="center">Precio Compra</th>
           <th align="center">Compra Total</th>
           <th align="center">Precio Venta</th>
           <th align="center">Venta Total</th>
           <th align="center">Ganancia</th>
        </tr>
    </thead>
</table> 
EOF;

// print a block of text using Write()
$pdf->writeHTML($Contenido, false, false, false, false, '');

$superCompra=0;
$superVenta=0;
$superGanancia=0;

foreach ($ganancias as $key => $value) {

$totalCompra=$value['PrecioCompra']*$value['Stock'];
$totalVenta=$value['PrecioVenta']*$value['Stock'];
$total=$totalVenta-$totalCompra;

$superCompra=$superCompra+$totalCompra;
$superVenta=$superVenta+$totalVenta;
$superGanancia=$superGanancia+$total;

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


            <td>$value[Marca]</td>
            <td>$value[Submarca]</td>
            <td style="width: 80px;">$value[Clasificacion]</td>
            <td align="right" style="width: 40px;">$value[Stock]</td>
            <td align="right">$ $value[PrecioCompra]</td>
            <td align="right">$ $totalCompra</td>
            <td align="right">$ $value[PrecioVenta]</td>
            <td align="right">$ $totalVenta</td>
            <td align="right">$ $total</td>
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
					<td>Inversión</td>
					<td align="right">$ $superCompra</td>
				</tr>
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

$ReporteGanancias = new imprimirReporteGanancias();
$ReporteGanancias -> CodigoUsuario = $_GET["Cu"];
$ReporteGanancias -> traerImpresionReporteGanancia();	


//============================================================+
// END OF FILE
//============================================================+


