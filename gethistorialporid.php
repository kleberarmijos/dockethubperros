<?php
// Permitir todos los orígenes
header("Access-Control-Allow-Origin: *");

// Permitir los métodos y cabeceras que serán utilizados en las peticiones HTTP
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// Si la solicitud es OPTIONS, termina la ejecución
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Incluir el archivo de conexión
include 'conexion.php';

// Verificar si se proporcionó un ID de usuario en la solicitud GET
if (!isset($_GET['id_usuario'])) {
    http_response_code(400); // Solicitud incorrecta
    echo json_encode(['message' => 'ID de usuario no proporcionado.']);
    exit;
}

// Obtener el ID de usuario desde la solicitud GET
$idUsuario = intval($_GET['id_usuario']); // Convertir el ID a un entero para evitar inyecciones SQL

// Preparar la consulta SQL para obtener las ID_Mascota asociadas con el ID_Usuario
$sqlMascotas = "SELECT ID FROM mascotas WHERE ID_Usuario = ?";

// Preparar la consulta para obtener ID_Mascota
$stmtMascotas = $conn->prepare($sqlMascotas);

// Vincular el parámetro del ID de usuario
$stmtMascotas->bind_param("i", $idUsuario);

// Ejecutar la consulta
$stmtMascotas->execute();

// Obtener resultados
$resultadoMascotas = $stmtMascotas->get_result();

// Array para almacenar las ID de mascotas
$idsMascotas = array();
while ($row = $resultadoMascotas->fetch_assoc()) {
    $idsMascotas[] = $row['ID'];
}

// Verificar si se encontraron mascotas
if (count($idsMascotas) > 0) {
    // Convertir el array de IDs a una lista de valores para la consulta SQL
    $idsMascotasList = implode(',', $idsMascotas);

    // Preparar la consulta SQL con JOIN para obtener el historial clínico con información detallada
    $sqlHistorial = "SELECT hc.*, 
                       m.Nombre AS NombreMascota, m.Raza AS RazaMascota, m.Edad AS EdadMascota, m.Sexo AS SexoMascota, m.Color AS ColorMascota,
                       u.Nombre AS NombreUsuario, u.Apellido AS ApellidoUsuario,
                       t.Nombre AS NombreTratamiento,
                       v.Nombre AS NombreVacuna,
                       e.Nombre AS NombreEnfermedad,
                       a.Nombre AS NombreAlergia
                FROM historial_medico hc
                INNER JOIN mascotas m ON hc.ID_Mascota = m.ID
                INNER JOIN usuarios u ON m.ID_Usuario = u.ID
                LEFT JOIN tratamientos t ON hc.ID_Tratamiento = t.ID
                LEFT JOIN vacunas v ON hc.ID_Vacuna = v.ID
                LEFT JOIN enfermedades e ON hc.ID_Enfermedad = e.ID
                LEFT JOIN alergias a ON hc.ID_Alergia = a.ID
                WHERE hc.ID_Mascota IN ($idsMascotasList)";

    // Preparar y ejecutar la consulta del historial
    $resultadoHistorial = $conn->query($sqlHistorial);

    // Verificar si se encontraron resultados
    if ($resultadoHistorial->num_rows > 0) {
        // Array para almacenar los resultados
        $historial = array();

        // Recorrer los resultados y agregarlos al array
        while ($row = $resultadoHistorial->fetch_assoc()) {
            $historial[] = $row;
        }

        // Devolver los resultados como JSON
        echo json_encode($historial);
    } else {
        // Si no se encuentran resultados, devolver un mensaje adecuado
        echo json_encode(['message' => 'No se encontraron registros de historial clínico para el ID de usuario proporcionado.']);
    }
} else {
    // Si no se encontraron mascotas para el ID de usuario
    echo json_encode(['message' => 'No se encontraron mascotas asociadas con el ID de usuario proporcionado.']);
}

// Cerrar la conexión
$conn->close();
?>
