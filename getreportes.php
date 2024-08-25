<?php
// Permitir todos los orígenes
header("Access-Control-Allow-Origin: *");

// Permitir los métodos y cabeceras que serán utilizados en las peticiones HTTP
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// Si la solicitud es OPTIONS, termina la ejecución
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Incluir archivo de conexión
require_once 'conexion.php';

// Consulta SQL para obtener todos los reportes de perros perdidos
$sql = "SELECT * FROM reportes_perros_perdidos";

// Ejecutar la consulta
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Crear un array para almacenar los reportes
    $reportes = array();

    // Iterar sobre los resultados y agregar cada fila al array de reportes
    while ($row = $result->fetch_assoc()) {
        $reportes[] = $row;
    }

    // Devolver los reportes como JSON
    echo json_encode($reportes);
} else {
    // Si no hay resultados
    echo json_encode(array("message" => "No se encontraron reportes."));
}

// Cerrar la conexión
$conn->close();
?>