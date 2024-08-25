<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

$servername = "localhost"; // Cambia esto si tu servidor de base de datos no está en localhost
$username = "admin"; // Tu usuario de MySQL
$password = "Nomeacuerdo.2197"; // Tu contraseña de MySQL
$dbname = "vetcom"; // El nombre de tu base de datos

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);
// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
} 

?>
