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
    $query = "SELECT * FROM usuarios WHERE email = ? AND password = ?";
    $stmt = $conn->prepare($query);

    // Verificar que la preparación fue exitosa
    if ($stmt) {
        $stmt->bind_param("ss", $email, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Inicio de sesión exitoso
            $_SESSION['usuario'] = $email;

            // Redirige al menú principal
            header("Location: menu.php");
            exit();
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
</head>
<body>
    <h2>Iniciar Sesión</h2>
    <form method="post" action="login.php">
        <label>Email:</label>
        <input type="email" name="email" required><br>
        <label>Contraseña:</label>
        <input type="password" name="password" required><br>
        <button type="submit">Iniciar Sesión</button>
    </form>
    <?php if (!empty($error)) echo "<p style='color: red;'>$error</p>"; ?>
</body>
</html>
