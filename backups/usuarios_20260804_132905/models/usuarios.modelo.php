<?php

require_once "conexion.php";


class ModeloUsuarios{

// Método para actualizar la contraseña del usuario
static public function MdlActualizarContraseña($id_usuario, $password){
	// Conectar a la base de datos
	
	$stmt = Conexion::conectar()->prepare("CALL ActualizarContrasena(:id_usuario, :password)");

	// Enlazar los parámetros con los valores de entrada
	$stmt->bindParam(":id_usuario", $id_usuario, PDO::PARAM_INT);
	$stmt->bindParam(":password", $password, PDO::PARAM_STR);

	// Ejecutar la consulta
	if ($stmt->execute()) {
		return true; // Indicar que se actualizó correctamente
	} else {
		return false; // Indicar que ocurrió un error
	}

	// Cerrar la conexión
	$stmt = null;
}

static public function MdlResetPassword($id_usuario, $password){
	// Conectar a la base de datos
	
	$stmt = Conexion::conectar()->prepare("CALL ResetPassword(:id_usuario, :password)");

	// Enlazar los parámetros con los valores de entrada
	$stmt->bindParam(":id_usuario", $id_usuario, PDO::PARAM_INT);
	$stmt->bindParam(":password", $password, PDO::PARAM_STR);

	// Ejecutar la consulta
	if ($stmt->execute()) {
		return $stmt->rowCount() > 0; // Solo verdadero si realmente afectó una fila
	} else {
		return false; // Indicar que ocurrió un error
	}

	// Cerrar la conexión
	$stmt = null;
}

static public function MdlSuspenderUsuario($id_usuario){
	$stmt = Conexion::conectar()->prepare("UPDATE usuarios SET id_estatus = 2 WHERE id_usuario = :id_usuario");
	$stmt->bindParam(":id_usuario", $id_usuario, PDO::PARAM_INT);

	if ($stmt->execute()) {
		return $stmt->rowCount() > 0;
	} else {
		return false;
	}

	$stmt = null;
}

/**
Obtener Usuario
 */	
static public function MdlObtenerUsuario($valor){

	$stmt = Conexion::conectar()->prepare("CALL ObtenerUsuario('$valor')");
	$stmt -> execute();
	return $stmt -> fetch();

}

	/**
Obtener Usuario Nombre
 */	
static public function MdlObtenerUsuarioNombre($valor){

	$stmt = Conexion::conectar()->prepare("CALL ObtenerUsuarioNombre('$valor')");
	$stmt -> execute();
	return $stmt -> fetch();

}

/**
Obtener Usuarios
 */
	static public function MdlObtenerUsuarios(){

		$stmt = Conexion::conectar()->prepare("CALL ObtenerUsuarios()");
		$stmt -> execute();
		return $stmt -> fetchAll();

	}

/**
Obtener Usuarios de Hoteleria (Id_Giro = 2) pertenecientes a un negocio especifico
 */
	static public function MdlObtenerUsuariosHotel($idNegocio){

		$stmt = Conexion::conectar()->prepare("CALL ObtenerUsuariosHotel(:idNegocio)");
		$stmt -> bindParam(":idNegocio", $idNegocio, PDO::PARAM_INT);
		$stmt -> execute();
		$respuesta = $stmt -> fetchAll(PDO::FETCH_ASSOC);
		$stmt -> closeCursor();
		return $respuesta;

	}

/*
Obtener Perfiles de Usuario
 */

	static public function MdlObtenerPerfiles(){

		$stmt = Conexion::conectar()->prepare("CALL Obtenerperfiles()");
		$stmt -> execute();
		return $stmt;

	}

/**
Insertar  Usuario
 */

	static public function MdlInsertarUsuario($Usuario){

		$stmt = Conexion::conectar()->prepare("CALL InsertarUsuario(
			'$Usuario->nombre',
			'$Usuario->Apaterno',
			'$Usuario->Amaterno',
			'$Usuario->id_negocio',
			'$Usuario->Calle',
			'$Usuario->Id_estado',
			'$Usuario->Id_municipio',
			'$Usuario->id_colonia',
			'$Usuario->id_perfil',
			'$Usuario->usuario',
			'$Usuario->password',
			'$Usuario->foto',
			'$Usuario->id_estatus')");

	 $stmt -> execute();

     return $stmt -> fetch();
	}

	static public function MdlActualizarUsuario($Usuario){

		if($Usuario->password == null){

			$stmt = Conexion::conectar()->prepare("CALL ActualizarUsuario(
			'$Usuario->nombre',
			'$Usuario->Apaterno',
			'$Usuario->Amaterno',
			'$Usuario->id_negocio',
			'$Usuario->Calle',
			'$Usuario->Id_estado',
			'$Usuario->Id_municipio',
			'$Usuario->id_colonia',
			'$Usuario->id_perfil',
			'$Usuario->usuario')");

		}

		else{

			$stmt = Conexion::conectar()->prepare("CALL ActualizarUsuarioPassword(
			'$Usuario->nombre',
			'$Usuario->Apaterno',
			'$Usuario->Amaterno',
			'$Usuario->id_negocio',
			'$Usuario->Calle',
			'$Usuario->Id_estado',
			'$Usuario->Id_municipio',
			'$Usuario->id_colonia',
			'$Usuario->id_perfil',
			'$Usuario->usuario',
			'$Usuario->password')");

		}

	 return $stmt -> execute();

     
	}

}