<?php
session_start();

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require_once 'conexion.php'; // Asegúrate de tener tu archivo de conexión a la base de datos

$uploadDirImagen = './uploads/';
$uploadDirVideo = './videos/';
$allowedImageTypes = ['image/jpeg', 'image/jpg', 'image/png'];
$allowedVideoTypes = ['video/mp4', 'video/avi', 'video/mpeg', 'video/quicktime'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar que se recibieron todos los datos necesarios
    if (
        isset($_POST['fecha'], $_POST['nombres_apellidos'], $_POST['telefono'], $_POST['correo_electronico'], $_POST['direccion'], 
              $_POST['cedula_ruc'], $_POST['parroquia'], $_POST['referencia'], $_POST['tipo_medios_probatorios'], 
              $_POST['otros_medios_probatorios'], $_POST['relato_hechos'], $_POST['croquis'])
    ) {
        // Obtener datos del formulario
        $fecha = $_POST['fecha'];
        $nombres_apellidos = $_POST['nombres_apellidos'];
        $telefono = $_POST['telefono'];
        $correo_electronico = $_POST['correo_electronico'];
        $direccion = $_POST['direccion'];
        $cedula_ruc = $_POST['cedula_ruc'];
        $parroquia = $_POST['parroquia'];
        $referencia = $_POST['referencia'];
        $tipo_medios_probatorios = $_POST['tipo_medios_probatorios'];
        $otros_medios_probatorios = $_POST['otros_medios_probatorios'];
        $relato_hechos = $_POST['relato_hechos'];
        $croquis = $_POST['croquis'];

        // Inicializar variables para los archivos
        $evidencia_imagen = null;
        $evidencia_video = null;

        // Manejar imagen
        if ($tipo_medios_probatorios === 'Fotografías' && isset($_FILES['evidencia_imagen'])) {
            $file = $_FILES['evidencia_imagen'];
            $fileType = $file['type'];
            if (in_array($fileType, $allowedImageTypes)) {
                $tempName = $file['tmp_name'];
                $fileName = basename($file['name']);
                $destination = $uploadDirImagen . $fileName;

                if (move_uploaded_file($tempName, $destination)) {
                    $evidencia_imagen = $fileName;
                } else {
                    echo json_encode(["error" => "Error al mover la imagen al servidor."]);
                    exit;
                }
            } else {
                echo json_encode(["error" => "Tipo de imagen no permitido."]);
                exit;
            }
        }

        // Manejar video
        if ($tipo_medios_probatorios === 'Videos' && isset($_FILES['evidencia_video'])) {
            $file = $_FILES['evidencia_video'];
            $fileType = $file['type'];
            if (in_array($fileType, $allowedVideoTypes)) {
                $tempName = $file['tmp_name'];
                $fileName = basename($file['name']);
                $destination = $uploadDirVideo . $fileName;

                if (move_uploaded_file($tempName, $destination)) {
                    $evidencia_video = $fileName;
                } else {
                    echo json_encode(["error" => "Error al mover el video al servidor."]);
                    exit;
                }
            } else {
                echo json_encode(["error" => "Tipo de video no permitido."]);
                exit;
            }
        }

        try {
            // Preparar y ejecutar la consulta SQL
            $stmt = $conn->prepare("INSERT INTO denuncias 
                (fecha, nombres_apellidos, telefono, correo_electronico, direccion, cedula_ruc, parroquia, referencia, tipo_medios_probatorios, 
                evidencia_imagen, evidencia_video, otros_medios_probatorios, relato_hechos, croquis) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $stmt->bind_param(
                "ssssssssssssss",
                $fecha,
                $nombres_apellidos,
                $telefono,
                $correo_electronico,
                $direccion,
                $cedula_ruc,
                $parroquia,
                $referencia,
                $tipo_medios_probatorios,
                $evidencia_imagen,
                $evidencia_video,
                $otros_medios_probatorios,
                $relato_hechos,
                $croquis
            );
            $stmt->execute();

            $response = array("success" => true, "message" => "Denuncia registrada correctamente");
        } catch (Exception $e) {
            $response = array("success" => false, "message" => "Error de base de datos: " . $e->getMessage());
        } finally {
            $stmt->close();
            $conn->close();
        }
    } else {
        $response = array("success" => false, "message" => "Faltan datos necesarios para registrar la denuncia");
    }
} else {
    $response = array("success" => false, "message" => "Método de solicitud no válido");
}

echo json_encode($response);
?>
