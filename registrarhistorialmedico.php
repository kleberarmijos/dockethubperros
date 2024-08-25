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

include 'conexion.php'; // Incluye el archivo de conexión a la base de datos

// Obtener datos de la solicitud POST
$data = json_decode(file_get_contents('php://input'), true);

// Verificar que los datos están presentes
$requiredFields = ['ID_Mascota', 'Tipo_Esterilizacion', 'EsterilizacionParaMascota', 'Fecha', 'Esterilizado', 'Fecha_Esterilizacion', 'Ubicacion_Esterilizacion', 'desparacitacion', 'vacunas'];

foreach ($requiredFields as $field) {
    if (!isset($data[$field])) {
        http_response_code(400);
        echo json_encode(["message" => "Faltan datos en la solicitud"]);
        exit;
    }
}

// Preparar datos para la inserción
$idMascota = intval($data['ID_Mascota']);
$tipoEsterilizacion = ($data['Tipo_Esterilizacion'] === "null" || empty($data['Tipo_Esterilizacion'])) ? null : intval($data['Tipo_Esterilizacion']);
$esterilizacionParaMascota = ($data['EsterilizacionParaMascota'] === "null" || empty($data['EsterilizacionParaMascota'])) ? null : intval($data['EsterilizacionParaMascota']);
$fecha = ($data['Fecha'] === "null" || empty($data['Fecha'])) ? null : $data['Fecha'];
$esterilizado = boolval($data['Esterilizado']);
$fechaEsterilizacion = ($data['Fecha_Esterilizacion'] === "null" || empty($data['Fecha_Esterilizacion'])) ? null : $data['Fecha_Esterilizacion'];
$ubicacionEsterilizacion = ($data['Ubicacion_Esterilizacion'] === "null" || empty($data['Ubicacion_Esterilizacion'])) ? null : $data['Ubicacion_Esterilizacion'];
$desparacitacion = ($data['desparacitacion'] === "null" || empty($data['desparacitacion'])) ? null : $data['desparacitacion'];
$vacunas = is_array($data['vacunas']) ? $data['vacunas'] : [];

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["message" => "Conexión fallida: " . $conn->connect_error]);
    exit;
}

// Iniciar la transacción
$conn->begin_transaction();

try {
    // Insertar en la tabla historial_medico
    $stmt = $conn->prepare("INSERT INTO historial_medico (ID_Mascota, Tipo_Esterilizacion, EsterilizacionParaMascota, Fecha, Esterilizado, Fecha_Esterilizacion, Ubicacion_Esterilizacion, desparacitacion) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    if ($stmt === false) {
        throw new Exception("Error en la preparación del statement: " . $conn->error);
    }
    $stmt->bind_param("iiisssss", $idMascota, $tipoEsterilizacion, $esterilizacionParaMascota, $fecha, $esterilizado, $fechaEsterilizacion, $ubicacionEsterilizacion, $desparacitacion);
    $stmt->execute();

    $historialID = $conn->insert_id;

    // Insertar en la tabla historial_medico_vacunas
    $stmt = $conn->prepare("INSERT INTO historial_medico_vacunas (ID_Historial, ID_Vacuna) VALUES (?, ?)");
    if ($stmt === false) {
        throw new Exception("Error en la preparación del statement para vacunas: " . $conn->error);
    }
    $stmt->bind_param("ii", $historialID, $vacunaID);

    foreach ($vacunas as $vacunaID) {
        $stmt->execute();
    }

    // Confirmar la transacción
    $conn->commit();
    http_response_code(200);
    echo json_encode(["message" => "Datos insertados correctamente"]);
} catch (Exception $e) {
    // Revertir la transacción en caso de error
    $conn->rollback();
    http_response_code(500);
    echo json_encode(["message" => "Error al insertar los datos: " . $e->getMessage()]);
}

// Cerrar la conexión
$conn->close();
?>
