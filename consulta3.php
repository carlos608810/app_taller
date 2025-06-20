<?php
// consulta3.php - Tipos de Combustible

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
    <title>Precios de Combustible</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f4;}
        .container { max-width: 800px; margin: auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 0 15px rgba(0,0,0,0.1); }
        h1 { text-align: center; color: #333; margin-bottom: 25px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
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
        <h1>Precios Actuales de Combustible</h1>

        <?php
        // Consulta SQL para obtener el precio más reciente de cada tipo de combustible
        // Esto se logra usando una subconsulta que encuentra el 'MAX(last_updated)'
        // para cada 'fuel_type' y luego uniéndola con la tabla principal
        $sql = "SELECT
                    pc.fuel_type,
                    pc.price_per_liter,
                    pc.currency,
                    pc.last_updated
                FROM precioscombustible pc
                INNER JOIN (
                    SELECT fuel_type, MAX(last_updated) AS max_updated
                    FROM precioscombustible
                    GROUP BY fuel_type
                ) AS latest_prices
                ON pc.fuel_type = latest_prices.fuel_type AND pc.last_updated = latest_prices.max_updated
                ORDER BY pc.fuel_type";

        if ($result = $conn->query($sql)) {
            if ($result->num_rows > 0) {
                echo "<table>";
                    echo "<thead>";
                        echo "<tr>";
                            echo "<th>Tipo de Combustible</th>";
                            echo "<th>Precio por Litro</th>";
                            echo "<th>Moneda</th>";
                            echo "<th>Última Actualización</th>";
                        echo "</tr>";
                        "</thead>";
                    echo "<tbody>";
                    while($row = $result->fetch_assoc()){
                        echo "<tr>";
                            echo "<td>" . htmlspecialchars($row["fuel_type"]) . "</td>";
                            echo "<td>" . number_format($row["price_per_liter"], 2, ',', '.') . "</td>";
                            echo "<td>" . htmlspecialchars($row["currency"]) . "</td>";
                            echo "<td>" . $row["last_updated"] . "</td>";
                        echo "</tr>";
                    }
                    echo "</tbody>";
                echo "</table>";
                $result->free();
            } else {
                echo "<p style='text-align: center;'>No se encontraron precios de combustible.</p>";
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