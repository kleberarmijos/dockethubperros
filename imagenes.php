<?php
// Permitir todos los orígenes
header("Access-Control-Allow-Origin: *");

// Permitir los métodos y cabeceras que serán utilizados en las peticiones HTTP
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// Si la solicitud es OPTIONS, termina la ejecución
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Verificar si se recibió un archivo
if ($_FILES['file']) {
    $file = $_FILES['file'];
    
    // Configuración para la conexión a la base de datos
    require_once 'conexion.php'; // Asegúrate de que este archivo contenga la configuración de la conexión a la base de datos

    // Guardar el archivo en el servidor
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($file['name']);
    
    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        // Subida exitosa, guardar la ruta en la base de datos
        $ruta_imagen = $target_file;
        
        // Preparar la consulta SQL para insertar la ruta en la base de datos
        $sql = "INSERT INTO tabla_imagenes (ruta_imagen) VALUES ('$ruta_imagen')";
        
        if ($conn->query($sql) === TRUE) {
            // Éxito al guardar la ruta en la base de datos
            $response = array(
                "success" => true,
                "message" => "El archivo " . basename($file['name']) . " se ha subido correctamente y la ruta se ha guardado en la base de datos.",
                "file_path" => $ruta_imagen
            );
        } else {
            // Error al guardar en la base de datos
            $response = array(
                "success" => false,
                "message" => "Error al guardar la ruta de la imagen en la base de datos: " . $conn->error
            );
        }
    } else {
        // Error al mover el archivo al servidor
        $response = array(
            "success" => false,
            "message" => "Hubo un error al subir el archivo."
        );
    }

    // Cerrar conexión a la base de datos
    $conn->close();
} else {
    // No se recibió ningún archivo
    $response = array(
        "success" => false,
        "message" => "No se recibió ningún archivo."
    );
}

// Devolver la respuesta como JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
