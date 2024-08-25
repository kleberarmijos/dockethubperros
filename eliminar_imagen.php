<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require_once 'conexion.php'; // Conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true); // Obtener datos del cuerpo de la solicitud

    if (isset($data['id'])) {
        $imagenId = intval($data['id']);

        // Verificar si la imagen existe y obtener su ruta
        $query = "SELECT ruta_imagen FROM imagenes WHERE id = $imagenId";
        $result = mysqli_query($conn, $query);

        if ($result && $row = mysqli_fetch_assoc($result)) {
            $rutaImagen = $row['ruta_imagen'];

            // Eliminar archivo del servidor
            if (unlink("uploads/$rutaImagen")) {
                // Eliminar registro de la base de datos
                $deleteQuery = "DELETE FROM imagenes WHERE id = $imagenId";
                if (mysqli_query($conn, $deleteQuery)) {
                    echo json_encode(["success" => true, "mensaje" => "Imagen eliminada exitosamente."]);
                } else {
                    echo json_encode(["success" => false, "error" => "Error al eliminar el registro de la base de datos."]);
                }
            } else {
                echo json_encode(["success" => false, "error" => "Error al eliminar el archivo del servidor."]);
            }
        } else {
            echo json_encode(["success" => false, "error" => "Imagen no encontrada."]);
        }
    } else {
        echo json_encode(["success" => false, "error" => "No se proporcionó el ID de la imagen."]);
    }
} else {
    echo json_encode(["success" => false, "error" => "Método de solicitud no permitido."]);
}
?>
