<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

include 'conexion.php'; // Incluye tu archivo de conexión a la base de datos

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = json_decode(file_get_contents("php://input"));

    if (isset($data->Correo_Electronico) && isset($data->Contrasena)) {
        $Correo_Electronico = htmlspecialchars($data->Correo_Electronico);
        $Contrasena = htmlspecialchars($data->Contrasena);

        // Verificar credenciales del usuario
        $sql = "SELECT * FROM Usuarios WHERE Correo_Electronico = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $Correo_Electronico);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $usuario = $result->fetch_assoc();

            // Verificar la contraseña
            if (password_verify($Contrasena, $usuario['Contrasena'])) {
                // Contraseña correcta
                // Obtener el rol del usuario
                $rol = $usuario['Rol']; // Asumiendo que 'Rol' es el campo que contiene el rol del usuario
                $id = $usuario['ID'];
                http_response_code(200);
                echo json_encode(["message" => "Inicio de sesión exitoso.", "rol" => $rol, "usuario" => $usuario]);
            } else {
                // Contraseña incorrecta
                http_response_code(401); // Unauthorized
                echo json_encode(["error" => "La contraseña es incorrecta."]);
            }
        } else {
            // Usuario no encontrado
            http_response_code(404); // Not Found
            echo json_encode(["error" => "El usuario no existe."]);
        }
        $stmt->close();
    } else {
        // Datos incompletos
        http_response_code(400); // Bad Request
        echo json_encode(["error" => "Por favor, proporcione correo electrónico y contraseña."]);
    }
}
$conn->close();
?>
