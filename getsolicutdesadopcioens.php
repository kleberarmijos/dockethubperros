<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

require_once 'conexion.php'; // Asegúrate de tener este archivo de conexión

// Crear una nueva conexión a la base de datos
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar si la conexión fue exitosa
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Error en la conexión a la base de datos.']);
    exit();
}

// Consulta para obtener las solicitudes de adopción con estado 'pendiente'
$sql = "SELECT * FROM solicitudes_adopcion WHERE estado = 'pendiente'";
$result = $conn->query($sql);

// Array para almacenar las solicitudes
$solicitudes = [];

// Verificar si la consulta tiene resultados
if ($result->num_rows > 0) {
    // Convertir los resultados en un array
    while ($row = $result->fetch_assoc()) {
        $solicitudes[] = $row;
    }
    // Devolver los resultados en formato JSON
    echo json_encode(['success' => true, 'data' => $solicitudes]);
} else {
    // Si no hay resultados, devolver un mensaje de error
    echo json_encode(['success' => false, 'message' => 'No se encontraron solicitudes pendientes.']);
}

// Cerrar la conexión
$conn->close();
?>
