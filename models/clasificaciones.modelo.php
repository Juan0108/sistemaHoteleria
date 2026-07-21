<?php

require_once "conexion.php";

/**
 * 
 */
class ModeloClasificaciones{

/**
Insertar Clasificación
 */

static public function MdlInsertarClasificacion($clasificacion){

	$stmt = Conexion::conectar()->prepare("CALL InsertarClasificacion('$clasificacion->nombre','$clasificacion->descripcion','$clasificacion->id_categoria','$clasificacion->id_estatus')");

	$stmt -> execute();

    return $stmt -> fetch();
}	


/**
Obtener Clasificaciones
 */
static public function MdlObtenerClasificaciones(){

	$stmt = Conexion::conectar()->prepare("CALL ObtenerClasificaciones()");
	$stmt -> execute();
	return $stmt -> fetchAll();

}

/**
Obtener Clasificación
 */
static public function MdlObtenerClasificacion($valor){

	$stmt = Conexion::conectar()->prepare("CALL ObtenerClasificacion('$valor')");
	$stmt -> execute();
	return $stmt -> fetch();

}

/**
Obtener Marcas JSON
 */
static public function MdlJsonObtenerClasificaciones($valor){

	$stmt = Conexion::conectar()->prepare("CALL ObtenerComboClasificacionesActivas('$valor')");
	$stmt -> execute();

	$clasificaciones = array();
 
 	while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
  
  		$clasificaciones['Clasificaciones'][] = $row;
 	}

	 echo json_encode($clasificaciones);

}

/**
Actualizar Clasificación
 */

static public function MdlActualizarClasificacion($clasificacion){

	$stmt = Conexion::conectar()->prepare("CALL ActualizarClasificacion('$clasificacion->id_clasificacion','$clasificacion->nombre','$clasificacion->descripcion','$clasificacion->id_estatus')");

	if($stmt->execute()){
		return "ok";

	}else{
		return "error";
	}
}

}

