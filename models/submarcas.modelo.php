<?php

require_once "conexion.php";

/**
 * 
 */
class ModeloSubMarcas{

/**
Insertar SubMarca
 */

static public function MdlInsertarSubMarca($submarca){

	$stmt = Conexion::conectar()->prepare("CALL InsertarSubMarca('$submarca->nombre','$submarca->id_marca','$submarca->id_estatus')");

	$stmt -> execute();

    return $stmt -> fetch();
}	


/**
Obtener SubMarcas
 */
static public function MdlObtenerSubMarcas(){

	$stmt = Conexion::conectar()->prepare("CALL ObtenerSubMarcas()");
	$stmt -> execute();
	return $stmt -> fetchAll();

}

/**
Obtener SubMarca
 */
static public function MdlObtenerSubMarca($valor){

	$stmt = Conexion::conectar()->prepare("CALL ObtenerSubMarca('$valor')");
	$stmt -> execute();
	return $stmt -> fetch();

}

/**
Obtener SubMarcas JSON
 */
static public function MdlJsonObtenerSubMarcasCategoria($valor){

	$stmt = Conexion::conectar()->prepare("CALL ObtenerComboSubMarcasActivas('$valor')");
	$stmt -> execute();

	$submarcas = array();
 
 	while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
  
  		$submarcas['SubMarcas'][] = $row;
 	}

	 echo json_encode($submarcas);

}

/**
Actualizar SubMarca
 */

static public function MdlActualizarSubMarca($submarca){

	$stmt = Conexion::conectar()->prepare("CALL ActualizarsubMarca('$submarca->id_submarca','$submarca->nombre','$submarca->id_estatus')");

	if($stmt->execute()){
		return "ok";

	}else{
		return "error";
	}
}

}

