<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

include 'conexion.php'; // Incluir archivo de conexión

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $sql = "SELECT * FROM Usuarios";
    $result = $conn->query($sql);

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
    echo json_encode(["message" => "Método no permitido"]);
}
$conn->close();
?>
