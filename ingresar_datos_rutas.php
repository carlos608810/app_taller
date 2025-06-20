<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: logins.html");
    exit;
}

require_once 'config.php';

$user_id = $_SESSION["user_id"]; // Asumimos que el user_id se guarda en la sesión al iniciar sesión

// --- Lógica CRUD ---

// 1. CREATE (Crear una nueva ruta)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && $_POST["action"] == "create") {
    $origin = trim($_POST["origin_address"]);
    $destination = trim($_POST["destination_address"]);
    $planned_date = trim($_POST["planned_date"]);

    // Validaciones básicas
    if (empty($origin) || empty($destination) || empty($planned_date)) {
        $error = "Todos los campos son obligatorios.";
    } else {
        $sql = "INSERT INTO rutas (user_id, origin_address, destination_address, planned_date) VALUES (?, ?, ?, ?)";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("isss", $user_id, $origin, $destination, $planned_date);
            if ($stmt->execute()) {
                $success = "Ruta creada exitosamente.";
                // Limpiar los campos del formulario después de la inserción exitosa
                $_POST = array();
            } else {
                $error = "Error al crear la ruta: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error = "Error al preparar la consulta de inserción: " . $conn->error;
        }
    }
}

// 2. DELETE (Eliminar una ruta)
if (isset($_GET["action"]) && $_GET["action"] == "delete" && isset($_GET["id"])) {
    $route_id_to_delete = $_GET["id"];

    // IMPORTANTE: Asegurarse de que el usuario solo pueda eliminar sus propias rutas
    $sql = "DELETE FROM rutas WHERE route_id = ? AND user_id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ii", $route_id_to_delete, $user_id);
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $success = "Ruta eliminada exitosamente.";
            } else {
                $error = "No se pudo eliminar la ruta o no tienes permiso para hacerlo.";
            }
        } else {
            $error = "Error al eliminar la ruta: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error = "Error al preparar la consulta de eliminación: " . $conn->error;
    }
    // Redirigir para limpiar la URL de los parámetros GET
    header("location: ingresar_datos_rutas.php?status=" . urlencode($success ?? $error));
    exit;
}

// 3. READ (Obtener todas las rutas para mostrar en la tabla)
$rutas = [];
$sql_select = "SELECT route_id, origin_address, destination_address, planned_date FROM rutas WHERE user_id = ? ORDER BY planned_date DESC";
if ($stmt_select = $conn->prepare($sql_select)) {
    $stmt_select->bind_param("i", $user_id);
    $stmt_select->execute();
    $result_select = $stmt_select->get_result();
    while ($row = $result_select->fetch_assoc()) {
        $rutas[] = $row;
    }
    $stmt_select->close();
} else {
    $error = "Error al preparar la consulta de lectura: " . $conn->error;
}

// 4. UPDATE (Pre-cargar datos para edición si se pasó un ID)
$route_to_edit = null;
if (isset($_GET["action"]) && $_GET["action"] == "edit" && isset($_GET["id"])) {
    $id_edit = $_GET["id"];
    $sql_edit = "SELECT route_id, origin_address, destination_address, planned_date FROM rutas WHERE route_id = ? AND user_id = ?";
    if ($stmt_edit = $conn->prepare($sql_edit)) {
        $stmt_edit->bind_param("ii", $id_edit, $user_id);
        $stmt_edit->execute();
        $result_edit = $stmt_edit->get_result();
        if ($result_edit->num_rows == 1) {
            $route_to_edit = $result_edit->fetch_assoc();
        } else {
            $error = "Ruta no encontrada o no tienes permiso para editarla.";
        }
        $stmt_edit->close();
    }
}

// 5. UPDATE (Procesar la actualización)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && $_POST["action"] == "update") {
    $route_id = trim($_POST["route_id"]);
    $origin = trim($_POST["origin_address"]);
    $destination = trim($_POST["destination_address"]);
    $planned_date = trim($_POST["planned_date"]);

    if (empty($origin) || empty($destination) || empty($planned_date) || empty($route_id)) {
        $error = "Todos los campos y el ID de ruta son obligatorios para actualizar.";
    } else {
        $sql = "UPDATE rutas SET origin_address = ?, destination_address = ?, planned_date = ? WHERE route_id = ? AND user_id = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("sssii", $origin, $destination, $planned_date, $route_id, $user_id);
            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    $success = "Ruta actualizada exitosamente.";
                } else {
                    $warning = "No se realizaron cambios en la ruta o no tienes permiso para editarla.";
                }
            } else {
                $error = "Error al actualizar la ruta: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error = "Error al preparar la consulta de actualización: " . $conn->error;
        }
    }
}

