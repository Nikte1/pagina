<?php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $correo = $conn->real_escape_string($_POST['correo']);
    $contraseña = $_POST['contraseña'];

    // Verifica si es un administrador o un vendedor
    $sql = "SELECT * FROM Admins WHERE correo='$correo'
            UNION
            SELECT * FROM Vendedores WHERE correo='$correo'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Aquí deberías utilizar password_verify si usas hashing
        if ($row['contraseña'] === $contraseña) { // Cambia esto si usas hashing
            $_SESSION['usuario'] = $correo;
            header("Location: dashboard.php");
            exit();
        } else {
            echo "Credenciales incorrectas.";
        }
    } else {
        echo "Credenciales incorrectas.";
    }
}
?>

