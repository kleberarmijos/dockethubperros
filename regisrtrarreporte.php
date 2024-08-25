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

// Incluir archivo de conexión
require_once 'conexion.php';

// Función para registrar un nuevo reporte
function registrarReporte($ID_Usuario, $Nombre, $Foto, $Especie, $Raza, $Sexo, $Edad, $Color, $Peso, $Ubicacion_Perdida, $Fecha_Perdida, $telefono) {
    global $conn; // Hacer $conn global

    try {
        // SQL para insertar un nuevo reporte
        $sql = "INSERT INTO reportes_perros_perdidos (
                    ID_Usuario, Nombre, Foto, Especie, Raza, Sexo, Edad, Color, Peso, Ubicacion_Perdida, Fecha_Perdida, telefono
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        // Preparar la declaración SQL
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            throw new Exception($conn->error);
        }

        // Vincular los parámetros
        $stmt->bind_param("isssssisdssi", $ID_Usuario, $Nombre, $Foto, $Especie, $Raza, $Sexo, $Edad, $Color, $Peso, $Ubicacion_Perdida, $Fecha_Perdida, $telefono);

        // Ejecutar la declaración
        $stmt->execute();

        // Verificar si se insertó correctamente
        if ($stmt->affected_rows === 0) {
            throw new Exception("Error al insertar el reporte");
        }

        // Devolver el ID del nuevo reporte
        return $conn->insert_id;

    } catch (Exception $e) {
        // Manejar errores
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
        exit;
    } finally {
        $stmt->close();
    }
}

// Manejar la solicitud POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos del cuerpo de la solicitud
    $input = json_decode(file_get_contents('php://input'), true);

    // Verificar que se hayan recibido todos los datos necesarios
    if (
        isset($input['ID_Usuario']) && isset($input['Nombre']) && isset($input['Foto']) &&
        isset($input['Especie']) && isset($input['Raza']) && isset($input['Sexo']) &&
        isset($input['Edad']) && isset($input['Color']) && isset($input['Peso']) &&
        isset($input['Ubicacion_Perdida']) && isset($input['Fecha_Perdida']) && isset($input['telefono'])
    ) {
        // Registrar el nuevo reporte
        $nuevoID = registrarReporte(
            $input['ID_Usuario'], $input['Nombre'], $input['Foto'], $input['Especie'], $input['Raza'], $input['Sexo'],
            $input['Edad'], $input['Color'], $input['Peso'], $input['Ubicacion_Perdida'], $input['Fecha_Perdida'], $input['telefono']
        );

        // Responder con el ID del nuevo reporte
        http_response_code(201);
        echo json_encode(['nuevoID' => $nuevoID]);
    } else {
        // Responder con un error si faltan datos
        http_response_code(400);
        echo json_encode(['error' => 'Faltan datos necesarios']);
    }
}
?>
