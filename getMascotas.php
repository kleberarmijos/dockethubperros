<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        // Obtener todas las mascotas de la base de datos
        $result = $conn->query("SELECT * FROM Mascotas");
        $mascotas = array();

        while ($row = $result->fetch_assoc()) {
            $mascotas[] = $row;
        }

        $response = array("success" => true, "mascotas" => $mascotas);
    } catch (Exception $e) {
        $response = array("success" => false, "message" => "Error de base de datos: " . $e->getMessage());
    }
} else {
    $response = array("success" => false, "message" => "Método de solicitud no válido");
}

echo json_encode($response);
?>
