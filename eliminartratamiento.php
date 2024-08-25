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

// Validar y limpiar el dato
$id = htmlspecialchars($data->id ?? '');

// Verificar que el ID no esté vacío
if (empty($id)) {
    $response = ['error' => 'El campo ID es obligatorio.'];
    http_response_code(400); // Bad Request
    echo json_encode($response);
    exit; // Terminar la ejecución del script
}

// Preparar la consulta SQL para eliminar el registro
$sql = "DELETE FROM tratamientos WHERE id = ?";

// Preparar la declaración y enlazar el parámetro
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

// Ejecutar la declaración
if ($stmt->execute()) {
    // Verificar si se eliminó algún registro
    if ($stmt->affected_rows > 0) {
        $response = ['message' => 'Registro eliminado correctamente.'];
    } else {
        $response = ['error' => 'No se encontró ningún registro con el ID proporcionado.'];
        http_response_code(404); // Not Found
    }
    echo json_encode($response);
} else {
    $response = ['error' => 'Error al eliminar el registro.'];
    http_response_code(500); // Internal Server Error
    echo json_encode($response);
}

// Cerrar la conexión
$stmt->close();
$conn->close();
?>
