<?php
// Configuración de conexión a la base de datos
$host = 'localhost';
$dbname = 'control';
$username = 'root';
$password = '';

// Variable para mensajes y modo de edición
$mensaje = "";
$modoEdicion = false;
$docenteData = ['nombre' => '', 'materia' => ''];

// Conectar a la base de datos y procesar el formulario si se envía
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Procesar formulario de agregar/editar
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['nombre']) && isset($_POST['materia'])) {
            $nombre = $_POST['nombre'];
            $materia = $_POST['materia'];

            if (isset($_POST['id']) && !empty($_POST['id'])) {
                // Actualizar registro
                $sql = "UPDATE docentes SET nombre = :nombre, materia = :materia WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':nombre', $nombre);
                $stmt->bindParam(':materia', $materia);
                $stmt->bindParam(':id', $_POST['id']);
                $stmt->execute();
                $mensaje = "Docente actualizado exitosamente.";
            } else {
                // Insertar registro
                $sql = "INSERT INTO docentes (nombre, materia) VALUES (:nombre, :materia)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':nombre', $nombre);
                $stmt->bindParam(':materia', $materia);
                $stmt->execute();
                $mensaje = "Docente guardado exitosamente.";
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
        $sql = "DELETE FROM docentes WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $mensaje = "Docente eliminado exitosamente.";

        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    // Consultar todos los registros de la tabla "docentes"
    $consulta = $pdo->query("SELECT * FROM docentes");
    $docentes = $consulta->fetchAll(PDO::FETCH_ASSOC);

    // Comprobar si se está editando
    if (isset($_GET['editar'])) {
        $modoEdicion = true;
        $docenteId = $_GET['editar'];
        $docenteQuery = $pdo->prepare("SELECT * FROM docentes WHERE id = :id");
        $docenteQuery->bindParam(':id', $docenteId);
        $docenteQuery->execute();
        $docenteData = $docenteQuery->fetch(PDO::FETCH_ASSOC);
    }

} catch (PDOException $e) {
    $mensaje = "Error de conexión: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Docentes</title>
    <style>
        /* Estilos de la interfaz */
        body { font-family: Arial, sans-serif; }
        .container { width: 50%; margin: auto; padding-top: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; font-weight: bold; }
        input[type="text"] { width: 100%; padding: 8px; }
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
        <h2>Gestión de Docentes</h2>

        <?php if (!empty($mensaje)) {
            $class = strpos($mensaje, 'exitosamente') !== false ? 'message' : 'message error';
            echo "<div class='$class'>$mensaje</div>";
        } ?>

        <form action="" method="post">
            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" required value="<?php echo htmlspecialchars($docenteData['nombre']); ?>">
            </div>
            <div class="form-group">
                <label for="materia">Materia:</label>
                <input type="text" id="materia" name="materia" required value="<?php echo htmlspecialchars($docenteData['materia']); ?>">
                <?php if ($modoEdicion): ?>
                    <input type="hidden" name="id" value="<?php echo $docenteData['id']; ?>">
                <?php endif; ?>
            </div>
            <button type="submit"><?php echo $modoEdicion ? 'Actualizar' : 'Guardar'; ?></button>
            <?php if ($modoEdicion): ?>
                <a href="docentes.php" class="btn-cancel">Cancelar</a>
            <?php endif; ?>
        </form>

        <h3>Lista de Docentes</h3>
        <table>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Materia</th>
                <th>Acciones</th>
            </tr>
            <?php foreach ($docentes as $docente): ?>
                <tr>
                    <td><?php echo htmlspecialchars($docente['id']); ?></td>
                    <td><?php echo htmlspecialchars($docente['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($docente['materia']); ?></td>
                    <td>
                        <a href="?editar=<?php echo $docente['id']; ?>" class="btn-edit">Editar</a>
                        <a href="?eliminar=<?php echo $docente['id']; ?>" class="btn-delete" onclick="return confirm('¿Estás seguro de que deseas eliminar este docente?');">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>
