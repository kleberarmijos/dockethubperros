<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (
        isset($data['ID'], $data['Nombre'], $data['Apellido'], $data['Correo_Electronico'], $data['Contrasena'], $data['Rol'], $data['cedula'])
    ) {
        $id = $data['ID'];
        $nombre = $data['Nombre'];
        $apellido = $data['Apellido'];
        $correo = $data['Correo_Electronico'];
        $contrasena = $data['Contrasena'];
        $rol = $data['Rol'];
        $cedula = $data['cedula'];

        try {
            // Verifica si el usuario existe
            $stmt = $conn->prepare("SELECT ID FROM usuarios WHERE ID = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                // Actualiza los datos del usuario en la base de datos
                $stmt = $conn->prepare("UPDATE usuarios SET Nombre = ?, Apellido = ?, Correo_Electronico = ?, Contrasena = ?, Rol = ?, cedula = ? WHERE ID = ?");
                $stmt->bind_param("ssssssi", $nombre, $apellido, $correo, $contrasena, $rol, $cedula, $id);
                $stmt->execute();

                $response = array("success" => true, "message" => "Usuario actualizado correctamente");
            } else {
                $response = array("success" => false, "message" => "El usuario no existe");
            }
        } catch (Exception $e) {
            $response = array("success" => false, "message" => "Error de base de datos: " . $e->getMessage());
        }
    } else {
        $response = array("success" => false, "message" => "Faltan datos necesarios para actualizar el usuario");
    }
} else {
    $response = array("success" => false, "message" => "Método de solicitud no válido");
}

echo json_encode($response);
?>
