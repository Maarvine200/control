<?php
// Configuración de conexión a la base de datos
$host = 'localhost';
$dbname = 'control';
$username = 'root';
$password = '';

// Variable para mensajes y modo de edición
$mensaje = "";
$modoEdicion = false;
$notaData = ['alumno_id' => '', 'docente_id' => '', 'nota' => ''];

// Conectar a la base de datos y procesar el formulario si se envía
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener lista de alumnos
    $consultaAlumnos = $pdo->query("SELECT id, nombre FROM alumnos");
    $alumnos = $consultaAlumnos->fetchAll(PDO::FETCH_ASSOC);

    // Obtener lista de docentes
    $consultaDocentes = $pdo->query("SELECT id, nombre FROM docentes");
    $docentes = $consultaDocentes->fetchAll(PDO::FETCH_ASSOC);

    // Procesar formulario de agregar/editar
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['alumno_id']) && isset($_POST['docente_id']) && isset($_POST['nota'])) {
            $alumno_id = $_POST['alumno_id'];
            $docente_id = $_POST['docente_id'];
            $nota = $_POST['nota'];

            if (isset($_POST['id']) && !empty($_POST['id'])) {
                // Actualizar registro
                $sql = "UPDATE notas SET alumno_id = :alumno_id, docente_id = :docente_id, nota = :nota WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':alumno_id', $alumno_id);
                $stmt->bindParam(':docente_id', $docente_id);
                $stmt->bindParam(':nota', $nota);
                $stmt->bindParam(':id', $_POST['id']);
                $stmt->execute();
                $mensaje = "Nota actualizada exitosamente.";
            } else {
                // Insertar registro
                $sql = "INSERT INTO notas (alumno_id, docente_id, nota) VALUES (:alumno_id, :docente_id, :nota)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':alumno_id', $alumno_id);
                $stmt->bindParam(':docente_id', $docente_id);
                $stmt->bindParam(':nota', $nota);
                $stmt->execute();
                $mensaje = "Nota agregada exitosamente.";
            }

            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        } else {
            $mensaje = "Por favor, complete todos los campos.";
        }
    }

    // Procesar solicitud de eliminación
    if (isset($_GET['eliminar'])) {
        $id = $_GET['eliminar'];
        $sql = "DELETE FROM notas WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $mensaje = "Nota eliminada exitosamente.";
        
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    // Consultar todos los registros de la tabla "notas"
    $consulta = $pdo->query("SELECT * FROM notas");
    $notas = $consulta->fetchAll(PDO::FETCH_ASSOC);

    // Comprobar si se está editando
    if (isset($_GET['editar'])) {
        $modoEdicion = true;
        $notaId = $_GET['editar'];
        $notaQuery = $pdo->prepare("SELECT * FROM notas WHERE id = :id");
        $notaQuery->bindParam(':id', $notaId);
        $notaQuery->execute();
        $notaData = $notaQuery->fetch(PDO::FETCH_ASSOC);
    }

} catch (PDOException $e) {
    $mensaje = "Error de conexión: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Notas</title>
    <style>
        /* Estilos de la interfaz */
        body { font-family: Arial, sans-serif; }
        .container { width: 50%; margin: auto; padding-top: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; font-weight: bold; }
        input[type="text"], select { width: 100%; padding: 8px; }
        button { padding: 10px 15px; background-color: #4CAF50; color: #fff; border: none; cursor: pointer; }
        button:hover { background-color: #45a049; }
        .btn-cancel {
            background-color: #007BFF; /* Azul */
            color: #fff;
            padding: 10px 15px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            border-radius: 5px;
        }
        .btn-cancel:hover {
            background-color: #0056b3; /* Azul más oscuro */
        }
        .message { margin-top: 20px; padding: 10px; background-color: #f0f8ff; border: 1px solid #b6d4fe; color: #31708f; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        table { width: 100%; margin-top: 20px; border-collapse: collapse; }
        table, th, td { border: 1px solid #ddd; padding: 8px; }
        th { background-color: #f2f2f2; }
        .btn-edit {
            color: #fff;
            background-color: #FFA500; /* Naranja */
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 3px;
            margin-right: 5px;
        }
        .btn-edit:hover {
            background-color: #FF8C00; /* Naranja más oscuro */
        }
        .btn-delete {
            color: #fff;
            background-color: #FF6347; /* Rojo */
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 3px;
        }
        .btn-delete:hover {
            background-color: #FF4500; /* Rojo más oscuro */
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Gestión de Notas</h2>

        <?php if (!empty($mensaje)) {
            $class = strpos($mensaje, 'exitosamente') !== false ? 'message' : 'message error';
            echo "<div class='$class'>$mensaje</div>";
        } ?>

        <form action="" method="post">
            <div class="form-group">
                <label for="alumno_id">ID del Alumno:</label>
                <select id="alumno_id" name="alumno_id" required>
                    <option value="">Seleccione un alumno</option>
                    <?php foreach ($alumnos as $alumno): ?>
                        <option value="<?php echo $alumno['id']; ?>" <?php echo $alumno['id'] == $notaData['alumno_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($alumno['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="docente_id">ID del Docente:</label>
                <select id="docente_id" name="docente_id" required>
                    <option value="">Seleccione un docente</option>
                    <?php foreach ($docentes as $docente): ?>
                        <option value="<?php echo $docente['id']; ?>" <?php echo $docente['id'] == $notaData['docente_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($docente['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="nota">Nota:</label>
                <select id="nota" name="nota" required>
                    <option value="">Seleccione una nota</option>
                    <option value="0" <?php echo $notaData['nota'] === '0' ? 'selected' : ''; ?>>0</option>
                    <option value="1" <?php echo $notaData['nota'] === '1' ? 'selected' : ''; ?>>1</option>
                </select>
                <?php if ($modoEdicion): ?>
                    <input type="hidden" name="id" value="<?php echo $notaData['id']; ?>">
                <?php endif; ?>
            </div>
            <button type="submit"><?php echo $modoEdicion ? 'Actualizar' : 'Guardar'; ?></button>
            <?php if ($modoEdicion): ?>
                <a href="notas.php" class="btn-cancel">Cancelar</a>
            <?php endif; ?>
        </form>

        <h3>Lista de Notas</h3>
        <table>
            <tr>
                <th>ID</th>
                <th>ID del Alumno</th>
                <th>ID del Docente</th>
                <th>Nota</th>
                <th>Acciones</th>
            </tr>
            <?php foreach ($notas as $nota): ?>
                <tr>
                    <td><?php echo htmlspecialchars($nota['id']); ?></td>
                    <td><?php echo htmlspecialchars($nota['alumno_id']); ?></td>
                    <td><?php echo htmlspecialchars($nota['docente_id']); ?></td>
                    <td><?php echo htmlspecialchars($nota['nota']); ?></td>
                    <td>
                        <a href="?editar=<?php echo $nota['id']; ?>" class="btn-edit">Editar</a>
                        <a href="?eliminar=<?php echo $nota['id']; ?>" class="btn-delete" onclick="return confirm('¿Estás seguro de que deseas eliminar esta nota?');">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>

