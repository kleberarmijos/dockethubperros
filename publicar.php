<?php
// Establecer el tipo de contenido y los encabezados CORS
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Incluir el archivo de conexión a la base de datos
require_once 'conexion.php'; // Asegúrate de tener tu archivo de conexión a la base de datos

// Manejar la solicitud
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar si se ha recibido el ID
    if (isset($_POST['ID'])) {
        $id = intval($_POST['ID']); // Asegurarse de que el ID sea un número entero

        try {
            // Preparar la consulta SQL para actualizar el estado
            $sql = "UPDATE campanias SET estado = 'aprobada' WHERE ID = ?";
            $stmt = $conn->prepare($sql);

            // Verificar si la preparación de la consulta fue exitosa
            if ($stmt === false) {
                throw new Exception("Error al preparar la consulta: " . $conn->error);
            }

            // Enlazar el parámetro y ejecutar la consulta
            $stmt->bind_param('i', $id);
            $stmt->execute();

            // Verificar si se actualizó alguna fila
            if ($stmt->affected_rows > 0) {
                echo json_encode(array("success" => true, "message" => "La Campaña ha sido publicada'"));
            } else {
                echo json_encode(array("success" => false, "message" => "No se encontró una campaña con el ID proporcionado o el estado ya estaba 'publicado'"));
            }

            // Cerrar la declaración
            $stmt->close();
        } catch (Exception $e) {
            // En caso de error, devolver mensaje de error
            echo json_encode(array("success" => false, "message" => "Error de base de datos: " . $e->getMessage()));
        } finally {
            // Cerrar conexión
            $conn->close();
        }
    } else {
        // ID no proporcionado
        echo json_encode(array("success" => false, "message" => "ID de campaña no proporcionado"));
    }
} else {
    // Método de solicitud no válido
    echo json_encode(array("success" => false, "message" => "Método de solicitud no válido"));
}
?>
