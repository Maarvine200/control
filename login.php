<?php
session_start();
include 'conexion.php'; // Asegúrate de que el archivo de conexión esté configurado correctamente

$error = ''; // Inicializa la variable de error

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

    // Verifica que la conexión esté activa
    if ($conn === false) {
        die("Error de conexión a la base de datos");
    }

    // Consulta para verificar las credenciales
    $query = "SELECT * FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($query);

    // Verificar que la preparación fue exitosa
    if ($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $usuario = $result->fetch_assoc();

            // Verificar la contraseña
            if (password_verify($password, $usuario['password'])) {
                // Inicio de sesión exitoso
                $_SESSION['usuario'] = $email;

                // Redirige al menú principal
                header("Location: menu.php");
                exit();
            } else {
                // Credenciales incorrectas
                $error = "Correo o contraseña incorrectos.";
            }
        } else {
            // Credenciales incorrectas
            $error = "Correo o contraseña incorrectos.";
        }

        $stmt->close();
    } else {
        // Si la consulta no se preparó correctamente, muestra un mensaje de error
        $error = "Error en la consulta: " . $conn->error;
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>
    <style>
        /* Estilos básicos para la página de inicio de sesión */
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        .error {
            color: red;
            margin-top: 10px;
        }
        .create-account {
            margin-top: 10px;
            text-align: center;
        }
        .create-account a {
            color: #007BFF;
            text-decoration: none;
        }
        .create-account a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Iniciar Sesión</h2>
        <form method="post" action="login.php">
            <label>Email:</label>
            <input type="email" name="email" required><br>
            <label>Contraseña:</label>
            <input type="password" name="password" required><br>
            <button type="submit">Iniciar Sesión</button>
        </form>
        <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
        <div class="create-account">
            <p>¿No tienes una cuenta? <a href="usuarios.php">Crear usuario</a></p>
        </div>
    </div>
</body>
</html>
