<?php
session_start();
require 'config.php';

// Conexión a la base de datos
$host = 'localhost';
$usuario = 'root'; // Usuario por defecto de XAMPP
$contraseña = ''; // Contraseña por defecto de XAMPP
$nombre_base_datos = 'boutiquee';

// Crear conexión
$conn = new mysqli($host, $usuario, $contraseña, $nombre_base_datos);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Redirigir si el usuario no está autenticado
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

// Registrar nuevo cliente
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $telefono = $_POST['telefono'];
    $direccion = $_POST['direccion'];

    // Verificar si el correo ya existe
    $checkSql = "SELECT * FROM clientes WHERE correo=?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("s", $correo);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        echo "<script>alert('El correo ya está registrado.');</script>";
    } else {
        $sql = "INSERT INTO clientes (nombre, correo, telefono, direccion) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $nombre, $correo, $telefono, $direccion);

        if ($stmt->execute()) {
            echo "<script>alert('Cliente registrado exitosamente.');</script>";
        } else {
            echo "<script>alert('Error al registrar cliente: " . $stmt->error . "');</script>";
        }
        $stmt->close();
    }
    $checkStmt->close();
}

// Actualizar cliente
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'update') {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $telefono = $_POST['telefono'];
    $direccion = $_POST['direccion'];

    $sql = "UPDATE clientes SET nombre=?, correo=?, telefono=?, direccion=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $nombre, $correo, $telefono, $direccion, $id);

    if ($stmt->execute()) {
        echo "<script>alert('Cliente actualizado exitosamente.');</script>";
    } else {
        echo "<script>alert('Error al actualizar cliente: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}

// Consultar clientes
$sql = "SELECT * FROM clientes";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/styles.css">
    <title>Clientes</title>
</head>
<body>
    <h1>Lista de clientes</h1>

    <!-- Formulario para registrar un nuevo cliente -->
    <h2>Registrar Nuevo cliente</h2>
    <form action="" method="post">
        <input type="text" name="nombre" placeholder="Nombre" required>
        <input type="email" name="correo" placeholder="Correo" required>
        <input type="text" name="telefono" placeholder="Teléfono">
        <input type="text" name="direccion" placeholder="Dirección">
        <input type="hidden" name="action" value="add">
        <button type="submit">Registrar</button>
    </form>

    <h2>Clientes Registrados</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Email</th>
            <th>Teléfono</th>
            <th>Dirección</th>
            <th>Acciones</th>
        </tr>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['nombre']; ?></td>
                    <td><?php echo $row['correo']; ?></td>
                    <td><?php echo $row['telefono']; ?></td>
                    <td><?php echo $row['direccion']; ?></td>
                    <td>
                        <button onclick="document.getElementById('updateForm<?php echo $row['id']; ?>').style.display='block'">Actualizar</button>
                    </td>
                </tr>

                <!-- Formulario de actualización -->
                <div id="updateForm<?php echo $row['id']; ?>" style="display:none;">
                    <form action="" method="post">
                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                        <input type="text" name="nombre" value="<?php echo $row['nombre']; ?>" required>
                        <input type="email" name="correo" value="<?php echo $row['correo']; ?>" required>
                        <input type="text" name="telefono" value="<?php echo $row['telefono']; ?>">
                        <input type="text" name="direccion" value="<?php echo $row['direccion']; ?>">
                        <input type="hidden" name="action" value="update">
                        <button type="submit">Actualizar</button>
                        <button type="button" onclick="document.getElementById('updateForm<?php echo $row['id']; ?>').style.display='none'">Cancelar</button>
                    </form>
                </div>

            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="6">No hay clientes registrados.</td>
            </tr>
        <?php endif; ?>
    </table>
    <a href="dashboard.php"><button>Volver al Dashboard</button></a>
</body>
</html>

<?php
$conn->close();
?>
