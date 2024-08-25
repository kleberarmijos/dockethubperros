<?php
session_start();
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
require_once 'conexion.php'; // Incluye tu archivo de conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $id = $_GET['id'] ?? null;

    if ($id) {
        $query = "SELECT * FROM adopciones WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $adopcion = $result->fetch_assoc();
                $response = ["success" => true, "adopcion" => $adopcion];
            } else {
                $response = ["success" => false, "message" => "Adopción no encontrada."];
            }
        } else {
            $response = ["success" => false, "message" => "Error al ejecutar la consulta."];
        }

        $stmt->close();
    } else {
        $response = ["success" => false, "message" => "ID de adopción no proporcionado."];
    }

    $conn->close();
} else {
    $response = ["success" => false, "message" => "Método de solicitud no permitido."];
}

echo json_encode($response);
?>
