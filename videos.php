<?php
session_start();

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require_once 'conexion.php'; // Conexión a la base de datos

$directorio_destino = "videos/"; // Directorio donde se guardarán los videos
$tipos_permitidos = ['video/mp4', 'video/avi', 'video/mpeg', 'video/quicktime']; // Tipos de video permitidos

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar que el archivo de video se haya enviado y que se haya proporcionado el id_campania
    if (isset($_FILES['video']) && $_FILES['video']['error'] == 0 && isset($_POST['id_campania']) && isset($_POST['nombre'])) {
        
        // Variables del archivo y otros datos
        $ruta_temporal = $_FILES['video']['tmp_name'];
        $nombre_archivo = basename($_FILES['video']['name']); // Nombre original del archivo
        $nombre_archivo_personalizado = $_POST['nombre']; // Nombre personalizado proporcionado por el usuario
        $tamaño_archivo = $_FILES['video']['size'];
        $tipo_archivo = $_FILES['video']['type'];
        $id_campania = $_POST['id_campania']; // ID de la campaña proporcionada

        // Validar el tipo de archivo
        if (!in_array($tipo_archivo, $tipos_permitidos)) {
            echo json_encode(["error" => "Tipo de archivo no permitido."]);
            exit;
        }

        // Definir la ruta completa del archivo donde se va a almacenar
        $ruta_completa = $directorio_destino . $nombre_archivo;

        // Mover el archivo al directorio de destino
        if (move_uploaded_file($ruta_temporal, $ruta_completa)) {
            // Consulta SQL para insertar los datos en la base de datos
            $query = "INSERT INTO videos (nombre, ruta, tipo, id_campania) VALUES ('$nombre_archivo_personalizado', '$ruta_completa', '$tipo_archivo', '$id_campania')";
            if (mysqli_query($conn, $query)) {
                echo json_encode(["mensaje" => "El archivo se ha subido exitosamente."]);
            } else {
                echo json_encode(["error" => "Error al guardar en la base de datos: " . mysqli_error($conn)]);
            }
        } else {
            echo json_encode(["error" => "Hubo un error al subir el archivo."]);
        }
    } else {
        echo json_encode(["error" => "No se recibió ningún archivo, hubo un error en la carga o faltan datos."]);
    }
} else {
    echo json_encode(["error" => "Método de solicitud no permitido."]);
}
?>
