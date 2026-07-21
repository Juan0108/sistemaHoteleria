<?php

require_once "conexion.php";

/**
 * 
 */
class ModeloCategorias{

/**
Insertar Categoria
 */

static public function MdlInsertarCategoria($categoria){

	$stmt = Conexion::conectar()->prepare("CALL InsertarCategoria('$categoria->nombre','$categoria->descripcion','$categoria->id_estatus')");
	$stmt -> execute();

     return $stmt -> fetch();
}	


/**
Obtener Categorias
 */
static public function MdlObtenerCategorias(){

	$stmt = Conexion::conectar()->prepare("CALL ObtenerCategorias()");
	$stmt -> execute();
	return $stmt -> fetchAll();

}

/**
Obtener Categorias JSON
 */
static public function MdlJsonObtenerCategorias(){

	$stmt = Conexion::conectar()->prepare("CALL ObtenerCategoriasActivas()");
	$stmt -> execute();

	$categorias = array();
 
 	while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
  
  		$categorias['data'][] = $row;
 	}

	 echo json_encode($categorias);

}


/**
Obtener Categorias
 */
static public function MdlObtenerCategoria($valor){

	$stmt = Conexion::conectar()->prepare("CALL ObtenerCategoria('$valor')");
	$stmt -> execute();
	return $stmt -> fetch();

}

/**
Insertar Categoria
 */

static public function MdlActualizarCategoria($categoria){

	$stmt = Conexion::conectar()->prepare("CALL ActualizarCategoria('$categoria->id_categoria','$categoria->nombre','$categoria->descripcion','$categoria->id_estatus')");

	if($stmt->execute()){
		return "ok";

	}else{
		return "error";
	}
}

}

