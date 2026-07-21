<?php

require_once "conexion.php";

/**
 * 
 */
class ModeloInventarios{


/**
Obtener Inventarios
 */
static public function MdlObtenerInventarios($idusuario){

	$stmt = Conexion::conectar()->prepare("CALL ObtenerInventarios($idusuario)");
	$stmt -> execute();
	return $stmt -> fetchAll();

}

/**
Obtener Inventarios
 */
static public function MdlObtenerStocksInventarios($valor){

	$stmt = Conexion::conectar()->prepare("CALL ObtenerStocksInventarios('$valor')");
	$stmt -> execute();
	return $stmt -> fetch();

}

/**
Obtener Inventario
 */
static public function MdlObtenerInventario($idNegocio,$valor){

	$stmt = Conexion::conectar()->prepare("CALL ObtenerInventario('$idNegocio','$valor')");
	$stmt -> execute();
	return $stmt -> fetch();

}

/**
Insertar Inventario
 */

static public function MdlInsertarInventario($inventario){

	$stmt = Conexion::conectar()->prepare("CALL InsertarInventario('$inventario->id_producto','$inventario->stock','$inventario->preciocompra','$inventario->precioventa','$inventario->id_negocio','$inventario->id_usuario')");

	if($stmt->execute()){
		return "ok";

	}else{
		return "error";
	}
}

/**
Actualizar Inventario
 */

static public function MdlActualizarInventario($inventario){

	$stmt = Conexion::conectar()->prepare("CALL ActualizarInventario('$inventario->id_inventario','$inventario->stock','$inventario->preciocompra','$inventario->precioventa','$inventario->id_usuario')");

	if($stmt->execute()){
		return "ok";

	}else{
		return "error";
	}
}

}

