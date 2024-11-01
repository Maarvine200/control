<?php
$servername = "localhost"; // Cambia según tus configuraciones
$username = "root";
$password = "";
$dbname = "control";

// Crea una conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica si hay errores de conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>

