<?php

require_once "conexion.php";

/**
 * 
 */
class ModeloProductos{


/**
Obtener Productos
 */
static public function MdlObtenerProductos(){

	$stmt = Conexion::conectar()->prepare("CALL ObtenerProductos()");
	$stmt -> execute();
	return $stmt -> fetchAll();

}

/**
Obtener Producto
 */
static public function MdlObtenerProducto($valor){

	$stmt = Conexion::conectar()->prepare("CALL ObtenerProducto('$valor')");
	$stmt -> execute();
	return $stmt -> fetch();

}

/**
Obtener Producto - Inventario
 */
static public function MdlObtenerProductoInventario($valor){

	$stmt = Conexion::conectar()->prepare("CALL ObtenerProductoInventario('$valor')");
	$stmt -> execute();
	return $stmt -> fetch();

}


/**
Insertar Producto
 */

static public function MdlInsertarProducto($producto){

	$stmt = Conexion::conectar()->prepare("CALL InsertarProducto('$producto->id_producto','$producto->nombre','$producto->gramaje','$producto->id_categoria','$producto->id_marca','$producto->id_submarca','$producto->id_clasificacion','$producto->id_estatus')");

	$stmt -> execute();

    return $stmt -> fetch();
}

/**
Actualizar Producto
 */

static public function MdlActualizarProducto($producto){

	$stmt = Conexion::conectar()->prepare("CALL ActualizarProducto('$producto->id_producto','$producto->nombre','$producto->gramaje','$producto->id_categoria','$producto->id_marca','$producto->id_submarca','$producto->id_clasificacion','$producto->id_estatus')");

	if($stmt->execute()){
		return "ok";

	}else{
		return "error";
	}
}

}

