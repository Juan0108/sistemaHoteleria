<?php

require_once "conexion.php";

/**
 * 
 */
class ModeloVentas{

/**
Obtener Ventas
 */
static public function MdlObtenerVentas($IdUsuario){

	$stmt = Conexion::conectar()->prepare("CALL ObtenerVentas('$IdUsuario')");
	$stmt -> execute();
	return $stmt -> fetchall();

}

/**
Obtener Cierre Día
 */
static public function MdlObtenerCierreDia($IdUsuario){

	$stmt = Conexion::conectar()->prepare("CALL ObtenerCierreDia('$IdUsuario')");
	$stmt -> execute();
	return $stmt -> fetchall();

}

/**
Obtener Inventarios Por Rango de Fecha
 */
static public function MdlObtenerVentasFecha($idusuario, $fi, $ff){

	$stmt = Conexion::conectar()->prepare("CALL ObtenerVentasFecha('$idusuario', '$fi', '$ff')");
	$stmt -> execute();
	return $stmt -> fetchAll();

}

/**

/**
Obtener suma Ventas
 */
static public function MdlObtenerSumaVentas($valor){

	$stmt = Conexion::conectar()->prepare("CALL ObtenerSumaVentas('$valor')");
	$stmt -> execute();
	return $stmt -> fetch();

}

/**
Obtener Ventas
 */
static public function MdlObtenerTicket($IdTicket, $IdUsuario){

	$stmt = Conexion::conectar()->prepare("CALL ObtenerTicket('$IdTicket', $IdUsuario)");
	$stmt -> execute();
	return $stmt -> fetchall();

}

/**
Obtener suma Ganancias
 */
static public function MdlObtenerSumaGanancias($valor){

	$stmt = Conexion::conectar()->prepare("CALL ObtenerSumaGanancias('$valor')");
	$stmt -> execute();
	return $stmt -> fetch();

}

/**
Obtener Fechas y montos sumarizados de las Ventas
 */
static public function MdlObtenerGraficaVentas($valor){

	$stmt = Conexion::conectar()->prepare("CALL ObtenerGraficaVentas('$valor')");
	$stmt -> execute();

	$ventas = array();
 
 	while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
  
  		$ventas[] = $row;
 	}


	 echo json_encode($ventas);

}

/**
Obtener Fechas y montos con ganacias sumarizados de las Ventas
 */
static public function MdlObtenerGraficaBalance($valor){

	$stmt = Conexion::conectar()->prepare("CALL ObtenerGraficaBalance('$valor')");
	$stmt -> execute();

	$balance = array();
 
 	while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
  
  		$balance[] = $row;
 	}


	 echo json_encode($balance);

}

/**
Insertar Inventario
 */

static public function MdlInsertarVenta($vIdVenta,$vCantidad,$vNTicket,$vIdUsuario, $vPrecioFinal, $vCliente, $vReservacion = 0){

	// CrearVentaDescuentos (6 parámetros) es el procedure original, usado por
	// el resto del sistema en producción: no se toca su firma. Cuando la venta
	// viene ligada a una habitación ocupada usamos el procedure independiente
	// InsertarConsumoHabitacion (7 parámetros), que además liga el
	// consumo en Tb_Consumo.
	if (!empty($vReservacion) && $vReservacion !== '0') {

		$stmt = Conexion::conectar()->prepare("CALL InsertarConsumoHabitacion(?,?,?,?,?,?,?)");

		$stmt->bindValue(1, $vIdVenta, PDO::PARAM_INT);
		$stmt->bindValue(2, $vCantidad, PDO::PARAM_INT);
		$stmt->bindValue(3, $vNTicket, PDO::PARAM_INT);
		$stmt->bindValue(4, $vIdUsuario, PDO::PARAM_INT);
		$stmt->bindValue(5, $vPrecioFinal);
		$stmt->bindValue(6, $vCliente, PDO::PARAM_INT);
		$stmt->bindValue(7, $vReservacion, PDO::PARAM_STR);

	} else {

		$stmt = Conexion::conectar()->prepare("CALL CrearVentaDescuentos(?,?,?,?,?,?)");

		$stmt->bindValue(1, $vIdVenta, PDO::PARAM_INT);
		$stmt->bindValue(2, $vCantidad, PDO::PARAM_INT);
		$stmt->bindValue(3, $vNTicket, PDO::PARAM_INT);
		$stmt->bindValue(4, $vIdUsuario, PDO::PARAM_INT);
		$stmt->bindValue(5, $vPrecioFinal);
		$stmt->bindValue(6, $vCliente, PDO::PARAM_INT);

	}

	if($stmt->execute()){
		return "ok";

	}else{
		return "error";
	}
}

/**
Obtener Ticket Nuevo
 */
static public function MdlObtenerNuevoTicket($valor){

	$stmt = Conexion::conectar()->prepare("CALL ObtenerNuevoTicket('$valor')");
	$stmt -> execute();
	return $stmt -> fetch();

}

/**
Obtener Top Ventas
 */
static public function MdlObtenerTopVentas($IdUsuario){

	$stmt = Conexion::conectar()->prepare("CALL ObtenerTopVentas('$IdUsuario')");
	$stmt -> execute();
	return $stmt -> fetchall();

}

/**
Inserta Recarga Telefonica
 */
static public function MdlInsertarVentaYServicio(
        $p_NTicket,
        $p_Id_Producto,
        $p_Cantidad,
        $p_PrecioCompra,
        $p_PrecioVenta,
        $p_Id_Usuario,
        $p_Fecha_Compra,
        $p_id_Nombre,
        $p_id_Codigo,
        $p_Descripcion,
        $p_Numero,
        $p_Folio
    ) {
        $stmt = Conexion::conectar()->prepare("CALL InsertarVentaYServicio(
            :p_NTicket,
            :p_Id_Producto,
            :p_Cantidad,
            :p_PrecioCompra,
            :p_PrecioVenta,
            :p_Id_Usuario,
            :p_Fecha_Compra,
            :p_id_Nombre,
            :p_id_Codigo,
            :p_Descripcion,
            :p_Numero,
            :p_Folio
        )");

        $stmt->bindParam(":p_NTicket", $p_NTicket, PDO::PARAM_INT);
        $stmt->bindParam(":p_Id_Producto", $p_Id_Producto, PDO::PARAM_STR);
        $stmt->bindParam(":p_Cantidad", $p_Cantidad, PDO::PARAM_INT);
        $stmt->bindParam(":p_PrecioCompra", $p_PrecioCompra, PDO::PARAM_STR);
        $stmt->bindParam(":p_PrecioVenta", $p_PrecioVenta, PDO::PARAM_STR);
        $stmt->bindParam(":p_Id_Usuario", $p_Id_Usuario, PDO::PARAM_INT);
        $stmt->bindParam(":p_Fecha_Compra", $p_Fecha_Compra, PDO::PARAM_STR);
        $stmt->bindParam(":p_id_Nombre", $p_id_Nombre, PDO::PARAM_STR);
        $stmt->bindParam(":p_id_Codigo", $p_id_Codigo, PDO::PARAM_STR);
        $stmt->bindParam(":p_Descripcion", $p_Descripcion, PDO::PARAM_STR);
        $stmt->bindParam(":p_Numero", $p_Numero, PDO::PARAM_STR);
        $stmt->bindParam(":p_Folio", $p_Folio, PDO::PARAM_STR);

        if ($stmt->execute()) {
            return "ok";
        } else {
            return "error";
        }
    }

}