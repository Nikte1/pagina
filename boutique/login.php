<?php
session_start();

// Crear conexión a la base de datos
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

// Mensaje de error si el inicio de sesión falla
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $correo = $_POST['correo'];
    $contraseña = $_POST['contraseña'];
    $rol = $_POST['rol']; // Obtener el rol seleccionado

    // Consulta para verificar el usuario y la contraseña
    $sql = "SELECT * FROM usuarios WHERE correo = ? AND contraseña = ? AND rol = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $correo, $contraseña, $rol);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verificar si se encontró un usuario
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $_SESSION['usuario'] = $row['correo']; // Guarda el correo en la sesión
        $_SESSION['rol'] = $row['rol']; // Guarda el rol en la sesión
        header("Location: index.php"); // Redirige al índice
        exit();
    } else {
        $error = 'correo, contraseña o rol incorrectos.';
    }

    // Cerrar la conexión
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: #ded5b7; /* Color de fondo actualizado */
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            color: #fff;
            text-align: center;
        }
        .container {
            background: rgba(0, 0, 0, 0.8);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.7);
            width: 400px;
        }
        h2 {
            margin-bottom: 20px;
            font-size: 2.5em;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
        }
        input[type="email"],
        input[type="password"],
        select {
            width: calc(100% - 20px);
            padding: 10px;
            border: none;
            border-radius: 5px;
            margin-bottom: 15px;
            font-size: 1.1em;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
        }
        input[type="submit"] {
            background: #a97c50; /* Color del botón actualizado */
            color: white;
            border: none;
            padding: 8px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            width: 50%;
            transition: background 0.3s;
            margin-top: 10px;
        }
        input[type="submit"]:hover {
            background: #8a6a4b; /* Color del botón al pasar el mouse */
        }
        .error {
            color: #ffdddd;
            margin: 10px 0;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Iniciar sesión</h2>
        <?php if ($error): ?>
            <p class="error"><?= $error ?></p>
        <?php endif; ?>
        <form action="login.php" method="POST" autocomplete="off">
            <label for="correo">Correo:</label>
            <input type="email" id="correo" name="correo" required>

            <label for="contraseña">Contraseña:</label>
            <input type="password" id="contraseña" name="contraseña" required>

            <label for="rol">Selecciona el rol:</label>
            <select id="rol" name="rol" required>
                <option value="admins">Administrador</option>
                <option value="vendedores">Vendedor</option>
            </select>

            <input type="submit" value="Iniciar sesión">
        </form>
    </div>
</body>
</html>


