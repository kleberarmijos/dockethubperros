<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require_once 'conexion.php'; // Asegúrate de tener tu archivo de conexión a la base de datos

$response = array();

$query = "SELECT * FROM adopciones";
$result = mysqli_query($conn, $query);

if ($result) {
    $adopciones = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $adopciones[] = $row;
    }
    $response['adopciones'] = $adopciones;
} else {
    $response['error'] = 'Error al obtener las adopciones.';
}

echo json_encode($response);

mysqli_close($conn);
?>
