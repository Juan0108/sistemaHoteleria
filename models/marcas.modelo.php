<?php

require_once "conexion.php";

/**
 * 
 */
class ModeloMarcas{

/**
Insertar Marca
 */

static public function MdlInsertarMarca($marca){

	$stmt = Conexion::conectar()->prepare("CALL InsertarMarca('$marca->nombre','$marca->descripcion','$marca->id_categoria','$marca->id_estatus')");

	$stmt -> execute();

    return $stmt -> fetch();
}	


/**
Obtener Marcas
 */
static public function MdlObtenerMarcas(){

	$stmt = Conexion::conectar()->prepare("CALL ObtenerMarcas()");
	$stmt -> execute();
	return $stmt -> fetchAll();

}

/**
Obtener Marca
 */
static public function MdlObtenerMarca($valor){

	$stmt = Conexion::conectar()->prepare("CALL ObtenerMarca('$valor')");
	$stmt -> execute();
	return $stmt -> fetch();

}

/**
Obtener Marcas JSON
 */
static public function MdlJsonObtenerMarcas(){

	$stmt = Conexion::conectar()->prepare("CALL ObtenerMarcasActivas()");
	$stmt -> execute();

	$marcas = array();
 
 	while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
  
  		$marcas['data'][] = $row;
 	}

	 echo json_encode($marcas);

}

/**
Obtener Marcas JSON
 */
static public function MdlJsonObtenerMarcasCategoria($valor){

	$stmt = Conexion::conectar()->prepare("CALL ObtenerComboMarcasActivas('$valor')");
	$stmt -> execute();

	$marcas = array();
 
 	while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
  
  		$marcas['Marcas'][] = $row;
 	}

	 echo json_encode($marcas);

}

/**
Insertar Marca
 */

static public function MdlActualizarMarca($marca){

	$stmt = Conexion::conectar()->prepare("CALL ActualizarMarca('$marca->id_marca','$marca->descripcion','$marca->id_estatus')");

	if($stmt->execute()){
		return "ok";

	}else{
		return "error";
	}
}

}

