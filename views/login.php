<?php
// Iniciar sesión
session_start();

// Incluir el archivo de conexión
require_once '../backend/db.php';  // Asegúrate de que la ruta sea correcta

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener los datos del formulario
    $usuario = $_POST['usuario'];
    $contrasena = $_POST['contrasena'];

    // Verificar que el usuario y la contraseña no estén vacíos
    if (!empty($usuario) && !empty($contrasena)) {
        // Preparar la consulta para obtener el usuario
        $sql = "SELECT * FROM usuarios WHERE nombre = :usuario";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':usuario', $usuario);
        $stmt->execute();

        // Verificar si el usuario existe
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            // Verificar la contraseña
            if (password_verify($contrasena, $user['password'])) {
                // Si las credenciales son correctas, iniciar sesión
                $_SESSION['usuario_id'] = $user['id'];
                $_SESSION['usuario_nombre'] = $user['nombre'];
                $_SESSION['usuario_rol'] = $user['rol']; // Guardar el rol si lo necesitas

                // Redirigir al dashboard
                header('Location: ../index.php'); // Asegúrate de que la ruta sea correcta
                exit();
            } else {
                $error = "Contraseña incorrecta";
            }
        } else {
            $error = "El usuario no existe";
        }
    } else {
        $error = "Por favor ingrese usuario y contraseña";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Estilos del login -->
    <link rel="icon" href="../assets/img/icono.png">
    <link rel="stylesheet" href="../assets/css/estilos_login.css">
</head>
<body>

    <div class="panes-decorativos">
        <img src="http://localhost/panaderia/assets/img/pan2.png" alt="Pan 1" class="pan1">
        <img src="http://localhost/panaderia/assets/img/pan1.png" alt="Pan 2" class="pan2">
        <img src="http://localhost/panaderia/assets/img/pan3.png" alt="Pan 3" class="pan3">
        <img src="http://localhost/panaderia/assets/img/pan4.png" alt="Pan 4" class="pan4">
    </div>

    <div class="container-fluid d-flex justify-content-center align-items-center min-vh-100">
        <div class="login-container p-5">
            <div class="text-center">
                <!-- Logo de la panadería -->
                <img src="../assets/img/logo.png" alt="Logo Panadería" class="logo">
            </div>
            <h1 class="text-center text-orange mb-4">Iniciar Sesión</h1>

            <?php
            // Mostrar errores si existen
            if (isset($error)) {
                echo "<div class='alert alert-danger'>$error</div>";
            }
            ?>

            <form action="login.php" method="POST">
                <div class="mb-3">
                    <label for="usuario" class="form-label">Usuario:</label>
                    <input type="text" name="usuario" id="usuario" class="form-control" placeholder="Ingresa tu usuario" required>
                </div>

                <div class="mb-3">
                    <label for="contrasena" class="form-label">Contraseña:</label>
                    <input type="password" name="contrasena" id="contrasena" class="form-control" placeholder="Ingresa tu contraseña" required>
                </div>

                <button type="submit" class="btn btn-login w-100">Iniciar sesión</button>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS y Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
