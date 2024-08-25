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
    try {
        // Preparar la consulta SQL para obtener todas las campañas
        $sql = "SELECT * FROM campanias";
        $result = $conn->query($sql);

        // Verificar si se encontraron resultados
        if ($result->num_rows > 0) {
            // Crear un array para almacenar los datos
            $campanias = array();

            // Recorrer los resultados y agregarlos al array
            while($row = $result->fetch_assoc()) {
                $campanias[] = $row;
            }

            // Devolver los datos como JSON
            echo json_encode(array("success" => true, "data" => $campanias));
        } else {
            // No se encontraron resultados
            echo json_encode(array("success" => false, "message" => "No se encontraron campañas."));
        }
    } catch (Exception $e) {
        // En caso de error, devolver mensaje de error
        echo json_encode(array("success" => false, "message" => "Error de base de datos: " . $e->getMessage()));
    } finally {
        // Cerrar conexión
        $conn->close();
    }
} else {
    // Método de solicitud no válido
    echo json_encode(array("success" => false, "message" => "Método de solicitud no válido"));
}
?>
