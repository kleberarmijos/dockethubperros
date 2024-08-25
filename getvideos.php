<?php
session_start();

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require_once 'conexion.php'; // Conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $query = "SELECT * FROM videos";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $videos = mysqli_fetch_all($result, MYSQLI_ASSOC);
        echo json_encode(["videos" => $videos]);
    } else {
        echo json_encode(["error" => "Error al obtener videos."]);
    }
} else {
    echo json_encode(["error" => "Método de solicitud no permitido."]);
}
?>
