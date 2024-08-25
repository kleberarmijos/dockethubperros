<?php
session_start();
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
require_once 'conexion.php';

$response = ["success" => false, "message" => "Método de solicitud no permitido."];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $nombre = $_POST['nombre'] ?? null;
    $descripcion = $_POST['descripcion'] ?? null;
    $edad = $_POST['edad'] ?? null;
    $raza = $_POST['raza'] ?? null;
    $salud = $_POST['salud'] ?? null;

    $updateFields = [];
    $updateValues = [];

    // Verificar si se envió una nueva imagen
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == UPLOAD_ERR_OK) {
        $imagen = $_FILES['imagen']['name'];
        $temp = $_FILES['imagen']['tmp_name'];
        $uploadFileDir = './uploads/';
        $dest_path = $uploadFileDir . basename($imagen);
        if (move_uploaded_file($temp, $dest_path)) {
            $imagen = $imagen;
            $updateFields[] = "imagen = ?";
            $updateValues[] = $imagen;
        } else {
            $imagen = null; // Error en la subida del archivo
        }
    }

    // Verificar y agregar otros campos si están definidos
    if ($nombre !== null) {
        $updateFields[] = "nombre = ?";
        $updateValues[] = $nombre;
    }
    if ($descripcion !== null) {
        $updateFields[] = "descripcion = ?";
        $updateValues[] = $descripcion;
    }
    if ($edad !== null) {
        $updateFields[] = "edad = ?";
        $updateValues[] = $edad;
    }
    if ($raza !== null) {
        $updateFields[] = "raza = ?";
        $updateValues[] = $raza;
    }
    if ($salud !== null) {
        $updateFields[] = "salud = ?";
        $updateValues[] = $salud;
    }

    if ($id && !empty($updateFields)) {
        // Construir la consulta SQL
        $query = "UPDATE adopciones SET " . implode(", ", $updateFields) . " WHERE id = ?";
        $stmt = $conn->prepare($query);

        // Agregar el ID al final de los parámetros
        $updateValues[] = $id;

        // Determinar los tipos de los parámetros
        $types = str_repeat('s', count($updateValues) - 1) . 'i'; // 's' para strings y 'i' para integer (ID)
        $stmt->bind_param($types, ...$updateValues);

        if ($stmt->execute()) {
            $response = ["success" => true, "message" => "Adopción actualizada correctamente."];
        } else {
            $response = ["success" => false, "message" => "Error al actualizar la adopción."];
        }
        $stmt->close();
    } else {
        $response = ["success" => false, "message" => "Datos incompletos o inválidos."];
    }

    $conn->close();
}

echo json_encode($response);
?>
