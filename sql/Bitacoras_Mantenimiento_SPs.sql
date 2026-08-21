-- Feature: Bitácoras de Incidencias y de Abonos (por habitación)
-- No requiere tablas nuevas: usa Tb_Mantenimiento y Tb_Abonos, que ya existen.
--
--   1. ObtenerBitacoraIncidencias: recibe una incidencia (idMantenimiento) y el
--      hotel en sesión; resuelve a qué habitación pertenece y regresa el
--      historial completo de TODAS las incidencias que ha tenido esa
--      habitación (foto + fecha + descripción + estatus), sin importar en
--      cuál tarjeta se dio clic al botón de bitácora.
--   2. ObtenerBitacoraAbonos: mismo criterio pero regresa TODOS los abonos
--      hechos a cualquier incidencia de esa habitación (monto + fecha + foto
--      del ticket + a qué incidencia perteneció + quién lo registró).
--
-- CÓMO APLICARLO: pega y ejecuta este archivo completo en la pestaña SQL de
-- phpMyAdmin, contra la base posditcommx_postdit.

DELIMITER $$

DROP PROCEDURE IF EXISTS ObtenerBitacoraIncidencias$$

CREATE PROCEDURE ObtenerBitacoraIncidencias(
	IN p_id_mantenimiento INT,
	IN p_id_hotel INT
)
BEGIN
	DECLARE v_id_habitacion INT DEFAULT NULL;

	SELECT h.Id_Habitacion INTO v_id_habitacion
	FROM Tb_Mantenimiento m
	INNER JOIN cat_habitaciones h ON h.Id_Habitacion = m.Id_Habitacion
	WHERE m.Id_Mantenimiento = p_id_mantenimiento
	  AND h.Id_Hotel = p_id_hotel
	LIMIT 1;

	SELECT
		m.Id_Mantenimiento,
		m.Foto,
		m.Fecha_Registro,
		m.Descripcion,
		m.Id_Estatus
	FROM Tb_Mantenimiento m
	WHERE m.Id_Habitacion = v_id_habitacion
	ORDER BY m.Fecha_Registro DESC;
END$$

DELIMITER ;

DELIMITER $$

DROP PROCEDURE IF EXISTS ObtenerBitacoraAbonos$$

CREATE PROCEDURE ObtenerBitacoraAbonos(
	IN p_id_mantenimiento INT,
	IN p_id_hotel INT
)
BEGIN
	DECLARE v_id_habitacion INT DEFAULT NULL;

	SELECT h.Id_Habitacion INTO v_id_habitacion
	FROM Tb_Mantenimiento m
	INNER JOIN cat_habitaciones h ON h.Id_Habitacion = m.Id_Habitacion
	WHERE m.Id_Mantenimiento = p_id_mantenimiento
	  AND h.Id_Hotel = p_id_hotel
	LIMIT 1;

	SELECT
		a.Id_Abono,
		a.Monto,
		a.Fecha_Abono,
		a.Foto,
		m2.Id_Mantenimiento,
		m2.Descripcion,
		u.Nombre,
		u.Apaterno,
		u.Amaterno
	FROM Tb_Abonos a
	INNER JOIN Tb_Mantenimiento m2 ON m2.Id_Mantenimiento = a.Id_Mantenimiento
	LEFT JOIN usuarios u ON u.Id_Usuario = a.Id_Usuario
	WHERE m2.Id_Habitacion = v_id_habitacion
	ORDER BY a.Fecha_Abono DESC;
END$$

DELIMITER ;
