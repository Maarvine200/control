<?php
// Configuración de conexión a la base de datos
$host = 'localhost';
$dbname = 'control';
$username = 'root';
$password = '';

// Variable para mensajes y modo de edición
$mensaje = "";
$modoEdicion = false;
$usuarioData = ['nombre' => '', 'email' => '', 'password' => '', 'rol' => ''];

// Conectar a la base de datos y procesar el formulario si se envía
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Procesar formulario de agregar/editar
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['nombre'], $_POST['email'], $_POST['rol'])) {
            $nombre = $_POST['nombre'];
            $email = $_POST['email'];
            $rol = $_POST['rol'];
            
            // Verificar si la contraseña se debe actualizar
            $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;

            if (isset($_POST['id']) && !empty($_POST['id'])) {
                // Actualizar registro
                if ($password) {
                    $sql = "UPDATE usuarios SET nombre = :nombre, email = :email, password = :password, rol = :rol WHERE id = :id";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':password', $password);
                } else {
                    $sql = "UPDATE usuarios SET nombre = :nombre, email = :email, rol = :rol WHERE id = :id";
                    $stmt = $pdo->prepare($sql);
                }
                $stmt->bindParam(':id', $_POST['id']);
                $stmt->bindParam(':nombre', $nombre);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':rol', $rol);
                $stmt->execute();
                $mensaje = "Usuario actualizado exitosamente.";
            } else {
                // Insertar registro
                if ($password) {
                    $sql = "INSERT INTO usuarios (nombre, email, password, rol) VALUES (:nombre, :email, :password, :rol)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':password', $password);
                } else {
                    $mensaje = "La contraseña es obligatoria para un nuevo usuario.";
                }
                $stmt->bindParam(':nombre', $nombre);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':rol', $rol);
                $stmt->execute();
                $mensaje = "Usuario guardado exitosamente.";
            }

            // Restablecer los datos del usuario a valores predeterminados
            $usuarioData = ['nombre' => '', 'email' => '', 'password' => '', 'rol' => ''];

            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        } else {
            $mensaje = "Por favor, complete todos los campos.";
        }
    }

    // Procesar solicitud de eliminación
    if (isset($_GET['eliminar'])) {
        $id = $_GET['eliminar'];
        $sql = "DELETE FROM usuarios WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $mensaje = "Usuario eliminado exitosamente.";

        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    // Consultar todos los registros de la tabla "usuarios"
    $consulta = $pdo->query("SELECT * FROM usuarios");
    $usuarios = $consulta->fetchAll(PDO::FETCH_ASSOC);

    // Comprobar si se está editando
    if (isset($_GET['editar'])) {
        $modoEdicion = true;
        $usuarioId = $_GET['editar'];
        $usuarioQuery = $pdo->prepare("SELECT * FROM usuarios WHERE id = :id");
        $usuarioQuery->bindParam(':id', $usuarioId);
        $usuarioQuery->execute();
        $usuarioData = $usuarioQuery->fetch(PDO::FETCH_ASSOC);
    }

} catch (PDOException $e) {
    $mensaje = "Error de conexión: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Usuarios</title>
    <style>
        /* Estilos de la interfaz */
        body { font-family: Arial, sans-serif; }
        .container { width: 50%; margin: auto; padding-top: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; font-weight: bold; }
        input[type="text"], input[type="email"], input[type="password"], select { width: 100%; padding: 8px; }
        button { padding: 10px 15px; background-color: #4CAF50; color: #fff; border: none; cursor: pointer; }
        button:hover { background-color: #45a049; }
        .btn-cancel {
            background-color: #007BFF;
            color: #fff;
            padding: 10px 15px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            border-radius: 5px;
        }
        .btn-cancel:hover {
            background-color: #0056b3;
        }
        .message { margin-top: 20px; padding: 10px; background-color: #f0f8ff; border: 1px solid #b6d4fe; color: #31708f; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        table { width: 100%; margin-top: 20px; border-collapse: collapse; }
        table, th, td { border: 1px solid #ddd; padding: 8px; }
        th { background-color: #f2f2f2; }
        .btn-edit {
            color: #fff;
            background-color: #FFA500;
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 3px;
            margin-right: 5px;
        }
        .btn-edit:hover {
            background-color: #FF8C00;
        }
        .btn-delete {
            color: #fff;
            background-color: #FF6347;
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 3px;
        }
        .btn-delete:hover {
            background-color: #FF4500;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Gestión de Usuarios</h2>

        <?php if (!empty($mensaje)) {
            $class = strpos($mensaje, 'exitosamente') !== false ? 'message' : 'message error';
            echo "<div class='$class'>$mensaje</div>";
        } ?>

        <form action="" method="post">
            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" required value="<?php echo htmlspecialchars($usuarioData['nombre']); ?>">
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($usuarioData['email']); ?>">
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" <?php echo $modoEdicion ? '' : 'required'; ?>>
            </div>
            <div class="form-group">
                <label for="rol">Rol:</label>
                <select id="rol" name="rol" required>
                    <option value="Administrador" <?php echo ($usuarioData['rol'] == 'Administrador') ? 'selected' : ''; ?>>Administrador</option>
                    <option value="Docente" <?php echo ($usuarioData['rol'] == 'Docente') ? 'selected' : ''; ?>>Docente</option>
                </select>
                <?php if ($modoEdicion): ?>
                    <input type="hidden" name="id" value="<?php echo $usuarioData['id']; ?>">
                <?php endif; ?>
            </div>
            <button type="submit"><?php echo $modoEdicion ? 'Actualizar' : 'Guardar'; ?></button>
            <?php if ($modoEdicion): ?>
                <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn-cancel">Cancelar</a>
            <?php endif; ?>
        </form>

        <h3>Usuarios Registrados</h3>
        <table>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Rol</th>
                <th>Acciones</th>
            </tr>
            <?php foreach ($usuarios as $usuario): ?>
                <tr>
                    <td><?php echo htmlspecialchars($usuario['id']); ?></td>
                    <td><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                    <td><?php echo htmlspecialchars($usuario['rol']); ?></td>
                    <td>
                        <a href="?editar=<?php echo $usuario['id']; ?>" class="btn-edit">Editar</a>
                        <a href="?eliminar=<?php echo $usuario['id']; ?>" class="btn-delete">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>

