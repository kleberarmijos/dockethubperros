<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require_once 'conexion.php'; // Conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    // Verifica si el 'id' está presente y es un número entero
    if (isset($data['id']) && is_numeric($data['id'])) {
        $id = intval($data['id']); // Convierte a entero

        // Preparar consulta para actualizar el campo publicado
        $stmt = $conn->prepare("UPDATE videos SET publicado = TRUE WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $id);

            if ($stmt->execute()) {
                echo json_encode(["mensaje" => "Video publicado exitosamente."]);
            } else {
                echo json_encode(["error" => "Error al ejecutar la consulta: " . $stmt->error]);
            }

            $stmt->close();
        } else {
            echo json_encode(["error" => "Error al preparar la consulta: " . $conn->error]);
        }
    } else {
        echo json_encode(["error" => "ID del video no proporcionado o no válido."]);
    }
} else {
    echo json_encode(["error" => "Método de solicitud no permitido."]);
}

$conn->close();
?>
