<?php
// Establecer el tipo de contenido y los encabezados CORS
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Incluir el archivo de conexión a la base de datos
require_once 'conexion.php'; // Asegúrate de tener tu archivo de conexión a la base de datos

// Manejar la solicitud
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id'])) {
        // Obtener el ID de la campaña desde los parámetros de la URL
        $id = intval($_GET['id']);

        try {
            // Preparar la consulta SQL para obtener la campaña por ID
            $sql = "SELECT * FROM campanias WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id); // Enlazar el parámetro ID como entero
            $stmt->execute();
            $result = $stmt->get_result();

            // Verificar si se encontró la campaña
            if ($result->num_rows > 0) {
                $campania = $result->fetch_assoc();
                // Devolver los datos de la campaña como JSON
                echo json_encode(array("success" => true, "data" => $campania));
            } else {
                // No se encontró la campaña con ese ID
                echo json_encode(array("success" => false, "message" => "Campaña no encontrada."));
            }

            $stmt->close();
        } catch (Exception $e) {
            // En caso de error, devolver mensaje de error
            echo json_encode(array("success" => false, "message" => "Error de base de datos: " . $e->getMessage()));
        } finally {
            // Cerrar conexión
            $conn->close();
        }
    } else {
        // Parámetro ID no proporcionado
        echo json_encode(array("success" => false, "message" => "ID de campaña no proporcionado."));
    }
} else {
    // Método de solicitud no válido
    echo json_encode(array("success" => false, "message" => "Método de solicitud no válido"));
}
?>
