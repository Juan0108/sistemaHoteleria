<?php 

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
                "Davidios0108*"  // Cambia esto por la contraseña de tu base de datos
            );

            // Configura la codificación para evitar problemas con caracteres especiales
            $link->exec("set names utf8");

            return $link;

        } catch (PDOException $e) {
            // Manejo de errores: muestra el mensaje de error si la conexión falla
            die("Error de conexión: " . $e->getMessage());
        }
    }
}
