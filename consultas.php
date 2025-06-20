<?php
// Iniciar la sesión PHP
session_start();

// Verificar si el usuario no está logueado
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    // Si no está logueado, redirigirlo a la página de login
    header("location: logins.html"); // O la que uses para el login
    exit; // Terminar el script para asegurar la redirección
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard de Consultas</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #e0f2f7 0%, #c1e4f4 100%); /* Fondo degradado suave */
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: #333;
        }

        .main-container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 15px; /* Bordes más suaves */
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15); /* Sombra más pronunciada y suave */
            text-align: center;
            width: 90%;
            max-width: 600px; /* Ancho máximo para el dashboard */
            position: relative; /* Para posicionar el user-info y logout */
        }

        .user-info {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 0.9em;
            color: #555;
            font-weight: bold;
        }

        h1 {
            color: #2c3e50;
            margin-bottom: 40px;
            font-size: 2.2em;
            letter-spacing: -0.5px;
        }

        .button-grid {
            display: grid; /* Usamos CSS Grid para un layout flexible */
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); /* Columnas responsivas */
            gap: 25px; /* Espacio entre los botones */
            margin-top: 30px;
        }

        .button-grid button {
            padding: 25px 20px; /* Aumenta el padding para botones más grandes */
            font-size: 1.2em; /* Texto más grande */
            font-weight: bold;
            cursor: pointer;
            border: none;
            border-radius: 12px; /* Bordes más redondeados */
            color: white;
            transition: transform 0.2s ease, box-shadow 0.2s ease; /* Efectos de hover */
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); /* Sombra suave para los botones */
            display: flex; /* Para centrar contenido si es necesario */
            justify-content: center;
            align-items: center;
            flex-direction: column; /* Icono y texto apilados */
        }

        .button-grid button:hover {
            transform: translateY(-5px); /* Pequeño levantamiento al pasar el ratón */
            box-shadow: 0 8px 18px rgba(0, 0, 0, 0.2); /* Sombra más intensa al pasar el ratón */
        }

        /* Colores de los botones */
        .button-grid button:nth-child(1) { /* Comparación de Rutas */
            background-color: #28a745; /* Verde */
        }
        .button-grid button:nth-child(1):hover {
            background-color: #218838;
        }

        .button-grid button:nth-child(2) { /* Usuarios y Rutas */
            background-color: #ffc107; /* Amarillo/Naranja */
            color: #333; /* Texto oscuro para contraste */
        }
        .button-grid button:nth-child(2):hover {
            background-color: #e0a800;
        }

        .button-grid button:nth-child(3) { /* Tipos de Combustible */
            background-color: #6f42c1; /* Morado */
        }
        .button-grid button:nth-child(3):hover {
            background-color: #563d7c;
        }

        .button-grid button:nth-child(4) { /* Gestión de Rutas - Nuevo color */
            background-color: #007bff; /* Azul estándar de Bootstrap */
        }
        .button-grid button:nth-child(4):hover {
            background-color: #0056b3;
        }

        .logout-button {
            background-color: #dc3545; /* Rojo de Bootstrap */
            padding: 12px 25px;
            font-size: 1.1em;
            border-radius: 8px;
            margin-top: 35px; /* Más espacio arriba */
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease, box-shadow 0.2s ease;
        }
        .logout-button:hover {
            background-color: #c82333;
            transform: translateY(-3px);
            box-shadow: 0 6px 14px rgba(0, 0, 0, 0.15);
        }

        /* Media queries para responsividad */
        @media (max-width: 768px) {
            .main-container {
                padding: 30px;
            }
            .button-grid {
                grid-template-columns: 1fr; /* Una columna en pantallas pequeñas */
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="user-info">
            Bienvenido, **<?php echo htmlspecialchars($_SESSION["username"]); ?>**!
        </div>
        <h1>Dashboard de Consultas</h1>

        <div class="button-grid">
            <button onclick="location.href='consulta1.php'">
                Comparación de Rutas
            </button>
            <button onclick="location.href='consulta2.php'">
                Usuarios y Rutas
            </button>
            <button onclick="location.href='consulta3.php'">
                Tipos de Combustible
            </button>
            <button onclick="location.href='ingresar_datos_rutas.php'">
                Gestión de Rutas
            </button>
        </div>

        <button class="logout-button" onclick="location.href='logout.php'">Cerrar Sesión</button>
    </div>
</body>
</html>