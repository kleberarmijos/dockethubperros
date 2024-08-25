<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['id'])) {
        $id = $data['id'];

        try {
            // Verifica si el usuario existe
            $stmt = $conn->prepare("SELECT id FROM mascotas WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                // Elimina el usuario de la base de datos
                $stmt = $conn->prepare("DELETE FROM mascotas WHERE id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();

                $response = array("success" => true, "message" => "Usuario eliminado correctamente");
            } else {
                $response = array("success" => false, "message" => "El usuario no existe");
            }
        } catch (Exception $e) {
            $response = array("success" => false, "message" => "Error de base de datos: " . $e->getMessage());
        }
    } else {
        $response = array("success" => false, "message" => "Falta el ID del usuario a eliminar");
    }
} else {
    $response = array("success" => false, "message" => "Método de solicitud no válido");
}

echo json_encode($response);
?>
