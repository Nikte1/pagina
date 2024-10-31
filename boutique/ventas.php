<?php
session_start();
require 'config.php';

// Conexión a la base de datos
$host = 'localhost';
$usuario = 'root';
$contraseña = '12345';
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

// Manejar el registro, actualización y eliminación de ventas
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        $venta_id = $_POST['venta_id'] ?? null;

        if ($action === 'registrar') {
            // ... Código de registro permanece igual ...

        } elseif ($action === 'actualizar') {
            // ... Código de actualización permanece igual ...

        } elseif ($action === 'eliminar') {
            // Eliminar la venta
            $sql = "DELETE FROM ventas WHERE id=?";
            $stmt = $conn->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("i", $venta_id);

                if ($stmt->execute()) {
                    echo "Venta eliminada exitosamente.";
                } else {
                    echo "Error al eliminar la venta: " . $stmt->error;
                }

                $stmt->close();
            } else {
                echo "Error al preparar la consulta: " . $conn->error;
            }
        }
    }
}

// Manejo de eliminación desde la URL
if (isset($_GET['delete_id'])) {
    $venta_id = $_GET['delete_id'];
    $sql = "DELETE FROM ventas WHERE id=?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("i", $venta_id);

        if ($stmt->execute()) {
            echo "Venta eliminada exitosamente.";
        } else {
            echo "Error al eliminar la venta: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Error al preparar la consulta: " . $conn->error;
    }
}

// Obtener todas las ventas de la base de datos
$sql = "SELECT * FROM ventas";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styles.css">
    <title>Registro de Ventas</title>
</head>
<body>
    <h1>Registro de Ventas</h1>
    <form action="ventas.php" method="POST">
        <input type="hidden" name="venta_id" id="venta_id">

        <label for="cliente_id">ID del Cliente:</label>
        <select name="cliente_id" id="cliente_id" required>
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
        </select>

        <label for="vendedor_id">ID del Vendedor:</label>
        <select name="vendedor_id" id="vendedor_id" required>
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
        </select>

        <input type="datetime-local" name="fecha" required>

        <label for="forma_pago">Forma de Pago:</label>
        <select name="forma_pago" id="forma_pago" required>
            <option value="tarjeta_credito">Tarjeta de Crédito</option>
            <option value="efectivo">Efectivo</option>
            <option value="transferencia">Transferencia</option>
        </select>

        <input type="number" name="total" required placeholder="Total" step="0.01">

        <button type="submit" name="action" value="registrar">Registrar Venta</button>
    </form>

    <h2>Lista de Ventas</h2>
    <?php if ($result->num_rows > 0): ?>
        <table>
            <tr>
                <th>ID</th>
                <th>Cliente ID</th>
                <th>Vendedor ID</th>
                <th>Fecha</th>
                <th>Forma de Pago</th>
                <th>Total</th>
                <th>Acciones</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['cliente_id']; ?></td>
                    <td><?php echo $row['vendedor_id']; ?></td>
                    <td><?php echo $row['fecha']; ?></td>
                    <td><?php echo $row['forma_pago']; ?></td>
                    <td><?php echo $row['total']; ?></td>
                    <td>
                        <button onclick="document.getElementById('updateForm<?php echo $row['id']; ?>').style.d>
                        <a href="?delete_id=<?php echo $row['id']; ?>" onclick="return confirm('¿Estás seguro d>
                    </td>
                </tr>

                <!-- Formulario de actualización -->
                 <div id="updateForm<?php echo $row['id']; ?>" style="display:none;">
                    <form action="ventas.php" method="post">
                        <input type="hidden" name="venta_id" value="<?php echo $row['id']; ?>">
                        <label for="cliente_id">Cliente ID:</label>
                        <input type="number" name="cliente_id" value="<?php echo $row['cliente_id']; ?>" requir>
                        <label for="vendedor_id">Vendedor ID:</label>
                        <input type="number" name="vendedor_id" value="<?php echo $row['vendedor_id']; ?>" requ>
                        <label for="fecha">Fecha:</label>
                        <input type="datetime-local" name="fecha" value="<?php echo date('Y-m-d\TH:i', strtotim>
                        <label for="forma_pago">Forma de Pago:</label>
                        <select name="forma_pago" required>
                            <option value="tarjeta_credito" <?php if ($row['forma_pago'] == 'tarjeta_credito') >
                            <option value="efectivo" <?php if ($row['forma_pago'] == 'efectivo') echo 'selected>
                            <option value="transferencia" <?php if ($row['forma_pago'] == 'transferencia') echo>
                        </select>
                        <label for="total">Total:</label>
                        <input type="number" name="total" value="<?php echo $row['total']; ?>" required step="0>
                        <input type="hidden" name="action" value="actualizar">
                        <button type="submit">Actualizar</button>
                        <button type="button" onclick="document.getElementById('updateForm<?php echo $row['id']>
                    </form>
                </div>

            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No hay ventas registradas.</p>
    <?php endif; ?>
    <a href="dashboard.php"><button>Volver al Dashboard</button></a>
</body>
</html>

<?php $conn->close(); ?>


