<?php
// config.php
// Parámetros de conexión a la base de datos
define('DB_SERVER', 'localhost'); // Usualmente 'localhost' para XAMPP
define('DB_USERNAME', 'root');   // El usuario por defecto de MySQL en XAMPP
define('DB_PASSWORD', '');       // La contraseña por defecto de MySQL en XAMPP (vacía)
define('DB_NAME', 'app'); // ¡CAMBIA ESTO por el nombre real de tu BD!

// Intentar conectar a la base de datos MySQL
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Verificar la conexión
if ($conn->connect_error) {
    // Si hay un error, terminar el script y mostrar el error
    die("ERROR: No se pudo conectar. " . $conn->connect_error);
}
?>