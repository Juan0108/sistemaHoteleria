<?php

// Importar los controladores y modelos necesarios
require_once "../controllers/eventos.controlador.php";
require_once "../models/eventos.modelo.php";

class SendWhatsAppNotificationJob
{
    private $evento;

    public function __construct($evento)
    {
        $this->evento = $evento; // Se pasa el evento al constructor
    }

    public function handle()
    {
        try {

            $Prefijo = 52;
            $telefono = str_replace([' ', '(', ')', '-'], '', $this->evento['Telefono']);
            $Celular = $Prefijo . $telefono;
            $idevento = $this->evento['Id_Evento'];

            //Prepara Mensaje              
            $mensaje = "📢 *Sistema Automático de Mensaje de Notificación:*\n" .
            "🏢 *Negocio:* {$this->evento['Razon_Social']}\n" .
            "👤 *Solicitante:* {$this->evento['NombreUsuario']}\n" .
            "📝 *Comentarios Solicitante:* {$this->evento['Comentario']}\n" .
            "📅 *Día del Evento:* {$this->evento['DiaEvento']}\n" .
            "🎯 *Evento:* {$this->evento['NombreEvento']}\n\n" .
            "🔗 *Si requiere mayor información puede consultar el calendario de eventos";


            // Datos para la API
             $apiUrl = 'https://apiwsp.factiliza.com/api/v1/message/sendtext/NTI1NTI1MzI3MzA0';
             $token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiI2MjciLCJuYW1lIjoiSnVhbiBEYXZpZCBBZ3VpbGFyIEJhcnJvbiAiLCJlbWFpbCI6ImFndWlsYXJiYXJyb25qdWFuZGF2aWRAZ21haWwuY29tIiwiaHR0cDovL3NjaGVtYXMubWljcm9zb2Z0LmNvbS93cy8yMDA4LzA2L2lkZW50aXR5L2NsYWltcy9yb2xlIjoiY29uc3VsdG9yIn0.r5cvSNgCntPbf4OCjqx1JlS885CxHSN7FyxCLlVBAus';
             $data = array(
                 "number" => $Celular,
                 "text" => $mensaje
             );

            $this->enviarMensajeAPI($apiUrl,$token,$data);

            //Actualiza Status de la Notificación
            ControladorEventos::crtUpdateStatusNotificacion($idevento,7);

        } catch (\Exception $e) {
            $this->log("Error en Job: " . $e->getMessage());
        }

    }

    private function enviarMensajeAPI($url, $token, $data){
        $ch = curl_init($url);

        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token
            ),
        ));

        $response = curl_exec($ch);

        // Detectar errores en cURL
        if (curl_errno($ch)) {
            $errorMsg = 'Error en cURL: ' . curl_error($ch);
            curl_close($ch);
            throw new \Exception($errorMsg); // Lanza una excepción
        }

        // Verificar el código HTTP de respuesta
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($httpCode < 200 || $httpCode >= 300) {
            curl_close($ch);
            throw new \Exception("Error HTTP: Código {$httpCode}, Respuesta: {$response}");
        }

        curl_close($ch);
        return $response;
    }


    private function log($mensaje)
    {
        // Guardar logs en un archivo
        $logDir = __DIR__ . '/logs'; // Ruta absoluta del directorio de logs
        $logFile = $logDir . '/jobs.log';

        // Verificar si el directorio existe, si no, crearlo
        if (!file_exists($logDir)) {
            mkdir($logDir, 0777, true); // Crear el directorio con permisos recursivos
        }

        // Escribir en el archivo de log
        file_put_contents($logFile, date('Y-m-d H:i:s') . " - " . $mensaje . PHP_EOL, FILE_APPEND);
    }


}

// Obtener eventos pendientes desde el controlador
$eventos = ControladorEventos::crtObtenerEventosProveedor();

// Ejecutar el Job para cada evento
foreach ($eventos as $evento) {
    $job = new SendWhatsAppNotificationJob($evento);
    $job->handle();
}
