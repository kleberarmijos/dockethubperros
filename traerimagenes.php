<?php
session_start();

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require_once 'conexion.php'; // Conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $query = "SELECT id, descripcion, ruta_imagen,id_campania FROM imagenes";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $imagenes = mysqli_fetch_all($result, MYSQLI_ASSOC);
        echo json_encode(['imagenes' => $imagenes]);
    } else {
        echo json_encode(["error" => "Error al recuperar imágenes"]);
    }
} else {
    echo json_encode(["error" => "Método de solicitud no permitido"]);
}
?>
