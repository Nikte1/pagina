<?php
$host = 'localhost'; // Nombre del host
$db = 'boutiquee'; // Nombre de tu base de datos
$user = 'root'; // Usuario por defecto en XAMPP
$pass = '12345'; // Contraseña por defecto (puede estar vacía)

// Crear conexión
$conn = new mysqli($host, $user, $pass, $db);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>


