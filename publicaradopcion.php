<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require_once 'conexion.php';

$response = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['id'])) {
        $id = intval($data['id']);

        $query = "UPDATE adopciones SET publicado = TRUE WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Adopción publicada con éxito.';
        } else {
            $response['error'] = 'Error al publicar la adopción.';
        }

        $stmt->close();
    } else {
        $response['error'] = 'ID de adopción no proporcionado.';
    }

    $conn->close();
} else {
    $response['error'] = 'Método de solicitud no permitido.';
}

echo json_encode($response);
?>
