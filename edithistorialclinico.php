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

// Incluir el archivo de conexión
include 'conexion.php';

// Configurar el tipo de contenido a JSON
header("Content-Type: application/json");

// Recibir datos JSON del cuerpo de la solicitud
$data = file_get_contents("php://input");

// Depurar el contenido recibido
file_put_contents('debug_log.txt', $data);

// Decodificar el JSON
$data = json_decode($data, true);

// Verificar la decodificación
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Error de decodificación JSON', 'json_error' => json_last_error_msg()]);
    exit;
}

// Verificar que se recibieron datos válidos
if (!isset($data['ID'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Datos no válidos o vacíos']);
    exit;
}

// Obtener el ID del historial clínico para actualizar
$id_historial = intval($data['ID']);

// Verificar que se haya proporcionado el ID de historial
if (empty($id_historial)) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'ID de historial no proporcionado']);
    exit;
}

// Preparar datos para la actualización
$updateFields = [];
$bindParams = [];
$types = '';

// Si se proporcionan datos, agregar los campos a la consulta de actualización
if (isset($data['ID_Mascota'])) {
    $updateFields[] = 'ID_Mascota = ?';
    $bindParams[] = intval($data['ID_Mascota']);
    $types .= 'i';
}

if (isset($data['Tipo_Esterilizacion'])) {
    $updateFields[] = 'Tipo_Esterilizacion = ?';
    $bindParams[] = intval($data['Tipo_Esterilizacion']);
    $types .= 'i';
}

if (isset($data['EsterilizacionParaMascota'])) {
    $updateFields[] = 'EsterilizacionParaMascota = ?';
    $bindParams[] = intval($data['EsterilizacionParaMascota']);
    $types .= 'i';
}

if (isset($data['Fecha'])) {
    $updateFields[] = 'Fecha = ?';
    $bindParams[] = $data['Fecha'];
    $types .= 's';
}

if (isset($data['Esterilizado'])) {
    $updateFields[] = 'Esterilizado = ?';
    $bindParams[] = intval($data['Esterilizado']);
    $types .= 'i';
}

if (isset($data['Fecha_Esterilizacion'])) {
    $updateFields[] = 'Fecha_Esterilizacion = ?';
    $bindParams[] = $data['Fecha_Esterilizacion'];
    $types .= 's';
}

if (isset($data['Ubicacion_Esterilizacion'])) {
    $updateFields[] = 'Ubicacion_Esterilizacion = ?';
    $bindParams[] = $data['Ubicacion_Esterilizacion'];
    $types .= 's';
}

if (isset($data['desparacitacion'])) {
    $updateFields[] = 'desparacitacion = ?';
    $bindParams[] = $data['desparacitacion'];
    $types .= 's';
}

// Verificar si hay campos para actualizar
if (empty($updateFields)) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'No se proporcionaron campos para actualizar']);
    exit;
}

// Construir la consulta SQL para actualizar la tabla historial_medico
$sqlUpdate = "UPDATE historial_medico SET " . implode(', ', $updateFields) . " WHERE ID = ?";

// Preparar la declaración y enlazar los parámetros
$stmt = $conn->prepare($sqlUpdate);
if ($stmt === false) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'Error al preparar la consulta: ' . $conn->error]);
    exit;
}

// Añadir el ID del historial al final de los parámetros
$bindParams[] = $id_historial;
$types .= 'i';

// Enlazar los parámetros
$stmt->bind_param($types, ...$bindParams);

// Ejecutar la declaración
if (!$stmt->execute()) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'Error al actualizar datos: ' . $stmt->error]);
    exit;
}

// Manejo de vacunas
if (isset($data['vacunas']) && is_array($data['vacunas'])) {
    // Iniciar la transacción
    $conn->begin_transaction();

    try {
        // Obtener las vacunas actuales
        $currentVaccinesStmt = $conn->prepare("SELECT ID_Vacuna FROM historial_medico_vacunas WHERE ID_Historial = ?");
        if ($currentVaccinesStmt === false) {
            throw new Exception("Error en la preparación del statement para obtener vacunas actuales: " . $conn->error);
        }
        $currentVaccinesStmt->bind_param('i', $id_historial);
        $currentVaccinesStmt->execute();
        $currentVaccinesResult = $currentVaccinesStmt->get_result();
        $currentVaccines = [];
        while ($row = $currentVaccinesResult->fetch_assoc()) {
            $currentVaccines[] = $row['ID_Vacuna'];
        }
        $currentVaccinesStmt->close();

        // Calcular vacunas a agregar
        $vaccinesToAdd = array_diff($data['vacunas'], $currentVaccines);

        // Insertar nuevas vacunas
        if (!empty($vaccinesToAdd)) {
            $insertVaccinesStmt = $conn->prepare("INSERT INTO historial_medico_vacunas (ID_Historial, ID_Vacuna) VALUES (?, ?)");
            if ($insertVaccinesStmt === false) {
                throw new Exception("Error en la preparación del statement para insertar vacunas: " . $conn->error);
            }
            $insertVaccinesStmt->bind_param('ii', $id_historial, $vacunaID);

            foreach ($vaccinesToAdd as $vacunaID) {
                $insertVaccinesStmt->execute();
            }
            $insertVaccinesStmt->close();
        }

        // Confirmar la transacción
        $conn->commit();
        echo json_encode(['message' => 'Datos actualizados correctamente.']);
    } catch (Exception $e) {
        // Revertir la transacción en caso de error
        $conn->rollback();
        http_response_code(500); // Internal Server Error
        echo json_encode(['error' => 'Error al actualizar vacunas: ' . $e->getMessage()]);
    }
} else {
    // Si no se proporcionan vacunas, confirmar la transacción
    $conn->commit();
    echo json_encode(['message' => 'Datos actualizados correctamente.']);
}

// Cerrar la conexión y liberar recursos
$conn->close();
?>
