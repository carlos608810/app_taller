<?php
// procesar_login.php

// Iniciar una sesión PHP. Esto es crucial para manejar el estado de login.
session_start();

// Incluir el archivo de configuración de la base de datos
require_once 'config.php';

// Verificar si el formulario ha sido enviado usando el método POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validar y limpiar los datos de entrada
    // trim() elimina espacios en blanco al principio y al final
    // stripslashes() elimina barras invertidas
    // htmlspecialchars() convierte caracteres especiales en entidades HTML para prevenir XSS
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]); // La contraseña aún no ha sido hasheada

    // Preparar una sentencia SELECT para buscar el usuario por su nombre de usuario
    // Usamos sentencias preparadas para prevenir inyección SQL
    $sql = "SELECT user_id, username, password_hash FROM Usuarios WHERE username = ?";

    if ($stmt = $conn->prepare($sql)) {
        // Enlazar la variable del nombre de usuario al parámetro de la sentencia
        $stmt->bind_param("s", $param_username); // "s" indica que el parámetro es un string
        $param_username = $username; // Asignar el valor limpio

        // Intentar ejecutar la sentencia preparada
        if ($stmt->execute()) {
            // Almacenar el resultado
            $stmt->store_result();

            // Verificar si el nombre de usuario existe en la base de datos
            if ($stmt->num_rows == 1) {
                // Si existe, enlazar las variables de resultado
                $stmt->bind_result($user_id, $username, $password_hash);

                // Obtener los valores
                if ($stmt->fetch()) {
                    // Verificar la contraseña usando password_verify()
                    // password_verify compara la contraseña plana con el hash almacenado
                    if (password_verify($password, $password_hash)) {
                        // Contraseña correcta, iniciar sesión para el usuario
                        // Guardar variables en la sesión
                        $_SESSION["loggedin"] = true;
                        $_SESSION["user_id"] = $user_id;
                        $_SESSION["username"] = $username;

                        // Redireccionar al usuario a la página de dashboard/consultas
                        header("location: consultas.php"); // O dashboard.php si lo haces PHP
                        exit; // Es importante usar exit después de header()
                    } else {
                        // Contraseña incorrecta
                        echo "<script>alert('La contraseña que ingresaste no es válida.'); window.location.href='logins.html';</script>";
                    }
                }
            } else {
                // El nombre de usuario no existe
                echo "<script>alert('No existe ninguna cuenta con ese nombre de usuario.'); window.location.href='logins.html';</script>";
            }
        } else {
            // Error en la ejecución de la sentencia
            echo "Algo salió mal. Por favor, inténtalo de nuevo más tarde.";
        }

        // Cerrar la sentencia
        $stmt->close();
    } else {
        // Error al preparar la sentencia
        echo "Error al preparar la consulta: " . $conn->error;
    }

    // Cerrar la conexión a la base de datos
    $conn->close();
} else {
    // Si no se accedió a través de POST (ej. alguien intentó acceder directamente)
    header("location: logins.php");
    exit;
}
?>