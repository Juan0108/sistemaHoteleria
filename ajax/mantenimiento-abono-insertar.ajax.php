<?php

session_start();
header('Content-Type: application/json');

require_once "../controllers/mantenimiento.controlador.php";
require_once "../models/mantenimiento.modelo.php";

class InsertarAbonoAjax
{
	public function ajaxInsertar()
	{
		$id_mantenimiento = isset($_POST["idMantenimiento"]) ? (int) $_POST["idMantenimiento"] : 0;
		$monto = $_POST["monto"] ?? "";
		$archivoFoto = $_FILES["fotoAbono"] ?? null;

		$respuesta = ControladorMantenimiento::crtInsertarAbono($id_mantenimiento, $monto, $archivoFoto);

		if($respuesta["status"] !== "success"){
			http_response_code(400);
		}

		echo json_encode($respuesta);
	}
}

if(!empty($_POST)){
	$ajax = new InsertarAbonoAjax();
	$ajax->ajaxInsertar();
}else{
	http_response_code(400);
	echo json_encode([
		"status" => "error",
		"message" => "Faltan datos para registrar el abono"
	]);
}
