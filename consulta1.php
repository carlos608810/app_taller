<?php
// consulta1.php - Comparación de Rutas

session_start();

// Verificar si el usuario no está logueado
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: logins.html"); // Redirigir si no hay sesión
    exit;
}

// Incluir el archivo de conexión a la base de datos
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comparación de Rutas</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f4;}
        .container { max-width: 1200px; margin: auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 0 15px rgba(0,0,0,0.1); }
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
        <h1>Resultados de Comparación de Rutas</h1>

        <?php
        // Consulta SQL para obtener los datos de comparación de rutas
        // Unimos con la tabla 'rutas' para obtener el origen y destino de cada ruta
        $sql = "SELECT
                    cr.comparison_id,
                    r.origin_address,
                    r.destination_address,
                    cr.comparison_date,
                    cr.waze_estimated_time_seconds,
                    cr.waze_distance_km,
                    cr.waze_estimated_fuel_cost,
                    cr.waze_route_url,
                    cr.Maps_estimated_time_seconds,
                    cr.Maps_distance_km,
                    cr.Maps_estimated_fuel_cost,
                    cr.Maps_route_url,
                    cr.best_time_app,
                    cr.best_fuel_app,
                    cr.overall_best_app
                FROM comparacionesruta cr
                JOIN rutas r ON cr.route_id = r.route_id
                ORDER BY cr.comparison_date DESC"; // Ordenar por fecha más reciente

        // Ejecutar la consulta
        if ($result = $conn->query($sql)) {
            // Verificar si hay resultados
            if ($result->num_rows > 0) {
                echo "<table>";
                    echo "<thead>";
                        echo "<tr>";
                            echo "<th>ID Comparación</th>";
                            echo "<th>Origen</th>";
                            echo "<th>Destino</th>";
                            echo "<th>Fecha Comparación</th>";
                            echo "<th>Waze Tiempo (s)</th>";
                            echo "<th>Waze Distancia (km)</th>";
                            echo "<th>Waze Combustible ($)</th>";
                            echo "<th>Google Maps Tiempo (s)</th>";
                            echo "<th>Google Maps Distancia (km)</th>";
                            echo "<th>Google Maps Combustible ($)</th>";
                            echo "<th>Mejor Tiempo</th>";
                            echo "<th>Mejor Combustible</th>";
                            echo "<th>Mejor General</th>";
                        echo "</tr>";
                        "</thead>";
                    echo "<tbody>";
                    // Iterar sobre cada fila de resultados
                    while($row = $result->fetch_assoc()){
                        echo "<tr>";
                            echo "<td>" . $row["comparison_id"] . "</td>";
                            echo "<td>" . htmlspecialchars($row["origin_address"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["destination_address"]) . "</td>";
                            echo "<td>" . $row["comparison_date"] . "</td>";
                            echo "<td>" . ($row["waze_estimated_time_seconds"] ?? 'N/A') . "</td>"; // Usar 'N/A' si es NULL
                            echo "<td>" . ($row["waze_distance_km"] ?? 'N/A') . "</td>";
                            echo "<td>" . (isset($row["waze_estimated_fuel_cost"]) ? number_format($row["waze_estimated_fuel_cost"], 2, ',', '.') : 'N/A') . "</td>";
                            echo "<td>" . ($row["Maps_estimated_time_seconds"] ?? 'N/A') . "</td>";
                            echo "<td>" . ($row["Maps_distance_km"] ?? 'N/A') . "</td>";
                            echo "<td>" . (isset($row["Maps_estimated_fuel_cost"]) ? number_format($row["Maps_estimated_fuel_cost"], 2, ',', '.') : 'N/A') . "</td>";
                            echo "<td>" . htmlspecialchars($row["best_time_app"] ?? 'N/A') . "</td>";
                            echo "<td>" . htmlspecialchars($row["best_fuel_app"] ?? 'N/A') . "</td>";
                            echo "<td>" . htmlspecialchars($row["overall_best_app"] ?? 'N/A') . "</td>";
                        echo "</tr>";
                    }
                    echo "</tbody>";
                echo "</table>";
                $result->free(); // Liberar la memoria del resultado
            } else {
                echo "<p style='text-align: center;'>No se encontraron comparaciones de rutas.</p>";
            }
        } else {
            echo "<p style='text-align: center; color: red;'>ERROR al ejecutar la consulta: " . $conn->error . "</p>";
        }

        $conn->close(); // Cerrar la conexión a la base de datos
        ?>

        <a href="consultas.php" class="back-button">Volver al Dashboard</a>
    </div>
</body>
</html>