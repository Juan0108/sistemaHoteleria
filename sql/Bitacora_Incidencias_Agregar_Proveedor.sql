-- Agrega Proveedor a ObtenerBitacoraIncidencias (bitácora de incidencias de
-- la habitación), para llevar registro de quién reparó cada incidencia.
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
		m.Proveedor,
		m.Id_Estatus
	FROM Tb_Mantenimiento m
	WHERE m.Id_Habitacion = v_id_habitacion
	ORDER BY m.Fecha_Registro DESC;
END$$

DELIMITER ;
