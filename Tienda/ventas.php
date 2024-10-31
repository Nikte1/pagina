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

// Manejar el registro, actualización y eliminación de ventas
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        $venta_id = $_POST['venta_id'] ?? null;

        if ($action === 'registrar') {
            // Código de registro de ventas
            $cliente_id = $_POST['cliente_id'];
            $vendedor_id = $_POST['vendedor_id'];
            $fecha = $_POST['fecha'];
            $forma_pago = $_POST['forma_pago'];
            $total = $_POST['total'];

            $sql = "INSERT INTO ventas (cliente_id, vendedor_id, fecha, forma_pago, total) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("iissi", $cliente_id, $vendedor_id, $fecha, $forma_pago, $total);

                if ($stmt->execute()) {
                    echo "<script>alert('Venta registrada exitosamente.');</script>";
                } else {
                    echo "<script>alert('Error al registrar la venta: " . $stmt->error . "');</script>";
                }

                $stmt->close();
            } else {
                echo "Error al preparar la consulta: " . $conn->error;
            }
        } elseif ($action === 'actualizar') {
            // Código de actualización de ventas
            $cliente_id = $_POST['cliente_id'];
            $vendedor_id = $_POST['vendedor_id'];
            $fecha = $_POST['fecha'];
            $forma_pago = $_POST['forma_pago'];
            $total = $_POST['total'];

            $sql = "UPDATE ventas SET cliente_id=?, vendedor_id=?, fecha=?, forma_pago=?, total=? WHERE id=?";
            $stmt = $conn->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("isssdi", $cliente_id, $vendedor_id, $fecha, $forma_pago, $total, $venta_id);

                if ($stmt->execute()) {
                    echo "<script>alert('Venta actualizada exitosamente.');</script>";
                } else {
                    echo "<script>alert('Error al actualizar la venta: " . $stmt->error . "');</script>";
                }

                $stmt->close();
            } else {
                echo "Error al preparar la consulta: " . $conn->error;
            }
        } elseif ($action === 'eliminar') {
            // Eliminar la venta
            $sql = "DELETE FROM ventas WHERE id=?";
            $stmt = $conn->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("i", $venta_id);

                if ($stmt->execute()) {
                    echo "<script>alert('Venta eliminada exitosamente.');</script>";
                } else {
                    echo "<script>alert('Error al eliminar la venta: " . $stmt->error . "');</script>";
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
            echo "<script>alert('Venta eliminada exitosamente.');</script>";
        } else {
            echo "<script>alert('Error al eliminar la venta: " . $stmt->error . "');</script>";
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
                        <button onclick="document.getElementById('updateForm<?php echo $row['id']; ?>').style.display='block'">Editar</button>
                        <a href="?delete_id=<?php echo $row['id']; ?>" onclick="return confirm('¿Estás seguro de que deseas eliminar esta venta?')">Eliminar</a>
                    </td>
                </tr>

                <!-- Formulario de actualización -->
                <div id="updateForm<?php echo $row['id']; ?>" style="display:none;">
                    <form action="ventas.php" method="post">
                        <input type="hidden" name="venta_id" value="<?php echo $row['id']; ?>">
                        <label for="cliente_id">Cliente ID:</label>
                        <input type="number" name="cliente_id" value="<?php echo $row['cliente_id']; ?>" required>
                        <label for="vendedor_id">Vendedor ID:</label>
                        <input type="number" name="vendedor_id" value="<?php echo $row['vendedor_id']; ?>" required>
                        <label for="fecha">Fecha:</label>
                        <input type="datetime-local" name="fecha" value="<?php echo date('Y-m-d\TH:i', strtotime($row['fecha'])); ?>" required>
                        <label for="forma_pago">Forma de Pago:</label>
                        <select name="forma_pago" required>
                            <option value="tarjeta_credito" <?php if ($row['forma_pago'] == 'tarjeta_credito') echo 'selected'; ?>>Tarjeta de Crédito</option>
                            <option value="efectivo" <?php if ($row['forma_pago'] == 'efectivo') echo 'selected'; ?>>Efectivo</option>
                            <option value="transferencia" <?php if ($row['forma_pago'] == 'transferencia') echo 'selected'; ?>>Transferencia</option>
                        </select>
                        <label for="total">Total:</label>
                        <input type="number" name="total" value="<?php echo $row['total']; ?>" required step="0.01">
                        <input type="hidden" name="action" value="actualizar">
                        <button type="submit">Actualizar</button>
                        <button type="button" onclick="document.getElementById('updateForm<?php echo $row['id']; ?>').style.display='none'">Cancelar</button>
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
