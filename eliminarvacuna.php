<?php
// Permitir todos los orígenes
header("Access-Control-Allow-Origin: *");

// Permitir los métodos y cabeceras que serán utilizados en las peticiones HTTP
header("Access-Control-Allow-Methods: DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// Si la solicitud es OPTIONS, termina la ejecución
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// El resto de tu código PHP para procesar la solicitud DELETE
include 'conexion.php';

// Recibir datos JSON de la solicitud
$data = json_decode(file_get_contents("php://input"));

// Validar y limpiar datos
$id = intval($data->id ?? 0);

// Verificar que el ID no esté vacío
if (empty($id)) {
    $response = ['error' => 'El ID es obligatorio.'];
    http_response_code(400); // Bad Request
    echo json_encode($response);
    exit; // Terminar la ejecución del script
}

// Preparar la consulta SQL
$sql = "DELETE FROM vacunas WHERE ID = ?";

// Preparar la declaración y enlazar los parámetros
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

// Ejecutar la declaración
if ($stmt->execute()) {
    $response = ['message' => 'Datos eliminados correctamente.'];
    echo json_encode($response);
} else {
    $response = ['error' => 'Error al eliminar datos.'];
    echo json_encode($response);
}

// Cerrar la conexión
$stmt->close();
$conn->close();
?>
