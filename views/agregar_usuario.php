<?php
include '../backend/db.php'; // Conexión a la base de datos

// Variable para mostrar mensajes
$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar que todos los campos están presentes y no vacíos
    $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : null;
    $email = isset($_POST['email']) ? trim($_POST['email']) : null;
    $password = isset($_POST['password']) ? trim($_POST['password']) : null;
    $rol = isset($_POST['rol']) ? trim($_POST['rol']) : null;

    // Comprobar si los campos requeridos no están vacíos
    if ($nombre && $email && $password && $rol) {
        try {
            // Encriptar la contraseña
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Inserción en la base de datos
            $sql = "INSERT INTO usuarios (nombre, email, password, rol) 
                    VALUES (:nombre, :email, :password, :rol)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $hashed_password);
            $stmt->bindParam(':rol', $rol);
            $stmt->execute();

            // Mensaje de éxito
            header("Location: agregar_usuario.php?usuario_agregado=true");
            exit();
        } catch (PDOException $e) {
            // Mensaje de error
            $mensaje = "Error al agregar usuario: " . $e->getMessage();
        }
    } else {
        $mensaje = "Todos los campos son requeridos.";
    }
}

// Verificar si hay un mensaje de éxito
if (isset($_GET['usuario_agregado']) && $_GET['usuario_agregado'] == 'true') {
    $mensaje = "Usuario agregado correctamente.";
    $alert_class = "alert-success"; // Éxito (verde)
} else {
    // Si hay un error o advertencia, usar una alerta de error o advertencia
    if ($mensaje) {
        $alert_class = strpos($mensaje, 'Error') !== false ? "alert-danger" : "alert-warning"; // Error (rojo) o advertencia (amarillo)
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Agregar Usuario</h1>

        <!-- Mostrar el mensaje si existe -->
        <?php if ($mensaje): ?>
            <div class="alert <?php echo $alert_class; ?> alert-dismissible fade show" role="alert">
                <?php echo $mensaje; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre del Usuario</label>
                <input type="text" class="form-control" id="nombre" name="nombre" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Correo Electrónico</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3">
                <label for="rol" class="form-label">Rol</label>
                <select class="form-select" id="rol" name="rol" required>
                    <option value="admin">Administrador</option>
                    <option value="maestro_panadero">Maestro Panadero</option>
                    <option value="panadero">Panadero</option>
                    <option value="responsable_sucursal">Responsable de Sucursal</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Agregar</button>
            <a href="registros.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
