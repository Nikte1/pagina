<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php"); // Si no hay sesión, redirige a login.php
    exit();
} else {
    // Si la sesión está activa, redirige a index.php
    header("Location: index.php"); 
    exit();
}
?>


