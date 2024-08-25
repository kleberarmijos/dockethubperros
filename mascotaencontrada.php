<?php
// Permitir todos los orígenes
header("Access-Control-Allow-Origin: *");

// Permitir los métodos y cabeceras que serán utilizados en las peticiones HTTP
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// Especificar el tipo de contenido de la respuesta
header("Content-Type: application/json");

// Si la solicitud es OPTIONS, termina la ejecución
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Incluir archivo de conexión
require_once 'conexion.php';

// Obtener el ID de usuario desde los parámetros de la solicitud
$idUsuario = isset($_GET['idusuario']) ? intval($_GET['idusuario']) : 0;

// Verificar que se haya proporcionado un ID de usuario válido
if ($idUsuario <= 0) {
    echo json_encode(array("message" => "ID de usuario no válido."));
    exit;
}

// Consulta SQL para obtener reportes de perros perdidos por ID_Usuario y con estado 'encontrada'
$sql = "SELECT * FROM reportes_perros_perdidos WHERE ID_Usuario = ? AND Estado = 'encontrada'";

// Preparar y ejecutar la consulta
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idUsuario); // "i" indica que el parámetro es un entero
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Crear un array para almacenar los reportes
    $reportes = array();

    // Iterar sobre los resultados y agregar cada fila al array de reportes
    while ($row = $result->fetch_assoc()) {
        $reportes[] = $row;
    }

    // Devolver los reportes como JSON
    echo json_encode($reportes);
} else {
    // Si no hay resultados
    echo json_encode(array("message" => "No se encontraron reportes con el estado 'encontrada'."));
}

// Cerrar la conexión
$conn->close();
?>
