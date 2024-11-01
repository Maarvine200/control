<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: menu.php");
    exit();
}

echo "Bienvenido, " . ($_SESSION['rol'] == 'administrador' ? "Administrador" : "Profesor");

if ($_SESSION['rol'] == 'administrador') {
    echo '<a href="agregar_docente.php">Agregar Docentes</a>';
    echo '<a href="agregar_alumno.php">Agregar Alumnos</a>';
}
echo '<a href="ingresar_notas.php">Ingresar Notas</a>';
?>