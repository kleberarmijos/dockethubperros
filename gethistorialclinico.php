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

// Preparar la consulta SQL con JOIN para obtener el historial clínico con información detallada
$sqlHistorial = "SELECT hm.*, 
                   m.Nombre AS NombreMascota, 
                   m.Raza AS RazaMascota, 
                   m.Edad AS EdadMascota, 
                   m.Sexo AS SexoMascota, 
                   m.Color AS ColorMascota,
                   u.Nombre AS NombreUsuario, 
                   u.Apellido AS ApellidoUsuario,
                   e.Nombre AS NombreEnfermedad,
                   t.Nombre AS NombreTratamiento,
                   GROUP_CONCAT(v.Nombre SEPARATOR ', ') AS NombreVacunas
            FROM historial_medico hm
            INNER JOIN mascotas m ON hm.ID_Mascota = m.ID
            INNER JOIN usuarios u ON m.ID_Usuario = u.ID
            LEFT JOIN enfermedades e ON hm.Tipo_Esterilizacion = e.ID
            LEFT JOIN tratamientos t ON hm.EsterilizacionParaMascota = t.ID
            LEFT JOIN historial_medico_vacunas hmv ON hm.ID = hmv.ID_Historial
            LEFT JOIN vacunas v ON hmv.ID_Vacuna = v.ID
            GROUP BY hm.ID";

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
    echo json_encode(['message' => 'No se encontraron registros de historial clínico.']);
}

// Cerrar la conexión
$conn->close();
?>
