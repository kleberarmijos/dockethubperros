<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// Si la solicitud es OPTIONS, termina la ejecución
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Verificar si se ha recibido al menos el ID
if (isset($_POST['ID'])) {
    $id = $_POST['ID'];
    $nombre = isset($_POST['Nombre']) ? $_POST['Nombre'] : null;
    $sexo = isset($_POST['Sexo']) ? $_POST['Sexo'] : null;
    $raza = isset($_POST['Raza']) ? $_POST['Raza'] : null;
    $especie = isset($_POST['Especie']) ? $_POST['Especie'] : null;
    $edad = isset($_POST['Edad']) ? $_POST['Edad'] : null;
    $color = isset($_POST['Color']) ? $_POST['Color'] : null;
    $peso = isset($_POST['Peso']) ? $_POST['Peso'] : null;

    // Configuración para la conexión a la base de datos
    require_once 'conexion.php'; // Asegúrate de que este archivo contenga la configuración de la conexión a la base de datos

    // Preparar la consulta SQL con los campos proporcionados
    $query = "UPDATE mascotas SET ";
    $params = [];
    $types = '';

    if ($nombre !== null) {
        $query .= "Nombre = ?, ";
        $params[] = $nombre;
        $types .= 's';
    }

    if ($sexo !== null) {
        $query .= "Sexo = ?, ";
        $params[] = $sexo;
        $types .= 's';
    }

    if ($raza !== null) {
        $query .= "Raza = ?, ";
        $params[] = $raza;
        $types .= 's';
    }

    if ($especie !== null) {
        $query .= "Especie = ?, ";
        $params[] = $especie;
        $types .= 's';
    }

    if ($edad !== null) {
        $query .= "Edad = ?, ";
        $params[] = $edad;
        $types .= 'i';  // Asumiendo que Edad es un entero
    }

    if ($color !== null) {
        $query .= "Color = ?, ";
        $params[] = $color;
        $types .= 's';
    }

    if ($peso !== null) {
        $query .= "Peso = ?, ";
        $params[] = $peso;
        $types .= 'd';  // Asumiendo que Peso es un decimal
    }

    // Verificar si se ha enviado una nueva imagen
    if (isset($_FILES['Foto']) && $_FILES['Foto']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['Foto'];
        $imagen = basename($file['name']);
        $temp = $file['tmp_name'];

        // Directorio donde se almacenarán las imágenes
        $uploadFileDir = 'uploads/';
        $dest_path = $uploadFileDir . $imagen;

        // Guardar el archivo en el servidor
        if (move_uploaded_file($temp, $dest_path)) {
            $query .= "Foto = ?, ";
            $params[] = $imagen;
            $types .= 's';
        } else {
            $response = array(
                "success" => false,
                "message" => "Hubo un error al subir el archivo."
            );
            echo json_encode($response);
            exit;
        }
    }

    // Eliminar la última coma y espacio de la consulta SQL
    $query = rtrim($query, ", ");
    $query .= " WHERE ID = ?";
    $params[] = $id;
    $types .= 'i';

    // Preparar y ejecutar la consulta SQL
    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        $response = array(
            "success" => true,
            "message" => "Mascota actualizada correctamente"
        );
    } else {
        $response = array(
            "success" => false,
            "message" => "Error al actualizar la mascota: " . $conn->error
        );
    }

    // Cerrar conexión a la base de datos
    $conn->close();
} else {
    $response = array(
        "success" => false,
        "message" => "Faltan datos necesarios para actualizar la mascota."
    );
}

// Devolver la respuesta como JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
