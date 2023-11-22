<?php
header('Content-Type: application/xml');

// Configuración de la base de datos
$dbHost = 'localhost';
$dbUser = 'tu_usuario';
$dbPassword = 'tu_contraseña';
$dbName = 'tu_base_de_datos';

// Crear conexión a la base de datos con manejo de errores
try {
    $db = new mysqli($dbHost, $dbUser, $dbPassword, $dbName);

    // Verificar la conexión
    if ($db->connect_error) {
        throw new Exception("Error de conexión: " . $db->connect_error);
    }

    // Obtén el dominio de la solicitud
    $domain = $_SERVER['HTTP_HOST']; // Ajusta esto según tu lógica de obtención del dominio

    // Consulta segura a la base de datos
    $query = $db->prepare("SELECT * FROM email_configurations WHERE domain = ?");
    $query->bind_param("s", $domain);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        $config = $result->fetch_assoc();
        echo buildXmlResponse($config);
    } else {
        echo "No se encontró configuración para el dominio solicitado.";
    }

    $db->close();
} catch (Exception $e) {
    // Manejo de errores
    echo "Error: " . $e->getMessage();
}

// Función para construir la respuesta XML
function buildXmlResponse($config) {
    $response = "<?xml version='1.0' encoding='UTF-8'?>";
    $response .= "<Autodiscover xmlns='http://schemas.microsoft.com/exchange/autodiscover/responseschema/2006'>";
    $response .= "<Response>";
    $response .= "<Account>";
    $response .= "<AccountType>email</AccountType>";
    $response .= "<Action>settings</Action>";

    // Agrega aquí más protocolos según sea necesario
    $response .= buildProtocolXml('IMAP', $config);
    $response .= buildProtocolXml('SMTP', $config);

    $response .= "</Account>";
    $response .= "</Response>";
    $response .= "</Autodiscover>";

    return $response;
}

// Función para construir XML para cada protocolo
function buildProtocolXml($type, $config) {
    $protocolXml = "<Protocol>";
    $protocolXml .= "<Type>".$type."</Type>";
    $protocolXml .= "<Server>".$config[strtolower($type) . '_server']."</Server>";
    $protocolXml .= "<Port>".$config[strtolower($type) . '_port']."</Port>";
    $protocolXml .= "<SSL>".$config['ssl']."</SSL>";
    $protocolXml .= "<LoginName>".$config['username']."</LoginName>";
    $protocolXml .= "</Protocol>";

    return $protocolXml;
}
?>
