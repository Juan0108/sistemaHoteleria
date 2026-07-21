<?php
class ModeloWebservice{
    static public function mdlConsultarSaldos($cuenta, $usuario, $password)
    {
        try {
            $ws = "https://app.sivetel.com:443/index.php/WebService";
            $xml_post_string = '<soapenv:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:urn="urn:WSDL_sivetel">
            <soapenv:Header/>
            <soapenv:Body>
               <urn:ConsultarSaldos soapenv:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
                  <request xsi:type="urn:ConsultarSaldos">
                     <!--You may enter the following 3 items in any order-->
                     <cuenta xsi:type="xsd:string">'.$cuenta.'</cuenta>
                     <usuario xsi:type="xsd:string">'.$usuario.'</usuario>
                     <password xsi:type="xsd:string">'.$password.'</password>
                  </request>
               </urn:ConsultarSaldos>
            </soapenv:Body>
         </soapenv:Envelope>';

            $headers = array(
                "Content-type: text/xml;charset=\"utf-8\"",
                "Accept: text/xml",
                "Cache-Control: no-cache",
                "Pragma: no-cache",
                "SOAPAction: ",
                "Content-length: " . strlen($xml_post_string),
            );

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
            curl_setopt($ch, CURLOPT_URL, $ws);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $response = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpcode == 200) //hubo conexion
            {
                $doc = new DOMDocument();
                $doc->loadXML($response); //lo que tengo en memoria lo convierto en un XML

                return $doc;
            } else {
                echo curl_error($ch);
                echo "</br> Problema de conexión";
            }
        } catch (Exception $e) {
            echo "error web service: " . $e->getMessage();
        }
    }
    static function mdlConsultarTransaccion($cuenta, $usuario, $password,$requestid)
    {
        try {
            $ws = "https://app.sivetel.com:443/index.php/WebService";
            $xml_post_string = '<soapenv:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:urn="urn:WSDL_sivetel">
            <soapenv:Header/>
            <soapenv:Body>
               <urn:ConsultarTransaccion soapenv:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
                  <request xsi:type="urn:ConsultarTransaccion">
                     <!--You may enter the following 4 items in any order-->
                     <cuenta xsi:type="xsd:string">'.$cuenta.'</cuenta>
                     <usuario xsi:type="xsd:string">'.$usuario.'</usuario>
                     <password xsi:type="xsd:string">'.$password.'</password>
                     <requestid xsi:type="xsd:string">'.$requestid.'</requestid>
                  </request>
               </urn:ConsultarTransaccion>
            </soapenv:Body>
         </soapenv:Envelope>';

            $headers = array(
                "Content-type: text/xml;charset=\"utf-8\"",
                "Accept: text/xml",
                "Cache-Control: no-cache",
                "Pragma: no-cache",
                "SOAPAction: ",
                "Content-length: " . strlen($xml_post_string),
            );

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
            curl_setopt($ch, CURLOPT_URL, $ws);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $response = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpcode == 200) //hubo conexion
            {

                $doc = new DOMDocument();
                $doc->loadXML($response); //lo que tengo en memoria lo convierto en un XML
                return $doc;
                
            } else {
                echo curl_error($ch);
                echo "</br> Problema de conexión";
            }
        } catch (Exception $e) {
            echo "error web service: " . $e->getMessage();
        }
    }
    static function mdlObtenerPines($cuenta, $usuario, $password)
    {
        try {
            $ws = "https://app.sivetel.com:443/index.php/WebService";
            $xml_post_string = '<soapenv:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:urn="urn:WSDL_sivetel">
            <soapenv:Header/>
            <soapenv:Body>
               <urn:ObtenerPines soapenv:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
                  <request xsi:type="urn:ObtenerProductos">
                     <!--You may enter the following 3 items in any order-->
                     <cuenta xsi:type="xsd:string">'.$cuenta.'</cuenta>
                     <usuario xsi:type="xsd:string">'.$usuario.'</usuario>
                     <password xsi:type="xsd:string">'.$password.'</password>
         
                  </request>
               </urn:ObtenerPines>
            </soapenv:Body>
         </soapenv:Envelope>';

            $headers = array(
                "Content-type: text/xml;charset=\"utf-8\"",
                "Accept: text/xml",
                "Cache-Control: no-cache",
                "Pragma: no-cache",
                "SOAPAction: ",
                "Content-length: " . strlen($xml_post_string),
            );

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
            curl_setopt($ch, CURLOPT_URL, $ws);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string); // the SOAP request
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $response = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpcode == 200) //hubo conexion
            {
                $doc = new DOMDocument();
                $doc->loadXML($response); //lo que tengo en memoria lo convierto en un XML

                return $doc;

            } else {
                echo curl_error($ch);
                echo "</br> Problema de conexión";
            }
        } catch (Exception $e) {
            echo "error web service: " . $e->getMessage();
        }
    }
    static function mdlObtenerProductos($cuenta, $usuario, $password)
    {
        try {
            $ws = "https://app.sivetel.com:443/index.php/WebService";
            $xml_post_string = '<soapenv:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:urn="urn:WSDL_sivetel">
            <soapenv:Header/>
            <soapenv:Body>
               <urn:ObtenerProductos soapenv:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
                  <request xsi:type="urn:ObtenerProductos">
                     <!--You may enter the following 3 items in any order-->                     
                     <cuenta xsi:type="xsd:string">'.$cuenta.'</cuenta>
                     <usuario xsi:type="xsd:string">'.$usuario.'</usuario>
                     <password xsi:type="xsd:string">'.$password.'</password>
         
                  </request>
               </urn:ObtenerProductos>
            </soapenv:Body>
         </soapenv:Envelope>';

            $headers = array(
                "Content-type: text/xml;charset=\"utf-8\"",
                "Accept: text/xml",
                "Cache-Control: no-cache",
                "Pragma: no-cache",
                "SOAPAction: ",
                "Content-length: " . strlen($xml_post_string),
            );

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
            curl_setopt($ch, CURLOPT_URL, $ws);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $response = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpcode == 200) //hubo conexion
            {

                $doc = new DOMDocument();
                $doc->loadXML($response); //lo que tengo en memoria lo convierto en un XML

                return $doc;

            } else {
                echo curl_error($ch);
                echo "</br> Problema de conexión";
            }
        } catch (Exception $e) {
            echo "error web service: " . $e->getMessage();
        }
    }
    static function mdlObtenerServicios($cuenta, $usuario, $password)
    {
        try {
            $ws = "https://app.sivetel.com:443/index.php/WebService";
            $xml_post_string = '<soapenv:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:urn="urn:WSDL_sivetel">
            <soapenv:Header/>
            <soapenv:Body>
               <urn:ObtenerServicios soapenv:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
                  <request xsi:type="urn:ObtenerProductos">
                     <!--You may enter the following 3 items in any order-->
                     <cuenta xsi:type="xsd:string">'.$cuenta.'</cuenta>
                     <usuario xsi:type="xsd:string">'.$usuario.'</usuario>
                     <password xsi:type="xsd:string">'.$password.'</password>
         
                  </request>
               </urn:ObtenerServicios>
            </soapenv:Body>
         </soapenv:Envelope>';

            $headers = array(
                "Content-type: text/xml;charset=\"utf-8\"",
                "Accept: text/xml",
                "Cache-Control: no-cache",
                "Pragma: no-cache",
                "SOAPAction: ",
                "Content-length: " . strlen($xml_post_string),
            );

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
            curl_setopt($ch, CURLOPT_URL, $ws);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string); // the SOAP request
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $response = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpcode == 200) //hubo conexion
            {
                $doc = new DOMDocument();
                $doc->loadXML($response); //lo que tengo en memoria lo convierto en un XML

                return $doc;

            } else {
                echo curl_error($ch);
                echo "</br> Problema de conexión";
            }
        } catch (Exception $e) {
            echo "error web service: " . $e->getMessage();
        }
    }
    static function mdlProcesarTransaccion($cuenta, $usuario, $password,$requestid)
    {
        try {
            $ws = "https://app.sivetel.com:443/index.php/WebService";
            $xml_post_string = '<soapenv:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:urn="urn:WSDL_sivetel">
            <soapenv:Header/>
            <soapenv:Body>
               <urn:ProcesarTransaccion soapenv:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
                  <request xsi:type="urn:Procesar">
                     <!--You may enter the following 4 items in any order-->
                     <cuenta xsi:type="xsd:string">'.$cuenta.'</cuenta>
                     <usuario xsi:type="xsd:string">'.$usuario.'</usuario>
                     <password xsi:type="xsd:string">'.$password.'</password>
                     <requestid xsi:type="xsd:string">'.$requestid.'</requestid>
                  </request>
               </urn:ProcesarTransaccion>
            </soapenv:Body>
         </soapenv:Envelope>';

            $headers = array(
                "Content-type: text/xml;charset=\"utf-8\"",
                "Accept: text/xml",
                "Cache-Control: no-cache",
                "Pragma: no-cache",
                "SOAPAction: ",
                "Content-length: " . strlen($xml_post_string),
            );

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
            curl_setopt($ch, CURLOPT_URL, $ws);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
            curl_setopt($ch, CURLOPT_TIMEOUT, 100);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string); // the SOAP request
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $response = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpcode == 200) //hubo conexion
            {
                $doc = new DOMDocument();
                $doc->loadXML($response); //lo que tengo en memoria lo convierto en un XML
                return $doc;
                
            } else {
                echo curl_error($ch);
                echo "</br> Problema de conexión";
            }
        } catch (Exception $e) {
            echo "error web service: " . $e->getMessage();
        }
    }
    static function mdlReservarTransaccion($cuenta, $usuario, $password,$numero,$producto)
    {
        try {
            $ws = "https://app.sivetel.com:443/index.php/WebService";
            $xml_post_string = '<soapenv:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:urn="urn:WSDL_sivetel">
            <soapenv:Header/>
            <soapenv:Body>
               <urn:ReservarTransaccion soapenv:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
                  <request xsi:type="urn:Reservar">
                     <!--You may enter the following 7 items in any order-->
                     <cuenta xsi:type="xsd:string">'.$cuenta.'</cuenta>
                     <usuario xsi:type="xsd:string">'.$usuario.'</usuario>
                     <password xsi:type="xsd:string">'.$password.'</password>
                     <numero xsi:type="xsd:string">'.$numero.'</numero>
                     <producto xsi:type="xsd:string">'.$producto.'</producto>

                  </request>
               </urn:ReservarTransaccion>
            </soapenv:Body>
         </soapenv:Envelope>';

            $headers = array(
                "Content-type: text/xml;charset=\"utf-8\"",
                "Accept: text/xml",
                "Cache-Control: no-cache",
                "Pragma: no-cache",
                "SOAPAction: ",
                "Content-length: " . strlen($xml_post_string),
            );

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
            curl_setopt($ch, CURLOPT_URL, $ws);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string); // the SOAP request
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $response = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpcode == 200) //hubo conexion
            {
                $doc = new DOMDocument();
                $doc->loadXML($response); //lo que tengo en memoria lo convierto en un XML

                return $doc;
               
            } else {
                echo curl_error($ch);
                echo "</br> Problema de conexión";
            }
        } catch (Exception $e) {
            echo "error web service: " . $e->getMessage();
        }
    }
    static function mdlReservarTransaccionServ($cuenta, $usuario, $password,$referencia,$producto,$monto)
    {
        try {
            $ws = "https://app.sivetel.com:443/index.php/WebService";
            $xml_post_string = '<soapenv:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:urn="urn:WSDL_sivetel">
            <soapenv:Header/>
            <soapenv:Body>
               <urn:ReservarTransaccion soapenv:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
                  <request xsi:type="urn:Reservar">
                     <!--You may enter the following 7 items in any order-->
                     <cuenta xsi:type="xsd:string">'.$cuenta.'</cuenta>
                     <usuario xsi:type="xsd:string">'.$usuario.'</usuario>
                     <password xsi:type="xsd:string">'.$password.'</password>
                     <referencia xsi:type="xsd:string">'.$referencia.'</referencia>
                     <producto xsi:type="xsd:string">'.$producto.'</producto>
                     <monto xsi:type="xsd:string">'.$monto.'</monto>

                  </request>
               </urn:ReservarTransaccion>
            </soapenv:Body>
         </soapenv:Envelope>';

            $headers = array(
                "Content-type: text/xml;charset=\"utf-8\"",
                "Accept: text/xml",
                "Cache-Control: no-cache",
                "Pragma: no-cache",
                "SOAPAction: ",
                "Content-length: " . strlen($xml_post_string),
            );

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
            curl_setopt($ch, CURLOPT_URL, $ws);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string); // the SOAP request
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $response = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpcode == 200) //hubo conexion
            {
                $doc = new DOMDocument();
                $doc->loadXML($response); //lo que tengo en memoria lo convierto en un XML

                return $doc;
               
            } else {
                echo curl_error($ch);
                echo "</br> Problema de conexión";
            }
        } catch (Exception $e) {
            echo "error web service: " . $e->getMessage();
        }
    }
}
?>