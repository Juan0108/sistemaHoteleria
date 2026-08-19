-- Feature: cancelación de reservaciones con folio, motivo y estatus diferenciado
-- PARTE 2 de 2: stored procedures. Requiere haber corrido primero
-- sql/Cancelaciones_Feature_1_Tablas.sql (usa cat_estatus 12/13, cat_Motivos y
-- Tb_Cancelaciones, que ese script crea).
--
--   5. Reemplaza CancelarReservacion para que, según el estatus que tenía la
--      reservación (8 Ocupado / 9 Reservado), aplique el nuevo estatus correcto
--      (12 CancelacionOcupacion / 13 CancelacionReserva) e inserte el registro
--      de auditoría en Tb_Cancelaciones.
--   6. Crea ObtenerMotivosCancelacion para poblar el combo del formulario.
--
-- CÓMO APLICARLO: pega y ejecuta este archivo completo en la pestaña SQL de
-- phpMyAdmin, contra la base posditcommx_postdit.

-- ============================================================
-- 5. CancelarReservacion: ahora recibe el motivo, decide el estatus de
--    cancelación correcto según lo que se está cancelando, y deja el
--    folio/motivo/fecha en Tb_Cancelaciones.
-- ============================================================

DELIMITER $$

DROP PROCEDURE IF EXISTS CancelarReservacion$$

CREATE PROCEDURE CancelarReservacion(
	IN p_id_reservacion VARCHAR(20),
	IN p_id_hotel INT,
	IN p_id_motivo INT
)
BEGIN
	DECLARE v_estatus_actual INT DEFAULT NULL;
	DECLARE v_estatus_cancelacion INT DEFAULT NULL;
	DECLARE v_afectados INT DEFAULT 0;
	DECLARE v_folio INT DEFAULT NULL;

	SELECT r.Id_Estatus INTO v_estatus_actual
	FROM Tb_Reservaciones r
	INNER JOIN cat_habitaciones h ON h.Id_Habitacion = r.Id_Habitacion
	WHERE r.Id_Reservacion = p_id_reservacion
	  AND h.Id_Hotel = p_id_hotel
	  AND r.Id_Estatus IN (8, 9)
	LIMIT 1;

	IF v_estatus_actual IS NOT NULL THEN

		-- 8=Ocupado -> 12=CancelacionOcupacion ; 9=Reservado -> 13=CancelacionReserva
		SET v_estatus_cancelacion = IF(v_estatus_actual = 8, 12, 13);

		UPDATE Tb_Reservaciones
		SET Id_Estatus = v_estatus_cancelacion
		WHERE Id_Reservacion = p_id_reservacion;

		SET v_afectados = ROW_COUNT();

		INSERT INTO Tb_Cancelaciones (Id_Reservacion, Id_Estatus, Id_Motivo, Fecha_Cancelacion)
		VALUES (p_id_reservacion, v_estatus_cancelacion, p_id_motivo, NOW());

		SET v_folio = LAST_INSERT_ID();
	END IF;

	SELECT v_afectados AS Afectados, v_folio AS Folio, v_estatus_cancelacion AS Id_Estatus;
END$$

DELIMITER ;

-- ============================================================
-- 6. Catálogo de motivos para el combo del formulario de cancelación
-- ============================================================

DELIMITER $$

DROP PROCEDURE IF EXISTS ObtenerMotivosCancelacion$$

CREATE PROCEDURE ObtenerMotivosCancelacion()
BEGIN
	SELECT Id_Motivo, Nombre
	FROM cat_Motivos
	ORDER BY Nombre;
END$$

DELIMITER ;
