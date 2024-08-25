<?php
session_start();
header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Incluir archivo de conexión
require_once 'conexion.php';

// Verifica que sea una solicitud POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Decodifica el JSON a un array asociativo
    $data = json_decode(file_get_contents('php://input'), true);

    // Verifica que se haya enviado el parámetro "Cedula"
    if (isset($data['Cedula'])) {
        $cedula_dueno = $data['Cedula'];

        // Realiza la consulta para buscar la mascota por cedula del dueño y obtener el nombre del dueño
        $sql = "SELECT m.ID AS ID_Mascota, m.Especie, m.Raza, m.Nombre AS NombreMascota, m.Sexo, m.Edad, m.Color, m.Peso, m.Foto,
                       u.ID AS IDDueno, u.Nombre AS NombreDueno, u.Apellido AS ApellidoDueno
                FROM mascotas m
                INNER JOIN usuarios u ON m.ID_Usuario = u.ID
                WHERE u.cedula = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $cedula_dueno);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $mascotas = array();
            while ($row = $result->fetch_assoc()) {
                $mascotas[] = array(
                    "ID_Mascota" => $row['ID_Mascota'],
                    "Especie" => $row['Especie'],
                    "Raza" => $row['Raza'],
                    "NombreMascota" => $row['NombreMascota'],
                    "Sexo" => $row['Sexo'],
                    "Edad" => $row['Edad'],
                    "Color" => $row['Color'],
                    "Peso" => $row['Peso'],
                    "Foto" => $row['Foto'] ? 'http://localhost/VECTOM/uploads/' . $row['Foto'] : '', // Asumiendo que la foto está en el directorio 'uploads'
                    "IDDueno" => $row['IDDueno'],
                    "NombreDueno" => $row['NombreDueno'],
                    "ApellidoDueno" => $row['ApellidoDueno']
                );
            }
            $response = array("success" => true, "mascotas" => $mascotas);
        } else {
            $response = array("success" => false, "message" => "No se encontraron mascotas asociadas a la cédula proporcionada");
        }

        $stmt->close();
    } else {
        $response = array("success" => false, "message" => "Cédula del dueño no proporcionada");
    }
} else {
    $response = array("success" => false, "message" => "Método de solicitud no válido");
}

echo json_encode($response);
?>
