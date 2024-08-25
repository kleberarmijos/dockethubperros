<?php
session_start();

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require_once 'conexion.php'; // Conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['id'])) {
        $id = intval($data['id']);
        $query = "UPDATE imagenes SET publicada = TRUE WHERE id = $id";

        if (mysqli_query($conn, $query)) {
            echo json_encode(["mensaje" => "Imagen publicada exitosamente"]);
        } else {
            echo json_encode(["error" => "Error al publicar la imagen"]);
        }
    } else {
        echo json_encode(["error" => "ID de imagen no proporcionado"]);
    }
} else {
    echo json_encode(["error" => "Método de solicitud no permitido"]);
}
?>
