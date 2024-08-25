<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (
        isset($data['Nombre'], $data['Apellido'], $data['Correo_Electronico'], $data['Contrasena'], $data['Rol'], $data['cedula'])
    ) {
        $nombre = htmlspecialchars($data['Nombre']);
        $apellido = htmlspecialchars($data['Apellido']);
        $correo = htmlspecialchars($data['Correo_Electronico']);
        $contrasena = htmlspecialchars($data['Contrasena']);
        $rol = htmlspecialchars($data['Rol']);
        $cedula = htmlspecialchars($data['cedula']);

        try {
            // Verifica si el correo electrónico ya está registrado
            $stmt = $conn->prepare("SELECT Correo_Electronico FROM usuarios WHERE Correo_Electronico = ?");
            $stmt->bind_param("s", $correo);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $response = array("success" => false, "message" => "El correo electrónico ya está registrado");
            } else {
                // Encripta la contraseña antes de insertarla
                $hashed_password = password_hash($contrasena, PASSWORD_DEFAULT);

                // Inserta el nuevo usuario en la base de datos
                $stmt = $conn->prepare("INSERT INTO usuarios (Nombre, Apellido, Correo_Electronico, Contrasena, Rol, cedula) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssss", $nombre, $apellido, $correo, $hashed_password, $rol, $cedula);
                $stmt->execute();

                $response = array("success" => true, "message" => "Usuario registrado correctamente");
            }
        } catch (Exception $e) {
            $response = array("success" => false, "message" => "Error de base de datos: " . $e->getMessage());
        }
    } else {
        $response = array("success" => false, "message" => "Faltan datos necesarios para el registro");
    }
} else {
    $response = array("success" => false, "message" => "Método de solicitud no válido");
}

echo json_encode($response);
?>
