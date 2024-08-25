<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require_once 'conexion.php'; // Conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $id = intval($_GET['id']);
    if ($id) {
        // Preparar consulta para obtener la ruta del archivo
        $stmt = $conn->prepare("SELECT ruta FROM videos WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $video = $result->fetch_assoc();

        if ($video) {
            $ruta_completa = $video['ruta'];

            // Eliminar el archivo del servidor
            if (file_exists($ruta_completa)) {
                if (unlink($ruta_completa)) {
                    // Eliminar el registro de la base de datos
                    $stmt = $conn->prepare("DELETE FROM videos WHERE id = ?");
                    $stmt->bind_param("i", $id);
                    if ($stmt->execute()) {
                        echo json_encode(["mensaje" => "Video eliminado exitosamente."]);
                    } else {
                        echo json_encode(["error" => "Error al eliminar el video de la base de datos."]);
                    }
                } else {
                    echo json_encode(["error" => "Error al eliminar el archivo del servidor."]);
                }
            } else {
                echo json_encode(["error" => "El archivo no se encuentra en el servidor."]);
            }
        } else {
            echo json_encode(["error" => "Video no encontrado."]);
        }
    } else {
        echo json_encode(["error" => "ID del video no proporcionado."]);
    }
} else {
    echo json_encode(["error" => "Método de solicitud no permitido."]);
}
?>
