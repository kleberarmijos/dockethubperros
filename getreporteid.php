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
    // Verificar si se proporcionó un ID de reporte en la solicitud GET
    if (!isset($_GET['id'])) {
        throw new Exception('ID de reporte no proporcionado');
    }

    // Obtener el ID de reporte desde la solicitud GET
    $idReporte = $_GET['id'];

    // Sentencia SQL para seleccionar el reporte por su ID
    $sql = "SELECT * FROM reportes_perros_perdidos WHERE ID = ?";

    // Preparar la consulta
    $stmt = $conn->prepare($sql);

    // Vincular parámetros
    $stmt->bind_param("i", $idReporte);

    // Ejecutar la consulta
    $stmt->execute();

    // Obtener resultados
    $result = $stmt->get_result();

    // Verificar si se encontró el reporte
    if ($result->num_rows > 0) {
        // Obtener el reporte como un arreglo asociativo
        $reporte = $result->fetch_assoc();

        // Devolver el reporte en formato JSON
        echo json_encode($reporte);
    } else {
        // Si no se encuentra el reporte
        http_response_code(404); // No encontrado
        echo json_encode(array("message" => "No se encontró el reporte con ID: $idReporte"));
    }
} catch(Exception $e) {
    // En caso de error, devolver un mensaje de error
    http_response_code(500); // Error interno del servidor
    echo json_encode(array("message" => "Error al obtener el reporte: " . $e->getMessage()));
}

// Cerrar conexión
$conn->close();
?>
