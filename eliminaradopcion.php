<?php
session_start();
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtén el contenido de la solicitud POST
    $input = json_decode(file_get_contents('php://input'), true);
    $id = $input['id'] ?? null;

    if ($id) {
        // Primero, obtener la ruta del archivo desde la base de datos
        $query = "SELECT imagen FROM adopciones WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            $stmt->store_result();
            $stmt->bind_result($image);
            $stmt->fetch();

            // Eliminar el archivo del servidor
            if ($image) {
                $filePath = __DIR__ . '/uploads/' . $image; // Ajusta la ruta según tu estructura
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            // Eliminar la entrada de la base de datos
            $stmt->close();

            $query = "DELETE FROM adopciones WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $id);

            if ($stmt->execute()) {
                $response = ["success" => true, "message" => "Adopción eliminada correctamente."];
            } else {
                $response = ["success" => false, "message" => "Error al eliminar la adopción."];
            }

            $stmt->close();
        } else {
            $response = ["success" => false, "message" => "Error al obtener la imagen de la adopción."];
        }

    } else {
        $response = ["success" => false, "message" => "ID de adopción no proporcionado."];
    }
    $conn->close();
} else {
    $response = ["success" => false, "message" => "Método de solicitud no permitido."];
}

echo json_encode($response);
?>
