<?php
// Permitir todos los orígenes
header("Access-Control-Allow-Origin: *");

// Permitir los métodos y cabeceras que serán utilizados en las peticiones HTTP
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// Si la solicitud es OPTIONS, termina la ejecución
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Incluir archivo de conexión
require_once 'conexion.php';

// Consulta SQL para obtener las rutas de las imágenes
$sql = "SELECT ruta_imagen FROM tabla_imagenes";
$result = $conn->query($sql);

$rutas = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Agregar la ruta de la imagen al arreglo
        $rutas[] = $row['ruta_imagen'];
    }
}

// Devolver las rutas de las imágenes como JSON
$response = [
    "success" => true,
    "imagenes" => $rutas
];

header('Content-Type: application/json');
echo json_encode($response);

// Cerrar conexión a la base de datos
$conn->close();
?>
