<?php
session_start();
require_once "../../../models/conexion.php";
require_once "../../../models/usuarios.modelo.php";

// Validar sesión activa y parámetro por GET
if (!isset($_SESSION["IniciarSesion"]) || $_SESSION["IniciarSesion"] != "ok" || !isset($_GET["id"])) {
    echo "Acceso no autorizado.";
    exit;
}

$valor = $_GET["id"]; // Nombre de usuario en texto enviado desde el JS

// Obtener la información del usuario usando tu Store Procedure principal
$usuario = ModeloUsuarios::MdlObtenerUsuario($valor);

if (!$usuario) {
    echo "Usuario no encontrado.";
    exit;
}

// Obtener los nombres usando los Stored Procedures de tus catálogos a través del modelo
$nombreEstado = !empty($usuario["IdEstado"]) ? ModeloUsuarios::MdlObtenerNombreEstadoPorId($usuario["IdEstado"]) : "";
$nombreMunicipio = (!empty($usuario["IdEstado"]) && !empty($usuario["IdMunicipio"])) ? ModeloUsuarios::MdlObtenerNombreMunicipioPorId($usuario["IdEstado"], $usuario["IdMunicipio"]) : "";
$nombreColonia = !empty($usuario["IdMunicipio"]) ? ModeloUsuarios::MdlObtenerNombreColoniaPorId($usuario["IdMunicipio"], $usuario["IdColonia"]) : "";

// Función robusta para normalizar cualquier texto a UTF-8 válido para TCPDF
function limpiarTexto($cadena) {
    if (empty($cadena)) return "";
    $convertido = @mb_convert_encoding($cadena, 'UTF-8', 'UTF-8, ISO-8859-1');
    return $convertido !== false ? $convertido : "";
}

// Incluir la librería TCPDF usando el include estándar del proyecto
require_once('tcpdf_include.php');

// Crear instancia TCPDF: Orientación Vertical (P), Unidades en mm, Tamaño de credencial (54 x 85.6 mm)
$pdf = new TCPDF('P', PDF_UNIT, array(54, 85.6), true, 'UTF-8', false);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Sistema PosDit');
$pdf->SetTitle('Gafete - ' . ($usuario["Nombre"] ?? 'Usuario'));
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
$razonSocial = isset($usuario["Razon_Social"]) ? $usuario["Razon_Social"] : ($_SESSION["RazonSocial"] ?? "SISTEMA POSDIT");
$pdf->SetTextColor(246, 236, 219);
$pdf->SetFont('helvetica', 'B', 9);
$pdf->SetXY(2, 3, 50);
$pdf->Cell(50, 5, limpiarTexto(strtoupper($razonSocial)), 0, 1, 'C');

// --- CÓDIGO QR EN LA PARTE SUPERIOR CON FONDO BLANCO Y BORDE ---
$pdf->SetFillColor(255, 255, 255);
$pdf->Rect(17, 8.5, 20, 20, 'F');

$styleQR = array(
    'border' => true, // Borde para garantizar la lectura correcta del lector
    'vpadding' => 1,
    'hpadding' => 1,
    'fgcolor' => array(0, 0, 0),       // Negro puro para contraste óptimo
    'bgcolor' => array(255, 255, 255), // Fondo blanco
    'module_width' => 1,
    'module_height' => 1
);

$idUsuarioQR = isset($usuario["id_usuario"]) ? $usuario["id_usuario"] : "0";
$pdf->write2DBarcode($idUsuarioQR, 'QRCODE,L', 17, 8.5, 20, 20, $styleQR, 'N');

// --- FOTO DEL USUARIO ---
$fotoPath = isset($usuario["foto"]) ? $usuario["foto"] : "";
$rutaFoto = "../../../" . $fotoPath;
if (!file_exists($rutaFoto) || empty($fotoPath)) {
    $rutaFoto = "../../../views/img/Users/default.png";
}

if (file_exists($rutaFoto)) {
    $pdf->Image($rutaFoto, 17, 29, 20, 20, '', '', '', true, 300, '', false, false, 0, 'CM', false, false);
}

// --- DATOS DEL USUARIO ---
$nombre = isset($usuario["Nombre"]) ? $usuario["Nombre"] : "";
$apaterno = isset($usuario["Apaterno"]) ? $usuario["Apaterno"] : "";
$amaterno = isset($usuario["Amaterno"]) ? $usuario["Amaterno"] : "";
$nombreCompleto = trim($nombre . " " . $apaterno . " " . $amaterno);

$pdf->SetTextColor(63, 52, 46);
$pdf->SetFont('helvetica', 'B', 9);
$pdf->SetXY(2, 50, 50);
$pdf->Cell(50, 4, limpiarTexto($nombreCompleto), 0, 1, 'C');

// Perfil / Rol (Color rust #b96a37)
$nombrePerfil = isset($usuario["NombrePerfil"]) ? $usuario["NombrePerfil"] : "Colaborador";
$pdf->SetTextColor(185, 106, 55);
$pdf->SetFont('helvetica', 'B', 7.5);
$pdf->SetXY(2, 55, 50);
$pdf->Cell(50, 3.5, limpiarTexto($nombrePerfil), 0, 1, 'C');

// Datos generales (Usuario y Correo)
$userLogin = isset($usuario["usuario"]) ? $usuario["usuario"] : "";
$userCorreo = isset($usuario["Correo"]) ? $usuario["Correo"] : "";

$pdf->SetTextColor(100, 100, 100);
$pdf->SetFont('helvetica', '', 6);
$pdf->SetXY(2, 59, 50);
$pdf->Cell(50, 3, limpiarTexto("Usuario: " . $userLogin), 0, 1, 'C');
$pdf->SetXY(2, 62.5, 50);
$pdf->Cell(50, 3, limpiarTexto("Correo: " . $userCorreo), 0, 1, 'C');

// --- DIRECCIÓN COMPLETA ---
$calle = isset($usuario["Calle"]) ? $usuario["Calle"] : "";
$cp = isset($usuario["Codigo_Postal"]) ? $usuario["Codigo_Postal"] : "";

$partesDireccion = [];
if (!empty($calle)) $partesDireccion[] = $calle;
if (!empty($nombreColonia)) $partesDireccion[] = "Col. " . $nombreColonia;
if (!empty($nombreMunicipio)) $partesDireccion[] = $nombreMunicipio;
if (!empty($nombreEstado)) $partesDireccion[] = $nombreEstado;
if (!empty($cp)) $partesDireccion[] = "C.P. " . $cp;

$direccionCompleta = implode(", ", $partesDireccion);

$pdf->SetFont('helvetica', '', 5.5);
$pdf->SetXY(2, 67, 50);
$pdf->MultiCell(50, 8, limpiarTexto($direccionCompleta), 0, 'C', false, 1, '', '', true);

// Pie de credencial
$pdf->SetTextColor(129, 65, 45);
$pdf->SetFont('helvetica', 'I', 5.5);
$pdf->SetXY(2, 79, 50);
$pdf->Cell(50, 3, limpiarTexto("Expedicion: " . date('d/m/Y') . " | ID: " . $idUsuarioQR), 0, 1, 'C');

// Salida del PDF para descarga directa en el navegador
$pdf->Output('Gafete_' . $userLogin . '.pdf', 'I');
exit;
?>