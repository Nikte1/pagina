<?php
session_start();
require 'config.php';

// Conexión a la base de datos
$host = 'localhost';
$usuario = 'root';
$contraseña = '';
$nombre_base_datos = 'boutiquee';

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

// Procesar la inserción o actualización de un nuevo inventario
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $id = $_POST['id'];
    $prenda = $_POST['prenda'];
    $talla = $_POST['talla'];
    $color = $_POST['color'];
    $cantidad = $_POST['cantidad'];
    $proveedor_id = $_POST['proveedor_id'];

    // Verificar la acción
    if ($_POST['action'] == 'add') {
        // Preparar la consulta para agregar
        $stmt = $conn->prepare("INSERT INTO inventario (id, prenda, talla, color, cantidad, proveedor_id) VALUE>
        $stmt->bind_param("issiii", $id, $prenda, $talla, $color, $cantidad, $proveedor_id);
    } elseif ($_POST['action'] == 'update') {
        // Preparar la consulta para actualizar
        $stmt = $conn->prepare("UPDATE inventario SET prenda=?, talla=?, color=?, cantidad=?, proveedor_id=? >
        $stmt->bind_param("ssiiii", $prenda, $talla, $color, $cantidad, $proveedor_id, $id);
    }

    // Ejecutar la consulta
    if ($stmt->execute()) {
        if ($_POST['action'] == 'add') {
            echo "<script>alert('Nueva prenda registrada con éxito.');</script>";
        } else {
            echo "<script>alert('Prenda actualizada con éxito.');</script>";
        }
    } else {
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
    }

    $stmt->close();
}

// Eliminar prenda
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM inventario WHERE id=?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>alert('Prenda eliminada con éxito.');</script>";
    } else {
        echo "<script>alert('Error al eliminar la prenda: " . $stmt->error . "');</script>";
    }

    $stmt->close();
}

// Consultar inventario
$sql = "SELECT * FROM inventario";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styles.css">
    <title>Inventario</title>
</head>
<body>
    <h1>Inventario de Prendas</h1>

    <!-- Formulario para agregar nueva prenda -->
    <form method="POST" action="">
        <h2>Agregar Nueva Prenda</h2>
        <label for="id">ID:</label>
        <input type="number" name="id" required>
        <label for="prenda">Prenda:</label>
        <input type="text" name="prenda" required>
        <label for="talla">Talla:</label>
        <input type="text" name="talla" required>
        <label for="color">Color:</label>
        <input type="text" name="color" required>
        <label for="cantidad">Cantidad:</label>
        <input type="number" name="cantidad" required>
        <label for="proveedor_id">Proveedor ID:</label>
        <input type="number" name="proveedor_id" required>
        <input type="hidden" name="action" value="add">
        <button type="submit">Registrar Prenda</button>
    </form>

    <h2>Listado de Prendas</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Prenda</th>
            <th>Talla</th>
            <th>Color</th>
            <th>Cantidad</th>
            <th>Proveedor ID</th>
            <th>Acciones</th>
        </tr>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['prenda']; ?></td>
                    <td><?php echo $row['talla']; ?></td>
                    <td><?php echo $row['color']; ?></td>
                    <td><?php echo $row['cantidad']; ?></td>
                    <td><?php echo $row['proveedor_id']; ?></td>
                    <td>
                        <button onclick="document.getElementById('updateForm<?php echo $row['id']; ?>').style.d>
                        <a href="?delete_id=<?php echo $row['id']; ?>" onclick="return confirm('¿Estás seguro d>
                    </td>
                </tr>

                <!-- Formulario de actualización -->
                <div id="updateForm<?php echo $row['id']; ?>" style="display:none;">
                    <form action="" method="post">
                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                        <label for="prenda">Prenda:</label>
                        <input type="text" name="prenda" value="<?php echo $row['prenda']; ?>" required>
                        <label for="talla">Talla:</label>
                        <input type="text" name="talla" value="<?php echo $row['talla']; ?>" required>
                        <label for="color">Color:</label>
                        <input type="text" name="color" value="<?php echo $row['color']; ?>" required>
                        <label for="cantidad">Cantidad:</label>
                        <input type="number" name="cantidad" value="<?php echo $row['cantidad']; ?>" required>
                        <label for="proveedor_id">Proveedor ID:</label>
                        <input type="number" name="proveedor_id" value="<?php echo $row['proveedor_id']; ?>" re>
                        <input type="hidden" name="action" value="update">
                        <button type="submit">Actualizar</button>
                        <button type="button" onclick="document.getElementById('updateForm<?php echo $row['id']>
                    </form>
                </div>
                 <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="7">No hay prendas registradas.</td>
            </tr>
        <?php endif; ?>
    </table>

    <a href="dashboard.php"><button>Volver al Dashboard</button></a>
</body>
</html>
