<?php
session_start();
require 'config.php';

// Conexión a la base de datos
$host = 'localhost';
$usuario = 'root'; // Usuario por defecto de XAMPP
$contraseña = '12345'; // Contraseña por defecto de XAMPP
$nombre_base_datos = 'boutiquee'; // Cambia esto al nombre de tu base de datos

// Crear conexión
$conn = new mysqli($host, $usuario, $contraseña, $nombre_base_datos);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

// Manejar la inserción de un nuevo proveedor
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {
    $nombre = $_POST['nombre'];
    $contacto = $_POST['contacto'];
    $telefono = $_POST['telefono'];
    $direccion = $_POST['direccion'];

    $sql = "INSERT INTO proveedores (nombre, contacto, telefono, direccion) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $nombre, $contacto, $telefono, $direccion);

    if ($stmt->execute()) {
        echo "<script>alert('Proveedor agregado exitosamente.');</script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
    }

    $stmt->close();
}

// Actualizar proveedor
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update') {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $contacto = $_POST['contacto'];
    $telefono = $_POST['telefono'];
    $direccion = $_POST['direccion'];
    $stmt = $conn->prepare("UPDATE proveedores SET nombre=?, contacto=?, telefono=?, direccion=? WHERE id=?");
    $stmt->bind_param("ssssi", $nombre, $contacto, $telefono, $direccion, $id);

    if ($stmt->execute()) {
        echo "<script>alert('Proveedor actualizado con éxito.');</script>";
    } else {
        echo "<script>alert('Error al actualizar el proveedor: " . $stmt->error . "');</script>";
    }

    $stmt->close();
}

// Eliminar proveedor
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM proveedores WHERE id=?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>alert('Proveedor eliminado con éxito.');</script>";
    } else {
        echo "<script>alert('Error al eliminar el proveedor: " . $stmt->error . "');</script>";
    }

    $stmt->close();
}

// Consultar proveedores
$sql = "SELECT * FROM proveedores";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styles.css">
    <title>proveedores</title>
</head>
<body>
    <h1>Lista de proveedores</h1>

    <h2>Agregar Proveedor</h2>
    <form method="POST" action="">
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" required>

        <label for="contacto">Contacto:</label>
        <input type="text" id="contacto" name="contacto" required>
        <label for="telefono">Teléfono:</label>
        <input type="text" id="telefono" name="telefono" required>

        <label for="direccion">Dirección:</label>
        <input type="text" id="direccion" name="direccion" required>

        <input type="hidden" name="action" value="add">
        <button type="submit">Agregar Proveedor</button>
    </form>

    <h2>proveedores Registrados</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Contacto</th>
            <th>Teléfono</th>
            <th>Dirección</th>
            <th>Acciones</th>
        </tr>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['nombre']; ?></td>
                    <td><?php echo $row['contacto']; ?></td>
                    <td><?php echo $row['telefono']; ?></td>
                    <td><?php echo $row['direccion']; ?></td>
                    <td>
                        <button onclick="document.getElementById('updateForm<?php echo $row['id']; ?>').style.d>
                        <a href="?delete_id=<?php echo $row['id']; ?>" onclick="return confirm('¿Estás seguro d>
                    </td>
                </tr>

                <!-- Formulario de actualización -->
                <div id="updateForm<?php echo $row['id']; ?>" style="display:none;">
                    <form action="" method="post">
                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                        <label for="nombre">Nombre:</label>
                        <input type="text" name="nombre" value="<?php echo $row['nombre']; ?>" required>
                        <label for="contacto">Contacto:</label>
                        <input type="text" name="contacto" value="<?php echo $row['contacto']; ?>" required>
                        <label for="telefono">Teléfono:</label>
                        <input type="text" name="telefono" value="<?php echo $row['telefono']; ?>" required>
                        <label for="direccion">Dirección:</label>
                        <input type="text" name="direccion" value="<?php echo $row['direccion']; ?>" required>
                        <input type="hidden" name="action" value="update">
                        <button type="submit">Actualizar</button>
                        <button type="button" onclick="document.getElementById('updateForm<?php echo $row['id']>
                    </form>
                    </div>

            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="6">No hay proveedores registrados.</td>
            </tr>
        <?php endif; ?>
    </table>

    <a href="dashboard.php"><button>Volver al Dashboard</button></a>
</body>
</html>

<?php
$conn->close();
?>


