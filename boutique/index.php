<?php
session_start();

// Redirigir si no hay sesión activa
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php"); // Redirige a login.php si no hay sesión
    exit();
}

$conexion = new mysqli('localhost', 'root', '12345', 'boutiquee');

// Verificar la conexión
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css"> <!-- Asegúrate de que este archivo esté en el mismo directorio>
    <title>Bienvenido a la Boutique</title>
</head>
<body>
    <header>
        <h1>Bienvenido a la Boutique Luna & Sol</h1>
    </header>

    <p>Correo: <?= htmlspecialchars($_SESSION['usuario']); ?></p>
    <p>Rol: <?= htmlspecialchars($_SESSION['rol']); ?></p> <!-- Muestra el rol del usuario -->

    <div class="dashboard-buttons">
        <a href="logout.php"><button>Cerrar Sesión</button></a>
        <a href="ventas.php"><button>Registro de Ventas</button></a>
        <a href="inventario.php"><button>Gestión de Inventario</button></a>
        <a href="clientes.php"><button>Gestión de Clientes</button></a>
        <a href="proveedores.php"><button>Gestión de Proveedores</button></a>
        <a href="pedidos.php"><button>Gestión de Pedidos</button></a>
    </div>

    <footer>
        <span>© 2024 Mi Página Web</span>
    </footer>
</body>
</html>
