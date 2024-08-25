<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // Obtener el ID del usuario desde los parámetros de la consulta
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        // Preparar la consulta para obtener los datos del usuario
        $sql = "SELECT * FROM Usuarios WHERE ID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Recuperar los datos del usuario
            $usuario = $result->fetch_assoc();
            http_response_code(200); // Código de estado 200 OK
            echo json_encode($usuario);
        } else {
            http_response_code(404); // Código de estado 404 No Encontrado
            echo json_encode(["message" => "Usuario no encontrado."]);
        }

        $stmt->close();
    } else {
        http_response_code(400); // Código de estado 400 Solicitud Incorrecta
        echo json_encode(["message" => "ID de usuario no proporcionado."]);
    }
}
$conn->close();
?>
