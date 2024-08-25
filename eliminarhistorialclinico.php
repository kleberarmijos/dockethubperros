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

// Incluir el archivo de conexión
include 'conexion.php';

// Verificar si se proporcionó el parámetro 'id' en la URL
if (!isset($_GET['id'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Falta el parámetro ID.']);
    exit;
}

// Obtener el ID del parámetro
$id = intval($_GET['id']);

// Preparar la consulta SQL para eliminar el registro
$sql = "DELETE FROM historial_medico WHERE ID = ?";

// Preparar la declaración y enlazar los parámetros
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

// Ejecutar la declaración
if ($stmt->execute()) {
    echo json_encode(['message' => 'Registro eliminado correctamente.']);
} else {
    echo json_encode(['error' => 'Error al eliminar el registro: ' . $stmt->error]);
}

// Cerrar la conexión
$stmt->close();
$conn->close();
?>
