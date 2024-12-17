<?php
include '../backend/db.php'; // Conexión a la base de datos

// Verificar si el ID del usuario está en la URL
if (!isset($_GET['id'])) {
    header("Location: registros.php");
    exit();
}

$id_usuario = $_GET['id'];

// Obtener la información del usuario desde la base de datos
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = :id");
$stmt->bindParam(':id', $id_usuario);
$stmt->execute();
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    echo "Usuario no encontrado.";
    exit();
}

// Actualizar usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $rol = $_POST['rol'];
    $password = $_POST['password'];

    // Verificar si la contraseña fue cambiada, si es así, actualizarla
    if (!empty($password)) {
        $password = password_hash($password, PASSWORD_DEFAULT); // Hash de la nueva contraseña
        $sql = "UPDATE usuarios SET nombre = :nombre, email = :email, rol = :rol, password = :password WHERE id = :id";
    } else {
        // Si no se ha cambiado la contraseña, solo actualizar el nombre, correo y rol
        $sql = "UPDATE usuarios SET nombre = :nombre, email = :email, rol = :rol WHERE id = :id";
    }

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':rol', $rol);
        $stmt->bindParam(':id', $id_usuario);
        if (!empty($password)) {
            $stmt->bindParam(':password', $password);
        }
        $stmt->execute();

        header("Location: registros.php?usuario_editado=true");
        exit();
    } catch (PDOException $e) {
        echo "Error al actualizar usuario: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #fff3e0;
        }
        .container {
            margin-top: 80px; /* Ajustar el espacio superior para que no se solape con el navbar */
        }
        .form-title {
            margin-bottom: 20px;
            color: #ff5722;
        }
        footer {
            background-color: #ff5722;
            color: white;
            padding: 15px 0;
            text-align: center;
            width: 100%;
            position: absolute;
            bottom: 0;
        }
        .password-toggle {
            cursor: pointer;
            color: #007bff;
        }
    </style>
</head>
<body>
    <?php include '../partials/navbar.php'; ?>

    <div class="container">
        <h2 class="form-title">Editar Usuario</h2>

        <form method="POST">
            <!-- Campo para el nombre -->
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" class="form-control" id="nombre" name="nombre" value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
            </div>

            <!-- Campo para el correo -->
            <div class="mb-3">
                <label for="email" class="form-label">Correo</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($usuario['email']) ?>" required>
            </div>

            <!-- Campo para el rol -->
            <div class="mb-3">
                <label for="rol" class="form-label">Rol</label>
                <select class="form-select" id="rol" name="rol" required>
                    <option value="administrador" <?= $usuario['rol'] == 'administrador' ? 'selected' : '' ?>>Administrador</option>
                    <option value="panadero" <?= $usuario['rol'] == 'panadero' ? 'selected' : '' ?>>Panadero</option>
                    <option value="maestro_panadero" <?= $usuario['rol'] == 'maestro_panadero' ? 'selected' : '' ?>>Maestro Panadero</option>
                    <option value="responsable_sucursal" <?= $usuario['rol'] == 'responsable_sucursal' ? 'selected' : '' ?>>Responsable Sucursal</option>
                </select>
            </div>

            <!-- Campo para la contraseña -->
            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Dejar en blanco para no cambiarla">
                <small id="passwordHelp" class="form-text text-muted">Dejar vacío si no deseas cambiar la contraseña.</small>
                <button type="button" class="btn btn-link password-toggle" id="togglePassword">Ver Contraseña</button>
            </div>

            <!-- Botón para actualizar el usuario -->
            <button type="submit" class="btn btn-success">Actualizar Usuario</button>
            <a href="registros.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; 2024 Panadería, Todos los derechos reservados.</p>
    </footer>

    <!-- Bootstrap JS, Popper.js, y jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    <script>
        // Función para alternar la visibilidad de la contraseña
        document.getElementById('togglePassword').addEventListener('click', function() {
            var passwordField = document.getElementById('password');
            var type = passwordField.type === 'password' ? 'text' : 'password';
            passwordField.type = type;
        });
    </script>
</body>
</html>
