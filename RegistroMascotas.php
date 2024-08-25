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
        isset($_FILES['imagen'], $_POST['ID_Usuario'], $_POST['Especie'], $_POST['Raza'], $_POST['Nombre'], $_POST['Sexo'], $_POST['Edad'], $_POST['Color'], $_POST['Peso'], $_POST['Numero_Identificacion'])
    ) {
        // Obtener información del archivo
        $imagen = $_FILES['imagen']['name'];
        $nombre = $_POST['Nombre'];
        $temp = $_FILES['imagen']['tmp_name'];

        // Directorio donde se almacenarán las imágenes
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
                $id_usuario = $_POST['ID_Usuario'];
                $especie = $_POST['Especie'];
                $raza = $_POST['Raza'];
                $sexo = $_POST['Sexo'];
                $edad = $_POST['Edad'];
                $color = $_POST['Color'];
                $peso = $_POST['Peso'];
                $numero_identificacion = $_POST['Numero_Identificacion'];

                try {
                    // Iniciar transacción
                    $conn->begin_transaction();

                    // Preparar consulta SQL para insertar datos en la base de datos
                    $stmt = $conn->prepare("INSERT INTO mascotas (ID_Usuario, Especie, Raza, Nombre, Sexo, Edad, Color, Peso, Foto, Numero_Identificacion) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("issssisdss", $id_usuario, $especie, $raza, $nombre, $sexo, $edad, $color, $peso, $imagen, $numero_identificacion);
                    $stmt->execute();

                    // Confirmar transacción
                    $conn->commit();

                    // Respuesta exitosa
                    $response = array("success" => true, "message" => "Mascota registrada correctamente");
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
        // Faltan datos necesarios para registrar la mascota
        $response = array("success" => false, "message" => "Faltan datos necesarios para registrar la mascota");
    }
} else {
    // Método de solicitud no válido
    $response = array("success" => false, "message" => "Método de solicitud no válido");
}

// Devolver la respuesta como JSON
echo json_encode($response);
?>
