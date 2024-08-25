<?php
// Establecer el tipo de contenido y los encabezados CORS
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Incluir el archivo de conexión a la base de datos
require_once 'conexion.php';

// Manejar la solicitud
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Obtener el cuerpo de la solicitud
    $input = json_decode(file_get_contents("php://input"), true);

    // Verificar si el ID de la campaña fue proporcionado
    if (isset($input['id'])) {
        $id = $input['id'];

        // Preparar la consulta SQL para eliminar la campaña
        $sql = "DELETE FROM campanias WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);

        // Intentar ejecutar la consulta
        if ($stmt->execute()) {
            echo json_encode(array("success" => true, "message" => "Campaña eliminada exitosamente."));
        } else {
            echo json_encode(array("success" => false, "message" => "Error al eliminar la campaña."));
        }

        // Cerrar la declaración
        $stmt->close();
    } else {
        echo json_encode(array("success" => false, "message" => "ID de campaña no proporcionado."));
    }

    // Cerrar conexión
    $conn->close();
} else {
    echo json_encode(array("success" => false, "message" => "Método de solicitud no válido."));
}
?>
