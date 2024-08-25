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
    // Verificar si se ha proporcionado un ID
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']); // Convertir a entero para evitar inyecciones SQL

        try {
            // Preparar la consulta SQL para obtener una campaña por ID
            $stmt = $conn->prepare("SELECT * FROM campanias WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();

            // Verificar si se encontró una campaña
            if ($result->num_rows > 0) {
                // Obtener los datos de la campaña
                $campania = $result->fetch_assoc();

                // Devolver los datos como JSON
                echo json_encode(array("success" => true, "data" => $campania));
            } else {
                // No se encontró la campaña con el ID proporcionado
                echo json_encode(array("success" => false, "message" => "No se encontró la campaña con el ID proporcionado."));
            }
        } catch (Exception $e) {
            // En caso de error, devolver mensaje de error
            echo json_encode(array("success" => false, "message" => "Error de base de datos: " . $e->getMessage()));
        } finally {
            // Cerrar declaración y conexión
            $stmt->close();
            $conn->close();
        }
    } else {
        // No se proporcionó un ID
        echo json_encode(array("success" => false, "message" => "ID no proporcionado."));
    }
} else {
    // Método de solicitud no válido
    echo json_encode(array("success" => false, "message" => "Método de solicitud no válido"));
}
?>
