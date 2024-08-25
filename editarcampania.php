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
    $tipo = isset($_POST['Tipo']) ? $_POST['Tipo'] : null;
    $descripcion = isset($_POST['Descripcion']) ? $_POST['Descripcion'] : null;
    $fecha_inicio = isset($_POST['FechaInicio']) ? $_POST['FechaInicio'] : null;
    $fecha_fin = isset($_POST['FechaFin']) ? $_POST['FechaFin'] : null;
    $estado = isset($_POST['estado']) ? $_POST['estado'] : null;

    // Configuración para la conexión a la base de datos
    require_once 'conexion.php'; // Asegúrate de que este archivo contenga la configuración de la conexión a la base de datos

    // Preparar la consulta SQL con los campos proporcionados
    $query = "UPDATE campanias SET ";
    $params = [];
    $types = '';

    if ($nombre !== null) {
        $query .= "Nombre = ?, ";
        $params[] = $nombre;
        $types .= 's';
    }
    if ($tipo !== null) {
        $query .= "Tipo = ?, ";
        $params[] = $tipo;
        $types .= 's';
    }
    if ($descripcion !== null) {
        $query .= "Descripcion = ?, ";
        $params[] = $descripcion;
        $types .= 's';
    }
    if ($fecha_inicio !== null) {
        $query .= "FechaInicio = ?, ";
        $params[] = $fecha_inicio;
        $types .= 's';  // Asumiendo que Fecha_Inicio es un string en formato de fecha
    }
    if ($fecha_fin !== null) {
        $query .= "FechaFin = ?, ";
        $params[] = $fecha_fin;
        $types .= 's';  // Asumiendo que Fecha_Fin es un string en formato de fecha
    }
    if ($estado !== null) {
        $query .= "estado = ?, ";
        $params[] = $estado;
        $types .= 's';
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
            header('Content-Type: application/json');
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
            "message" => "Campaña actualizada correctamente"
        );
    } else {
        $response = array(
            "success" => false,
            "message" => "Error al actualizar la Campaña: " . $conn->error
        );
    }

    // Cerrar conexión a la base de datos
    $conn->close();
} else {
    $response = array(
        "success" => false,
        "message" => "Faltan datos necesarios para actualizar la Campaña."
    );
}

// Devolver la respuesta como JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
