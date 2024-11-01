<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Menú Principal</title>
</head>
<body>
    <h1>Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario']); ?>!</h1>
    <nav>
        <ul>
            <li><a href="administrador.php">Administrador</a></li>
            <li><a href="alumno.php">alumnos</a></li>
            <li><a href="ingresar_nota.php">Ingresar Nota</a></li>
            <li><a href="guardar_nota.php">Guardar Nota</a></li>
            <li><a href="logout.php">Cerrar sesión</a></li>
        </ul>
    </nav>
</body>
</html>

