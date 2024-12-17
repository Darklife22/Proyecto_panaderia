<?php
// Configuración inicial
include_once '../backend/db.php'; // Ruta correcta desde 'views'

session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

// Verifica que APP_NAME esté definido
if (!defined('APP_NAME')) {
    define('APP_NAME', 'Panadería XYZ'); // Define el nombre de la aplicación si no está definido
}

$productos = [];
if (isset($pdo)) {
    try {
        $query = $pdo->query("SELECT id, nombre, precio FROM productos");
        $productos = $query->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Error al realizar la consulta: " . $e->getMessage());
    }
} else {
    die('Error: No se pudo conectar a la base de datos.');
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/estilos.css">
    <link rel="icon" href="../assets/img/icono.png">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <title>Productos - <?= APP_NAME ?></title>
    <style>
    /* Estilos generales para la página */
    /* Aseguramos que el navbar no se vea alterado en registros.php */
    .navbar-inpasep {
            background-color: #FFB347;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
            height: 65px;
        }

        .navbar-inpasep .marca {
            font-size: 1.8rem;
            
            color: white;
            font-weight: bold;
            text-decoration: none;
        }

        .navbar-inpasep .menu {
            list-style: none;
            display: flex;
        }

        .navbar-inpasep .menu li {
            padding-top: 10px;
        }

        .navbar-inpasep .menu a {
            align-items: center;
            color: white;
            text-decoration: none;
            font-size: 1rem;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .navbar-inpasep .menu a:hover {
            color: #FFB347;
        }

        .navbar-inpasep .boton-menu {
            display: none;
            flex-direction: column;
            justify-content: space-around;
            width: 30px;
            height: 25px;
            background: transparent;
            border: none;
            cursor: pointer;
        }

        .navbar-inpasep .linea {
            width: 30px;
            height: 3px;
            background-color: white;
            border-radius: 2px;
            transition: all 0.3s ease;
        }
    body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin-top: 60px; /* Para evitar que el contenido se solape con el navbar */
            min-height: 100vh; /* Asegura que el contenido ocupe todo el alto de la pantalla */
            display: flex;
            flex-direction: column;
        }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
        flex: 1;
    }

    h1 {
        color: #343a40;
        margin-bottom: 20px;
    }

    .btn-primary {
        background-color: #e67e22;
        border-color: #e67e22;
    }

    .table {
        background-color: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        width: 100%;
        overflow-x: auto;
    }

    .table-striped tbody tr:nth-child(odd) {
        background-color: #f2f2f2;
    }

    .footer {
        background-color: #e67e22;
        color: white;
        padding: 10px 0;
        text-align: center;
        margin-top: auto;
    }

    /* Estilos responsivos */
    @media (max-width: 768px) {
        h1 {
            font-size: 1.5rem;
        }

        .table th, .table td {
            font-size: 0.9rem;
        }
    }
</style>
</head>
<body>
    <?php include '../partials/navbar.php'; ?>

    <div class="container">
        <h1 style="text-decoration: bold; color: orange; text-align: center;">Lista de Productos</h1>
        <a href="/panaderia/index.php" class="btn btn-primary mb-3">Regresar al Dashboard</a>

        <!-- Alerta si no hay productos -->
        <?php if (empty($productos)): ?>
            <div class="alert alert-warning" role="alert">
                No hay productos registrados.
            </div>
        <?php endif; ?>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Precio (Bs)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($productos as $producto): ?>
                    <tr>
                        <td><?= htmlspecialchars($producto['id']) ?></td>
                        <td><?= htmlspecialchars($producto['nombre']) ?></td>
                        <td><?= htmlspecialchars($producto['precio']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>&copy; 2024 <?= APP_NAME ?>. Todos los derechos reservados.</p>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
