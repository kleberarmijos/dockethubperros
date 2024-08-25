<?php
// Permitir todos los orígenes
header("Access-Control-Allow-Origin: *");

// Permitir los métodos y cabeceras que serán utilizados en las peticiones HTTP
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// Si la solicitud es OPTIONS, termina la ejecución
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Incluir archivo de conexión (debe ser 'conexion.php' si ese es el nombre correcto)
require_once 'conexion.php';

// Obtener datos enviados en formato JSON
$data = json_decode(file_get_contents("php://input"), true);

// Verificar si se recibieron datos válidos
if ($data && isset($data['ID'], $data['estado'])) {
    // Obtener y sanitizar los datos recibidos
    $ID = $data['ID'];
    $estado = $data['estado'];

    try {
        // Sentencia SQL para la actualización del estado
        $sql = "UPDATE reportes_perros_perdidos
                SET estado = ?
                WHERE ID = ?";
        
        // Preparar la consulta
        $stmt = $conn->prepare($sql);

        // Verificar si la preparación de la consulta fue exitosa
        if ($stmt) {
            // Bind parameters
            $stmt->bind_param('si', $estado, $ID);
            
            // Ejecutar la consulta
            $stmt->execute();
            
            // Verificar si se realizó la actualización correctamente
            if ($stmt->affected_rows > 0) {
                echo json_encode(array("message" => "Estado del reporte actualizado correctamente."));
            } else {
                echo json_encode(array("message" => "No se encontró ningún reporte con el ID proporcionado."));
            }
        } else {
            // Si la preparación de la consulta falla
            throw new Exception('Error al preparar la consulta.');
        }
    } catch(Exception $e) {
        // En caso de error, devolver un mensaje de error
        http_response_code(500); // Error interno del servidor
        echo json_encode(array("message" => "Error al actualizar estado del reporte: " . $e->getMessage()));
    }
} else {
    // Si no se recibieron datos válidos
    http_response_code(400); // Bad Request
    echo json_encode(array("message" => "Datos incompletos o incorrectos para actualizar el estado del reporte."));
}

// Cerrar conexión
$conn->close();
?>
