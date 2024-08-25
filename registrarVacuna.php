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

// El resto de tu código PHP para procesar la solicitud POST
include 'conexion.php';

// Recibir datos JSON de la solicitud
$data = json_decode(file_get_contents("php://input"));

// Validar y limpiar datos
$nombre = htmlspecialchars($data->nombre ?? '');
$descripcion = htmlspecialchars($data->descripcion ?? '');

// Verificar que los datos no estén vacíos
if (empty($nombre) || empty($descripcion)) {
    $response = ['error' => 'Los campos nombre y descripción son obligatorios.'];
    http_response_code(400); // Bad Request
    echo json_encode($response);
    exit; // Terminar la ejecución del script
}

// Preparar la consulta SQL
$sql = "INSERT INTO vacunas (Nombre, Descripcion) VALUES (?, ?)";

// Preparar la declaración y enlazar los parámetros
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $nombre, $descripcion);

// Ejecutar la declaración
if ($stmt->execute()) {
    $response = ['message' => 'Datos insertados correctamente.'];
    echo json_encode($response);
} else {
    $response = ['error' => 'Error al insertar datos.'];
    echo json_encode($response);
}

// Cerrar la conexión
$stmt->close();
$conn->close();
?>
