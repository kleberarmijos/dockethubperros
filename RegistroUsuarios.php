<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = json_decode(file_get_contents("php://input"));

    if (isset($data->Nombre) && isset($data->Apellido) && isset($data->Correo_Electronico) && isset($data->Contrasena) && isset($data->cedula)) {
        $Nombre = htmlspecialchars($data->Nombre);
        $Apellido = htmlspecialchars($data->Apellido);
        $Correo_Electronico = htmlspecialchars($data->Correo_Electronico);
        $Contrasena = htmlspecialchars($data->Contrasena);
        $cedula = intval($data->cedula);

        // Verificar que el correo electrónico no esté ya registrado
        $sql = "SELECT * FROM Usuarios WHERE Correo_Electronico = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $Correo_Electronico);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            http_response_code(400); // Devuelve un código de estado 400 si el correo electrónico ya está registrado
            echo json_encode(["message" => "El correo electrónico ya está registrado."]);
        } else {
            $hashed_password = password_hash($Contrasena, PASSWORD_DEFAULT);

            $sql = "INSERT INTO Usuarios (Nombre, Apellido, Correo_Electronico, Contrasena, cedula) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssi", $Nombre, $Apellido, $Correo_Electronico, $hashed_password, $cedula);

            if ($stmt->execute()) {
                echo json_encode(["message" => "Registro exitoso."]);
            } else {
                http_response_code(500); // Devuelve un código de estado 500 si hay un error en la ejecución de la consulta
                echo json_encode(["message" => "Error: " . $sql . "<br>" . $conn->error]);
            }
        }
        $stmt->close();
    } else {
        http_response_code(400); // Devuelve un código de estado 400 si no se completaron todos los campos
        echo json_encode(["message" => "Por favor, complete todos los campos."]);
    }
}
$conn->close();
?>
