<?php

require_once "../../../controllers/inventarios.controlador.php";
require_once "../../../models/inventarios.modelo.php";
require_once "../../../controllers/hoteles.controlador.php";
require_once "../../../models/hoteles.modelo.php";

class imprimirReporteInventario{

Public $CodigoUsuario;

Public function traerImpresionReporeInventario(){

$idusuario = $this->CodigoUsuario;
$Inventarios = ControladorInventarios::crtObtenerInventarios($idusuario);
$Negocio = ControladorHoteles::crtObtenerNegocioUsuarioReporte($idusuario);

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
			<td style="width:90px"><img src="images/logo.png">
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
					Reporte de Inventario
								
				</div>
				
			</td>

			
		</tr>
</table>

<div style="font-size:6px; text-align:center; "></div>

<style>
    th {
        border: 2px solid #2E86C1;
        background-color: Black;
    } 
</style>

		<table border="1" style="font-size:8.5px; color:white">
             <thead>
               <tr>
                <th style="width:70px">Código Barras</th>  
                <th>Categoría</th>
                <th>Marca</th>
                <th style="width:75px">Sub-Marca</th>
                <th>Producto</th>
                <th style="width:90px">Clasificación</th>
                <th style="width:40px">Gramaje</th>
                <th style="width:27px">Stock</th>
                </tr>
             </thead>
     	</table> 
EOF;

// print a block of text using Write()
$pdf->writeHTML($Contenido, false, false, false, false, '');

foreach ($Inventarios as $key => $value) {

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
            <td style="width:70px">$value[Id_Producto]</td>
            <td>$value[Categoria]</td>
            <td>$value[Marca]</td>
            <td style="width:75px">$value[SubMarca]</td>
            <td>$value[Producto]</td>
            <td style="width:90px">$value[Clasificacion]</td>
            <td style="width:40px">$value[Gramaje]</td>
            <td style="width:27px">$value[Stock]</td>
        </tr>
    </tbody>
 </table> 

EOF;

// print a block of text using Write()
$pdf->writeHTML($Complemento, false, false, false, false, '');

}

//Close and output PDF document
$pdf->Output('ReporteInventario.pdf', 'I');

	}
}

$ReporteInventario = new imprimirReporteInventario();
$ReporteInventario -> CodigoUsuario = $_GET["Cu"];
$ReporteInventario -> traerImpresionReporeInventario();	


//============================================================+
// END OF FILE
//============================================================+


