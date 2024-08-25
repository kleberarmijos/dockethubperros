<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require_once 'conexion.php'; // Incluye el archivo de conexión

// Obtener los datos del formulario
$data = json_decode(file_get_contents("php://input"), true);

$cedula = $data['cedula'] ?? '';
$nombres_completos = $data['nombres_completos'] ?? '';
$parroquia = $data['parroquia'] ?? '';
$direccion = $data['direccion'] ?? '';
$telefono = $data['telefono'] ?? '';
$adopcion_id = $data['adopcion_id'] ?? '';

// Validar los datos
if (empty($cedula) || empty($nombres_completos)) {
    echo json_encode(['success' => false, 'message' => 'Cédula y nombres completos son requeridos.']);
    exit();
}

// Preparar y ejecutar la consulta SQL
$sql = "INSERT INTO solicitudes_adopcion (cedula, nombres_completos, parroquia, direccion, telefono, adopcion_id) 
        VALUES (?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param('sssssi', $cedula, $nombres_completos, $parroquia, $direccion, $telefono, $adopcion_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Solicitud enviada con éxito.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al enviar la solicitud.']);
}

// Cerrar la conexión
$stmt->close();
$conn->close();
?>
