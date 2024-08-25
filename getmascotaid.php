<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['ID_Usuario'])) {
        $idUsuario = $_GET['ID_Usuario'];

        try {
            // Preparar la consulta para obtener mascotas por ID_Usuario
            $stmt = $conn->prepare("SELECT * FROM Mascotas WHERE ID_Usuario = ?");
            $stmt->bind_param("i", $idUsuario);
            $stmt->execute();
            $result = $stmt->get_result();
            $mascotas = array();

            while ($row = $result->fetch_assoc()) {
                $mascotas[] = $row;
            }

            $stmt->close();

            if (count($mascotas) > 0) {
                $response = array("success" => true, "mascotas" => $mascotas);
            } else {
                $response = array("success" => false, "message" => "No se encontraron mascotas para el ID_Usuario proporcionado");
            }
        } catch (Exception $e) {
            $response = array("success" => false, "message" => "Error de base de datos: " . $e->getMessage());
        }
    } else {
        $response = array("success" => false, "message" => "ID_Usuario no proporcionado");
    }
} else {
    $response = array("success" => false, "message" => "Método de solicitud no válido");
}

echo json_encode($response);
?>
