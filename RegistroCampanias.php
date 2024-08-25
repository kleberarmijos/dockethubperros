<?php
// Iniciar sesión si aún no lo has hecho
session_start();

// Establecer el tipo de contenido y los encabezados CORS
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Incluir el archivo de conexión a la base de datos
require_once 'conexion.php'; // Asegúrate de tener tu archivo de conexión a la base de datos

// Manejar la solicitud
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar si se recibieron todos los campos necesarios
    if (
        isset($_FILES['imagen'], $_POST['Nombre'], $_POST['Tipo'], $_POST['FechaInicio'], $_POST['FechaFin'], $_POST['Descripcion'])
    ) {
        // Obtener información del archivo
        $imagen = $_FILES['imagen']['name'];
        $temp = $_FILES['imagen']['tmp_name'];
        $uploadFileDir = './uploads/';
        $dest_path = $uploadFileDir . basename($imagen);

        // Validar el tipo de archivo
        $allowed_types = array('image/jpeg', 'image/jpg', 'image/png');
        $tipo = $_FILES['imagen']['type'];

        if (!in_array($tipo, $allowed_types)) {
            $response = array("success" => false, "message" => "Tipo de archivo no permitido. Sube imágenes en formato JPG, JPEG o PNG.");
        } else {
            // Mover el archivo al directorio de destino
            if (move_uploaded_file($temp, $dest_path)) {
                // Obtener otros datos del formulario
                $nombre = $_POST['Nombre'];
                $tipo = $_POST['Tipo'];
                $fechaInicio = $_POST['FechaInicio']; // Debe estar en formato YYYY-MM-DD
                $fechaFin = $_POST['FechaFin']; // Debe estar en formato YYYY-MM-DD
                $descripcion = $_POST['Descripcion'];

                // Estado por defecto
                $estado = 'en revision';

                try {
                    // Iniciar transacción
                    $conn->begin_transaction();

                    // Preparar consulta SQL para insertar datos en la base de datos
                    $stmt = $conn->prepare("INSERT INTO campanias (Nombre, Tipo, FechaInicio, FechaFin, Descripcion, Foto, estado) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    if ($stmt === false) {
                        throw new Exception($conn->error);
                    }

                    // Enlazar parámetros: 's' para strings, 's' para las fechas también, ya que son strings en la consulta
                    $stmt->bind_param("sssssss", $nombre, $tipo, $fechaInicio, $fechaFin, $descripcion, $imagen, $estado);
                    
                    // Ejecutar consulta
                    if (!$stmt->execute()) {
                        throw new Exception($stmt->error);
                    }

                    // Confirmar transacción
                    $conn->commit();

                    // Respuesta exitosa
                    $response = array("success" => true, "message" => "Campaña registrada correctamente");
                } catch (Exception $e) {
                    // Revertir transacción en caso de error
                    $conn->rollback();

                    // Respuesta de error
                    $response = array("success" => false, "message" => "Error de base de datos: " . $e->getMessage());
                } finally {
                    // Cerrar conexión
                    $conn->close();
                }
            } else {
                // Error al mover el archivo
                $response = array("success" => false, "message" => "Error al mover el archivo al servidor");
            }
        }
    } else {
        // Faltan datos necesarios para registrar la campaña
        $response = array("success" => false, "message" => "Faltan datos necesarios para registrar la campaña");
    }
} else {
    // Método de solicitud no válido
    $response = array("success" => false, "message" => "Método de solicitud no válido");
}

// Devolver la respuesta como JSON
echo json_encode($response);
?>
