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