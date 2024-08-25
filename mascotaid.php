<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require_once 'conexion.php'; // Asegúrate de que la conexión a la base de datos esté correctamente configurada

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['ID'])) {
        $idMascota = intval($_GET['ID']); // Asegúrate de que ID sea un entero

        try {
            // Preparar la consulta para obtener una mascota por ID
            $stmt = $conn->prepare("SELECT * FROM mascotas WHERE ID = ?");
            $stmt->bind_param("i", $idMascota);
            $stmt->execute();
            $result = $stmt->get_result();
            $mascota = $result->fetch_assoc();

            $stmt->close();

            if ($mascota) {
                $response = array("success" => true, "mascota" => $mascota);
            } else {
                $response = array("success" => false, "message" => "No se encontró una mascota con el ID proporcionado");
            }
        } catch (Exception $e) {
            $response = array("success" => false, "message" => "Error de base de datos: " . $e->getMessage());
        }
    } else {
        $response = array("success" => false, "message" => "ID no proporcionado");
    }
} else {
    $response = array("success" => false, "message" => "Método de solicitud no válido");
}

echo json_encode($response);
?>
