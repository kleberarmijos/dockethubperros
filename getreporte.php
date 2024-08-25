<?php
// Permitir todos los orígenes
header("Access-Control-Allow-Origin: *");

// Permitir los métodos y cabeceras que serán utilizados en las peticiones HTTP
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// Si la solicitud es OPTIONS, termina la ejecución
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Incluir archivo de conexión (debe ser 'conexion.php' si ese es el nombre correcto)
require_once 'conexion.php';

try {
    // Sentencia SQL para seleccionar reportes donde el estado sea 'perdida'
    $sql = "SELECT * FROM reportes_perros_perdidos WHERE estado = 'perdida'";
    
    // Preparar la consulta
    $stmt = $conn->prepare($sql);
    
    // Ejecutar la consulta
    $stmt->execute();
    
    // Obtener resultados
    $result = $stmt->get_result();
    
    // Verificar si hay resultados
    if ($result->num_rows > 0) {
        // Array para almacenar los reportes
        $reportes = array();
        
        // Iterar sobre los resultados y añadirlos al array
        while ($row = $result->fetch_assoc()) {
            $reportes[] = $row;
        }
        
        // Devolver los reportes en formato JSON
        echo json_encode($reportes);
    } else {
        // Si no hay resultados
        echo json_encode(array("message" => "No se encontraron reportes con estado 'perdida'."));
    }
} catch(Exception $e) {
    // En caso de error, devolver un mensaje de error
    http_response_code(500); // Error interno del servidor
    echo json_encode(array("message" => "Error al obtener reportes: " . $e->getMessage()));
}

// Cerrar conexión
$conn->close();
?>
