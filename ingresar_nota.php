<?php
session_start();
require 'conexion.php';

// Obtener lista de alumnos y materias
$alumnos = $conn->query("SELECT * FROM alumnos");
$docentes = $conn->query("SELECT * FROM docentes");
?>
<form action="guardar_nota.php" method="POST">
    <label>Alumno:</label>
    <select name="alumno_id">
        <?php while ($alumno = $alumnos->fetch_assoc()): ?>
            <option value="<?php echo $alumno['id']; ?>"><?php echo $alumno['nombre']; ?></option>
        <?php endwhile; ?>
    </select>
    
    <label>Docente y Materia:</label>
    <select name="docente_id">
        <?php while ($docente = $docentes->fetch_assoc()): ?>
            <option value="<?php echo $docente['id']; ?>"><?php echo $docente['nombre'] . " - " . $docente['materia']; ?></option>
        <?php endwhile; ?>
    </select>
    
    <label>Nota:</label>
    <select name="nota">
        <option value="1">Aprobó</option>
        <option value="0">Reprobó</option>
    </select>
    <button type="submit">Guardar Nota</button>
</form>