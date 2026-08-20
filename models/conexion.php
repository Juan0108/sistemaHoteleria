<?php

// El date.timezone del php.ini de este servidor no siempre coincide con la zona horaria
// real del hotel (Ciudad de México), lo que desfasa date()/strtotime() varias horas y
// puede marcar reservaciones del mismo día como "en el pasado". Se fija aquí para que
// coincida con la hora real del servidor de base de datos, sin depender del php.ini.
date_default_timezone_set("America/Mexico_City");

/**
 * Clase para la conexión al servidor de base de datos.
 */
class Conexion
{
    static public function conectar()
    {
        try {
            // Cambia localhost por tu hostname
            $link = new PDO(
                "mysql:host=posdit.com.mx;dbname=posditcommx_postdit",
                "posditcommx",       // Cambia esto por el nombre de usuario de tu base de datos
                "SistemaPostProduction*"  // Cambia esto por la contraseña de tu base de datos
            );

            // Configura la codificación para evitar problemas con caracteres especiales
            $link->exec("set names utf8");
            $link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $link;

        } catch (PDOException $e) {
            // Manejo de errores: muestra el mensaje de error si la conexión falla
            die("Error de conexión: " . $e->getMessage());
        }
    }
}
