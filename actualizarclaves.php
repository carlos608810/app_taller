<?php
// actualizar_contraseñas.php

// Iniciar conexión con la base de datos
$host = "localhost";
$usuario = "root";
$contrasena = "";
$bd = "app"; // Nombre de tu base de datos

$conn = new mysqli($host, $usuario, $contrasena, $bd);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Seleccionar todos los usuarios
$sql = "SELECT user_id, password_hash FROM Usuarios";
$resultado = $conn->query($sql);

if ($resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $user_id = $fila["user_id"];
        $password_plano = $fila["password_hash"];

        // Verifica si ya es un hash de bcrypt (empieza con $2y$ o $2a$)
        if (preg_match('/^\$2[ayb]\$/', $password_plano)) {
            continue; // Ya está hasheada
        }

        // Hashear la contraseña
        $hash_nuevo = password_hash($password_plano, PASSWORD_DEFAULT);

        // Actualizar en la base de datos
        $update_sql = "UPDATE Usuarios SET password_hash = ? WHERE user_id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("si", $hash_nuevo, $user_id);
        $stmt->execute();
        $stmt->close();
    }

    echo "<h3 style='color:green;'>✅ Contraseñas actualizadas correctamente.</h3>";
} else {
    echo "<h3 style='color:orange;'>⚠️ No se encontraron usuarios.</h3>";
}

$conn->close();
?>