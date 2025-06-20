<?php
// consulta2.php - Usuarios y Rutas

session_start();

// Verificar si el usuario no está logueado
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: logins.html");
    exit;
}

require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios y Rutas</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f4;}
        .container { max-width: 1000px; margin: auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 0 15px rgba(0,0,0,0.1); }
        h1 { text-align: center; color: #333; margin-bottom: 25px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; vertical-align: top; }
        th { background-color: #007bff; color: white; font-weight: bold; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        tr:hover { background-color: #e9e9e9; }
        .back-button {
            display: block;
            width: fit-content;
            margin: 25px auto 0;
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
        <h1>Resultados de Usuarios y Rutas</h1>

        <?php
        // Consulta SQL para obtener usuarios y sus rutas planificadas
        // Unimos 'usuarios' con 'rutas' usando 'user_id'
        $sql = "SELECT
                    u.username,
                    u.email,
                    r.origin_address,
                    r.destination_address,
                    r.planned_date
                FROM usuarios u
                JOIN rutas r ON u.user_id = r.user_id
                ORDER BY u.username ASC, r.planned_date DESC"; // Ordenar por usuario y luego por fecha de ruta

        if ($result = $conn->query($sql)) {
            if ($result->num_rows > 0) {
                echo "<table>";
                    echo "<thead>";
                        echo "<tr>";
                            echo "<th>Usuario</th>";
                            echo "<th>Email</th>";
                            echo "<th>Origen de Ruta</th>";
                            echo "<th>Destino de Ruta</th>";
                            echo "<th>Fecha Planificada</th>";
                        echo "</tr>";
                       "</thead>";
                    echo "<tbody>";
                    while($row = $result->fetch_assoc()){
                        echo "<tr>";
                            echo "<td>" . htmlspecialchars($row["username"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["email"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["origin_address"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["destination_address"]) . "</td>";
                            echo "<td>" . $row["planned_date"] . "</td>";
                        echo "</tr>";
                    }
                    echo "</tbody>";
                echo "</table>";
                $result->free();
            } else {
                echo "<p style='text-align: center;'>No se encontraron usuarios con rutas asociadas.</p>";
            }
        } else {
            echo "<p style='text-align: center; color: red;'>ERROR al ejecutar la consulta: " . $conn->error . "</p>";
        }

        $conn->close();
        ?>

        <a href="consultas.php" class="back-button">Volver al Dashboard</a>
    </div>
</body>
</html>