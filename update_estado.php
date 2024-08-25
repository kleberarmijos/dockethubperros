<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require_once 'conexion.php';

$data = json_decode(file_get_contents("php://input"), true);

$id = $data['id'] ?? '';
$estado = $data['estado'] ?? '';

if (empty($id) || empty($estado)) {
    echo json_encode(['success' => false, 'message' => 'ID y estado son requeridos.']);
    exit();
}

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Error en la conexión a la base de datos.']);
    exit();
}

$conn->begin_transaction();

try {
    // Actualizar el estado en solicitudes_adopcion
    $sql_solicitud = "UPDATE solicitudes_adopcion SET estado = ? WHERE id = ?";
    $stmt_solicitud = $conn->prepare($sql_solicitud);
    $stmt_solicitud->bind_param('si', $estado, $id);

    if (!$stmt_solicitud->execute()) {
        throw new Exception("Error al actualizar el estado: " . $stmt_solicitud->error);
    }

    // Obtener el adopcion_id relacionado
    $sql_adopcion_id = "SELECT adopcion_id FROM solicitudes_adopcion WHERE id = ?";
    $stmt_adopcion_id = $conn->prepare($sql_adopcion_id);
    $stmt_adopcion_id->bind_param('i', $id);
    $stmt_adopcion_id->execute();
    $stmt_adopcion_id->bind_result($adopcion_id);
    $stmt_adopcion_id->fetch();
    $stmt_adopcion_id->close();

    // Comprobar si se obtuvo el adopcion_id correctamente
    if (!$adopcion_id) {
        throw new Exception("No se encontró el adopcion_id asociado.");
    } else {
        // Depuración: Mostrar el adopcion_id
        error_log("adopcion_id obtenido: " . $adopcion_id);
    }

    // Actualizar el campo adoptado en adopciones
    $sql_adopciones = "UPDATE adopciones SET adoptado = 1 WHERE id = ?";
    $stmt_adopciones = $conn->prepare($sql_adopciones);
    $stmt_adopciones->bind_param('i', $adopcion_id);

    if ($stmt_adopciones->execute()) {
        // Depuración: Mostrar mensaje de éxito
        error_log("Campo 'adoptado' actualizado correctamente en adopciones.");
    } else {
        throw new Exception("Error al actualizar el campo adoptado: " . $stmt_adopciones->error);
    }

    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Estado actualizado y adopción marcada como completada.']);

} catch (Exception $e) {
    $conn->rollback();
    error_log("Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();
?>
