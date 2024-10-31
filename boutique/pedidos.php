<?php
session_start();
require 'config.php';

// Conexión a la base de datos
$host = 'localhost';
$usuario = 'root'; // Usuario por defecto de XAMPP
$contraseña = '12345'; // Cambia esto a tu contraseña real
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

// Manejar la inserción de un nuevo pedido
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {
    $proveedor_id = $_POST['proveedor_id'];
    $fecha_pedido = $_POST['fecha_pedido'];
    $estado = $_POST['estado'];

    // Para depurar
    var_dump($_POST);

    $sql = "INSERT INTO pedidos (proveedor_id, fecha_pedido, estado) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $proveedor_id, $fecha_pedido, $estado);

    if ($stmt->execute()) {
        echo "<script>alert('Pedido agregado exitosamente.');</script>";
    } else {
        echo "<script>alert('Error al agregar el pedido: " . $stmt->error . "');</script>";
    }

    $stmt->close();
}

// Eliminar pedido
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM pedidos WHERE id=?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo "<script>alert('Pedido eliminado con éxito.');</script>";
    } else {
        echo "<script>alert('Error al eliminar el pedido: " . $stmt->error . "');</script>";
    }

    $stmt->close();
}

// Consultar pedidos
$sql = "SELECT * FROM pedidos";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styles.css">
    <title>pedidos</title>
</head>
<body>
    <h1>Lista de Pedidos</h1>

    <h2>Agregar Pedido</h2>
    <form method="POST" action="">
        <label for="proveedor_id">Proveedor ID:</label>
        <select id="proveedor_id" name="proveedor_id" required>
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
        </select>

        <label for="fecha_pedido">Fecha de Pedido:</label>
        <input type="date" id="fecha_pedido" name="fecha_pedido" required>

        <label for="estado">Estado:</label>
        <select id="estado" name="estado" required>
            <option value="Pendiente">Pendiente</option>
            <option value="Completado">Completado</option>
        </select>

        <input type="hidden" name="action" value="add">
        <button type="submit">Agregar Pedido</button>
    </form>

    <h2>Pedidos Registrados</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Proveedor ID</th>
            <th>Fecha de Pedido</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['proveedor_id']; ?></td>
                    <td><?php echo $row['fecha_pedido']; ?></td>
                    <td><?php echo $row['estado']; ?></td>
                    <td>
                        <button onclick="document.getElementById('updateForm<?php echo $row['id']; ?>').style.d>
                        <a href="?delete_id=<?php echo $row['id']; ?>" onclick="return confirm('¿Estás seguro d>
                    </td>
                </tr>

                <!-- Formulario de actualización -->
                <div id="updateForm<?php echo $row['id']; ?>" style="display:none;">
                    <form action="" method="post">
                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                        <label for="proveedor_id">Proveedor ID:</label>
                        <select name="proveedor_id" required>
                            <option value="<?php echo $row['proveedor_id']; ?>" selected><?php echo $row['prove>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                        </select>

                        <label for="fecha_pedido">Fecha de Pedido:</label>
                        <input type="date" name="fecha_pedido" value="<?php echo $row['fecha_pedido']; ?>" requ>

                        <label for="estado">Estado:</label>
                        <select name="estado" required>
                            <option value="Pendiente" <?php echo $row['estado'] == 'Pendiente' ? 'selected' : '>
                            <option value="Completado" <?php echo $row['estado'] == 'Completado' ? 'selected' :>
                            <option value="Cancelado" <?php echo $row['estado'] == 'Cancelado' ? 'selected' : '>
                        </select>

                        <input type="hidden" name="action" value="update">
                        <button type="submit">Actualizar</button>
                        <button type="button" onclick="document.getElementById('updateForm<?php echo $row['id']>
                    </form>
                </div>

            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="5">No hay pedidos registrados.</td>
            </tr>
            <?php endif; ?>
            </table>
            <a href="dashboard.php"><button>Volver al Dashboard</button></a>
        </body>
        </html>
        
        <?php
        $conn->close();
        ?>
        


