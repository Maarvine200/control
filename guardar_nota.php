<?php
session_start();
require 'conexion.php'; // Asegúrate de que conexion.php configura la conexión en la variable $conn

// Verifica si el usuario tiene permisos para acceder
if (!isset($_SESSION['rol']) || ($_SESSION['rol'] != 'administrador' && $_SESSION['rol'] != 'profesor')) {
    exit("Acceso denegado.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitiza y valida las entradas
    $alumno_id = filter_input(INPUT_POST, 'alumno_id', FILTER_VALIDATE_INT);
    $docente_id = filter_input(INPUT_POST, 'docente_id', FILTER_VALIDATE_INT);
    $nota = filter_input(INPUT_POST, 'nota', FILTER_VALIDATE_INT);

    if ($alumno_id && $docente_id && $nota !== false) {
        // Prepara la consulta
        $stmt = $conn->prepare("INSERT INTO notas (alumno_id, docente_id, nota) VALUES (?, ?, ?)");

        if ($stmt) {
            $stmt->bind_param("iii", $alumno_id, $docente_id, $nota);

            if ($stmt->execute()) {
                echo "Nota guardada exitosamente.";
            } else {
                echo "Error al ejecutar la consulta: " . $stmt->error;
            }

            $stmt->close();
        } else {
            echo "Error en la preparación de la consulta: " . $conn->error;
        }
    } else {
        echo "Error: Todos los campos deben ser números enteros válidos.";
    }

    $conn->close(); // Cierra la conexión
}
?>