$conn->close(); // Cerrar la conexión al final del script
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Rutas</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 20px; background-color: #f8f9fa; color: #333; }
        .container { max-width: 1000px; margin: auto; background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        h1, h2 { text-align: center; color: #007bff; margin-bottom: 25px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="date"] {
            width: calc(100% - 22px); /* Ancho completo menos padding y border */
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            box-sizing: border-box; /* Incluye padding y border en el ancho total */
        }
        button[type="submit"] {
            background-color: #28a745;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.3s ease;
        }
        button[type="submit"]:hover {
            background-color: #218838;
        }
        .message {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            text-align: center;
        }
        .success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .warning { background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; }

        table { width: 100%; border-collapse: collapse; margin-top: 30px; }
        th, td { border: 1px solid #e9ecef; padding: 12px; text-align: left; }
        th { background-color: #007bff; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        tr:hover { background-color: #e9e9e9; }
        .action-buttons { white-space: nowrap; }
        .action-buttons a, .action-buttons button {
            padding: 8px 12px;
            margin-right: 5px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 0.9em;
            display: inline-block; /* Para que queden en la misma línea */
        }
        .edit-button { background-color: #ffc107; color: #333; }
        .edit-button:hover { background-color: #e0a800; }
        .delete-button { background-color: #dc3545; color: white; }
        .delete-button:hover { background-color: #c82333; }

        .back-button {
            display: block;
            width: fit-content;
            margin: 30px auto 0;
            padding: 10px 20px;
            background-color: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
            transition: background-color 0.3s ease;
        }
        .back-button:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Gestión de Rutas</h1>

        <?php
        // Mostrar mensajes de estado
        if (isset($success)) { echo "<div class='message success'>" . $success . "</div>"; }
        if (isset($error)) { echo "<div class='message error'>" . $error . "</div>"; }
        if (isset($warning)) { echo "<div class='message warning'>" . $warning . "</div>"; }
        // Mostrar mensaje de status de la redirección después de eliminar
        if (isset($_GET['status'])) {
            $status_msg = htmlspecialchars($_GET['status']);
            $status_class = (strpos($status_msg, 'Error') !== false || strpos($status_msg, 'No se pudo') !== false) ? 'error' : 'success';
            echo "<div class='message " . $status_class . "'>" . $status_msg . "</div>";
        }
        ?>

        <h2><?php echo ($route_to_edit ? 'Editar Ruta Existente' : 'Agregar Nueva Ruta'); ?></h2>
        <form action="ingresar_datos_rutas.php" method="post">
            <?php if ($route_to_edit): ?>
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="route_id" value="<?php echo htmlspecialchars($route_to_edit['route_id']); ?>">
            <?php else: ?>
                <input type="hidden" name="action" value="create">
            <?php endif; ?>

            <div class="form-group">
                <label for="origin_address">Dirección de Origen:</label>
                <input type="text" id="origin_address" name="origin_address" value="<?php echo htmlspecialchars($route_to_edit['origin_address'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="destination_address">Dirección de Destino:</label>
                <input type="text" id="destination_address" name="destination_address" value="<?php echo htmlspecialchars($route_to_edit['destination_address'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="planned_date">Fecha Planificada:</label>
                <input type="date" id="planned_date" name="planned_date" value="<?php echo htmlspecialchars($route_to_edit['planned_date'] ?? ''); ?>" required>
            </div>
            <button type="submit"><?php echo ($route_to_edit ? 'Actualizar Ruta' : 'Crear Ruta'); ?></button>
            <?php if ($route_to_edit): ?>
                <a href="ingresar_datos_rutas.php" class="back-button" style="margin-top: 10px; width: auto; display: inline-block; background-color: #6c757d; color: white;">Cancelar Edición</a>
            <?php endif; ?>
        </form>

        <h2 style="margin-top: 50px;">Rutas Existentes de <?php echo htmlspecialchars($_SESSION["username"]); ?></h2>
        <?php if (!empty($rutas)): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Origen</th>
                        <th>Destino</th>
                        <th>Fecha Planificada</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rutas as $ruta): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($ruta['route_id']); ?></td>
                            <td><?php echo htmlspecialchars($ruta['origin_address']); ?></td>
                            <td><?php htmlspecialchars($ruta['destination_address']); ?></td>
                            <td><?php echo htmlspecialchars($ruta['planned_date']); ?></td>
                            <td class="action-buttons">
                                <a href="ingresar_datos_rutas.php?action=edit&id=<?php echo htmlspecialchars($ruta['route_id']); ?>" class="edit-button">Editar</a>
                                <button type="button" class="delete-button" onclick="confirmDelete(<?php echo htmlspecialchars($ruta['route_id']); ?>)">Eliminar</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="text-align: center;">No tienes rutas registradas.</p>
        <?php endif; ?>

        <a href="consultas.php" class="back-button">Volver al Dashboard</a>
    </div>

    <script>
        function confirmDelete(id) {
            if (confirm("¿Estás seguro de que quieres eliminar esta ruta?")) {
                window.location.href = "ingresar_datos_rutas.php?action=delete&id=" + id;
            }
        }
        // Mostrar mensaje de éxito/error después de la redirección
        window.onload = function() {
            const urlParams = new URLSearchParams(window.location.search);
            const status = urlParams.get('status');
            if (status) {
                // Eliminar el parámetro 'status' de la URL para que no se muestre al recargar
                const newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
                window.history.replaceState({ path: newUrl }, '', newUrl);
            }
        };
    </script>
</body>
</html>