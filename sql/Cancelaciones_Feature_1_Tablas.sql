-- Feature: cancelación de reservaciones con folio, motivo y estatus diferenciado
-- PARTE 1 de 2: solo tablas y catálogos (sin stored procedures).
-- Corre este archivo primero; la parte 2 (sql/Cancelaciones_Feature_2_SPs.sql)
-- depende de que estas tablas ya existan.
--
--   1. Renombra el estatus 12 (antes "OcupacionCancelada") a "CancelacionOcupacion".
--   2. Agrega el estatus 13 "CancelacionReserva".
--   3. Crea el catálogo cat_Motivos (con motivos precargados).
--   4. Crea la tabla Tb_Cancelaciones (Id_Cancelacion autoincrement = folio).
--
-- CÓMO APLICARLO: pega y ejecuta este archivo completo en la pestaña SQL de
-- phpMyAdmin, contra la base posditcommx_postdit.
--
-- Tb_Reservaciones.Id_Reservacion se confirmó con "SHOW COLUMNS FROM
-- Tb_Reservaciones;": es varchar(20) (el folio), no un entero — de ahí que
-- Tb_Cancelaciones.Id_Reservacion abajo también sea VARCHAR(20). cat_estatus se
-- confirmó con "CALL ObtenerEstatus();". Si aun así el CREATE TABLE falla con
-- "Foreign key constraint is incorrectly formed" (motor/collation distinta a
-- Tb_Reservaciones), quita las líneas "CONSTRAINT fk_..." (los datos igual
-- funcionan, solo sin esa validación extra a nivel de motor).

-- ============================================================
-- 1-2. Estatus de cancelación
-- ============================================================

UPDATE cat_estatus SET Nombre = 'CancelacionOcupacion' WHERE Id_Estatus = 12;

INSERT INTO cat_estatus (Id_Estatus, Nombre)
SELECT 13, 'CancelacionReserva'
WHERE NOT EXISTS (SELECT 1 FROM cat_estatus WHERE Id_Estatus = 13);

-- ============================================================
-- 3. Catálogo de motivos de cancelación
-- ============================================================

CREATE TABLE IF NOT EXISTS cat_Motivos (
	Id_Motivo INT NOT NULL AUTO_INCREMENT,
	Nombre VARCHAR(100) NOT NULL,
	PRIMARY KEY (Id_Motivo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO cat_Motivos (Nombre)
SELECT * FROM (SELECT 'Cliente canceló' AS Nombre) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM cat_Motivos WHERE Nombre = 'Cliente canceló');

INSERT INTO cat_Motivos (Nombre)
SELECT * FROM (SELECT 'Error de captura' AS Nombre) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM cat_Motivos WHERE Nombre = 'Error de captura');

INSERT INTO cat_Motivos (Nombre)
SELECT * FROM (SELECT 'No show' AS Nombre) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM cat_Motivos WHERE Nombre = 'No show');

INSERT INTO cat_Motivos (Nombre)
SELECT * FROM (SELECT 'Cambio de fecha' AS Nombre) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM cat_Motivos WHERE Nombre = 'Cambio de fecha');

INSERT INTO cat_Motivos (Nombre)
SELECT * FROM (SELECT 'Otro' AS Nombre) AS tmp
WHERE NOT EXISTS (SELECT 1 FROM cat_Motivos WHERE Nombre = 'Otro');

-- ============================================================
-- 4. Tabla de auditoría de cancelaciones (Id_Cancelacion = folio)
-- ============================================================

CREATE TABLE IF NOT EXISTS Tb_Cancelaciones (
	Id_Cancelacion INT NOT NULL AUTO_INCREMENT,
	Id_Reservacion VARCHAR(20) NOT NULL,
	Id_Estatus INT NOT NULL,
	Id_Motivo INT NOT NULL,
	Fecha_Cancelacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (Id_Cancelacion),
	KEY idx_tbcancelaciones_reservacion (Id_Reservacion),
	CONSTRAINT fk_tbcancelaciones_reservacion FOREIGN KEY (Id_Reservacion) REFERENCES Tb_Reservaciones (Id_Reservacion),
	CONSTRAINT fk_tbcancelaciones_estatus FOREIGN KEY (Id_Estatus) REFERENCES cat_estatus (Id_Estatus),
	CONSTRAINT fk_tbcancelaciones_motivo FOREIGN KEY (Id_Motivo) REFERENCES cat_Motivos (Id_Motivo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
