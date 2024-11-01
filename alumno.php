<?php
// Mostrar errores de PHP para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configuración de conexión a la base de datos
$host = 'localhost';
$dbname = 'control';
$username = 'root'; // Cambiar si usas otro usuario
$password = ''; // Cambiar si usas una contraseña

// Variable para almacenar mensajes
$mensaje = "";

// Variable para controlar el modo de edición
$modoEdicion = false;
$alumnoData = ['nombre' => ''];

// Conectar a la base de datos y procesar el formulario si se envía
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Verificar si el formulario de agregar/editar fue enviado
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['nombre']) && !empty($_POST['nombre'])) {
            $nombre = $_POST['nombre'];

            if (isset($_POST['id']) && !empty($_POST['id'])) {
                // Actualizar el registro en la tabla "alumnos"
                $sql = "UPDATE alumnos SET nombre = :nombre WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':nombre', $nombre);
                $stmt->bindParam(':id', $_POST['id']);
                $stmt->execute();
                $mensaje = "Alumno actualizado exitosamente.";
            } else {
                // Insertar el registro en la tabla "alumnos"
                $sql = "INSERT INTO alumnos (nombre) VALUES (:nombre)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':nombre', $nombre);
                $stmt->execute();
                $mensaje = "Alumno guardado exitosamente.";
            }

            // Redireccionar después de la operación
            header("Location: " . $_SERVER['PHP_SELF']);
            exit; // Asegúrate de salir después de la redirección
        } else {
            $mensaje = "Por favor, ingrese un nombre.";
        }
    }

    // Procesar solicitud de eliminación
    if (isset($_GET['eliminar'])) {
        $id = $_GET['eliminar'];
        $sql = "DELETE FROM alumnos WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $mensaje = "Alumno eliminado exitosamente.";
        
        // Redireccionar después de eliminar
        header("Location: " . $_SERVER['PHP_SELF']);
        exit; // Asegúrate de salir después de la redirección
    }

    // Consultar todos los registros de la tabla "alumnos"
    $consulta = $pdo->query("SELECT * FROM alumnos");
    $alumnos = $consulta->fetchAll(PDO::FETCH_ASSOC);

    // Comprobar si se está editando
    if (isset($_GET['editar'])) {
        $modoEdicion = true;
        $alumnoId = $_GET['editar'];
        // Obtener datos del alumno para editar
        $alumnoQuery = $pdo->prepare("SELECT * FROM alumnos WHERE id = :id");
        $alumnoQuery->bindParam(':id', $alumnoId);
        $alumnoQuery->execute();
        $alumnoData = $alumnoQuery->fetch(PDO::FETCH_ASSOC);
    }

} catch (PDOException $e) {
    $mensaje = "Error de conexión: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<nav>
        <ul>
            <li><a href="administrador.php">Administrador</a></li>
            <li><a href="alumno.php">alumnos</a></li>
            <li><a href="ingresar_nota.php">Ingresar Nota</a></li>
            <li><a href="guardar_nota.php">Guardar Nota</a></li>
            <li><a href="logout.php">Cerrar sesión</a></li>
        </ul>
    </nav>
    <meta charset="UTF-8">
    <title>Registro de Alumnos</title>
    <style>
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
        <h2>Registro de Alumnos</h2>

        <?php
        // Mostrar mensaje de éxito o error, si existe
        if (!empty($mensaje)) {
            $class = strpos($mensaje, 'exitosamente') !== false ? 'message' : 'message error';
            echo "<div class='$class'>$mensaje</div>";
        }
        ?>

        <form action="" method="post">
            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" required value="<?php echo htmlspecialchars($alumnoData['nombre']); ?>">
                <?php if ($modoEdicion): ?>
                    <input type="hidden" name="id" value="<?php echo $alumnoData['id']; ?>">
                <?php endif; ?>
            </div>
            <button type="submit"><?php echo $modoEdicion ? 'Actualizar' : 'Guardar'; ?></button>
            <?php if ($modoEdicion): ?>
                <a href="alumnos.php" class="btn-cancel">Cancelar</a>
            <?php endif; ?>
        </form>

        <h3>Lista de Alumnos</h3>
        <table>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Acciones</th>
            </tr>
            <?php foreach ($alumnos as $alumno): ?>
                <tr>
                    <td><?php echo htmlspecialchars($alumno['id']); ?></td>
                    <td><?php echo htmlspecialchars($alumno['nombre']); ?></td>
                    <td>
                        <a href="?editar=<?php echo $alumno['id']; ?>" class="btn-edit">Editar</a>
                        <a href="?eliminar=<?php echo $alumno['id']; ?>" class="btn-delete" onclick="return confirm('¿Estás seguro de que deseas eliminar este alumno?');">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>




