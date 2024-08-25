<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require_once 'conexion.php'; // Asegúrate de que este archivo contiene la conexión a tu base de datos

// Obtener id_campania desde los parámetros de la solicitud
$id_campania = isset($_GET['id_campania']) ? intval($_GET['id_campania']) : 0;

if ($id_campania > 0) {
    // Consulta para obtener imágenes
    $query_imagenes = "SELECT id, descripcion, ruta_imagen FROM imagenes WHERE id_campania = $id_campania";
    $result_imagenes = mysqli_query($conn, $query_imagenes);

    if (!$result_imagenes) {
        echo json_encode(["error" => "Error al ejecutar la consulta de imágenes."]);
        exit;
    }

    $imagenes = [];
    while ($row = mysqli_fetch_assoc($result_imagenes)) {
        $imagenes[] = $row;
    }

    // Consulta para obtener videos
    $query_videos = "SELECT id, nombre, ruta FROM videos WHERE id_campania = $id_campania";
    $result_videos = mysqli_query($conn, $query_videos);

    if (!$result_videos) {
        echo json_encode(["error" => "Error al ejecutar la consulta de videos."]);
        exit;
    }

    $videos = [];
    while ($row = mysqli_fetch_assoc($result_videos)) {
        $videos[] = $row;
    }

    // Devolver los resultados como JSON
    echo json_encode([
        "imagenes" => $imagenes,
        "videos" => $videos
    ]);

} else {
    echo json_encode(["error" => "ID de campaña no proporcionado o inválido."]);
}

// Cerrar la conexión
mysqli_close($conn);
?>
