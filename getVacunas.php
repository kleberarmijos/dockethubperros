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

// Incluir archivo de conexión
include 'conexion.php';

// Obtener el parámetro de búsqueda si existe
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

// Consulta SQL para seleccionar todas las enfermedades, con filtro opcional por nombre
$sql = "SELECT * FROM vacunas";
if (!empty($searchTerm)) {
    $sql .= " WHERE Nombre LIKE ?";
}

// Preparar y ejecutar la consulta
$stmt = $conn->prepare($sql);
if (!empty($searchTerm)) {
    $searchTerm = "%$searchTerm%";
    $stmt->bind_param('s', $searchTerm);
}
$stmt->execute();
$result = $stmt->get_result();

// Verificar si hay resultados
if ($result->num_rows > 0) {
    $enfermedades = array();
    // Iterar sobre los resultados y almacenar en un array asociativo
    while ($row = $result->fetch_assoc()) {
        $enfermedades[] = $row;
    }
    // Devolver los datos en formato JSON
    echo json_encode($enfermedades);
} else {
    // Si no hay resultados, devolver un array vacío
    echo json_encode(array());
}

// Cerrar la declaración y la conexión
$stmt->close();
$conn->close();
?>
