<?php
session_start();
require_once "../../../models/usuarios.modelo.php";
require_once "../../../models/conexion.php";

// Validar sesión activa y parámetro ID por GET
if (!isset($_SESSION["IniciarSesion"]) || $_SESSION["IniciarSesion"] != "ok" || !isset($_GET["id"])) {
    echo "Acceso no autorizado.";
    exit;
}

$id_usuario = $_GET["id"];

// Consultar mediante el controlador y modelo establecidos
$usuario = ModeloUsuarios::MdlObtenerUsuario($id_usuario);

if (!$usuario) {
    echo "Usuario no encontrado.";
    exit;
}

// Incluir la librería TCPDF usando la ruta estándar del proyecto
require_once('tcpdf_include.php');

// Crear instancia TCPDF: Orientación Vertical (P), Unidades en mm, Tamaño de credencial (54 x 85.6 mm)
$pdf = new TCPDF('P', PDF_UNIT, array(54, 85.6), true, 'UTF-8', false);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Sistema PosDit');
$pdf->SetTitle('Gafete - ' . $usuario["Nombre"]);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetMargins(4, 4, 4);
$pdf->SetAutoPageBreak(false, 0);

$pdf->AddPage();

// --- DISEÑO Y ESTILOS DEL GAFETE ---
// Fondo general color crema (#f6ecdb)
$pdf->SetFillColor(246, 236, 219);
$pdf->Rect(0, 0, 54, 85.6, 'F');

// Franja superior corporativa (#3f342e)
$pdf->SetFillColor(63, 52, 46);
$pdf->Rect(0, 0, 54, 20, 'F');

// Razón Social / Empresa
$pdf->SetTextColor(246, 236, 219);
$pdf->SetFont('helvetica', 'B', 9);
$pdf->SetXY(2, 3, 50);
$pdf->Cell(50, 5, strtoupper($usuario["Razon_Social"] ? $usuario["Razon_Social"] : "SISTEMA POSDIT"), 0, 1, 'C');

// --- CÓDIGO QR EN LA PARTE SUPERIOR ---
$styleQR = array(
    'border' => 0,
    'vpadding' => 0,
    'hpadding' => 0,
    'fgcolor' => array(63, 52, 46),
    'bgcolor' => array(255, 255, 255),
    'module_width' => 1,
    'module_height' => 1
);
$pdf->write2DBarcode($usuario["id_usuario"], 'QRCODE,L', 19, 9, 16, 16, $styleQR, 'N');

// --- FOTO DEL USUARIO ---
/* $rutaFoto = "../../../" . $usuario["foto"];
if (!file_exists($rutaFoto) || empty($usuario["foto"])) {
    $rutaFoto = "../../../views/img/Users/default.png";
}

if (file_exists($rutaFoto)) {
    $pdf->Image($rutaFoto, 17, 28, 20, 20, '', '', '', true, 300, '', false, false, 0, 'CM', false, false);
} */

// --- DATOS DEL USUARIO ---
// Nombre completo
$pdf->SetTextColor(63, 52, 46);
$pdf->SetFont('helvetica', 'B', 9.5);
$pdf->SetXY(2, 50, 50);
$pdf->Cell(50, 5, utf8_decode($usuario["Nombre"] . " " . $usuario["Apaterno"] . " " . $usuario["Amaterno"]), 0, 1, 'C');

// Perfil / Rol (Color rust #b96a37)
$pdf->SetTextColor(185, 106, 55);
$pdf->SetFont('helvetica', 'B', 8);
$pdf->SetXY(2, 56, 50);
$pdf->Cell(50, 4, utf8_decode($usuario["NombrePerfil"]), 0, 1, 'C');

// Datos generales
$pdf->SetTextColor(100, 100, 100);
$pdf->SetFont('helvetica', '', 6.5);
$pdf->SetXY(2, 62, 50);
$pdf->Cell(50, 3, utf8_decode("Usuario: " . $usuario["Usuario"]), 0, 1, 'C');
$pdf->SetXY(2, 66, 50);
$pdf->Cell(50, 3, utf8_decode("Correo: " . $usuario["Correo"]), 0, 1, 'C');
$pdf->SetXY(2, 70, 50);
$pdf->Cell(50, 3, utf8_decode("Dirección: " . $usuario["Calle"]), 0, 1, 'C');

// Pie de credencial
$pdf->SetTextColor(129, 65, 45);
$pdf->SetFont('helvetica', 'I', 6);
$pdf->SetXY(2, 78, 50);
$pdf->Cell(50, 3, "Expedicion: " . date('d/m/Y') . " | ID: " . $usuario["id_usuario"], 0, 1, 'C');

// Salida del PDF para descarga / visualización directa en navegador
$pdf->Output('Gafete_' . $usuario["Nombre"] . " " . $usuario["Apaterno"] . " " . '.pdf', 'I');
exit;
?>