<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");

include 'conexion.php'; // Incluir archivo de conexión

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // Verifica si se envió el parámetro de cédula
    if (isset($_GET['cedula'])) {
        $cedula = $_GET['cedula'];

        // Prepara la consulta para buscar el usuario por cédula
        $sql = "SELECT ID, CONCAT(Nombre, ' ', Apellido) AS NombreCompleto FROM usuarios WHERE cedula = ?";
        $stmt = $conn->prepare($sql);

        // Agrega el parámetro y ejecuta la consulta
        $stmt->bind_param("s", $cedula);
        $stmt->execute();
        $result = $stmt->get_result();

        // Verifica si se encontraron resultados
        if ($result->num_rows > 0) {
            $usuarios = array();
            while ($row = $result->fetch_assoc()) {
                $usuarios[] = $row;
            }
            echo json_encode($usuarios);
        } else {
            echo json_encode([]);
        }
    } else {
        echo json_encode(["message" => "Falta el parámetro de cédula"]);
    }
} else {
    echo json_encode(["message" => "Método no permitido"]);
}

$stmt->close();
$conn->close();
?>
