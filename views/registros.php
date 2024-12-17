<?php
// Incluir la conexión a la base de datos
include '../backend/db.php';

// Eliminar producto
if (isset($_POST['eliminar_producto'])) {
    $id_producto = $_POST['id_producto'];

    try {
        // Eliminar el producto de la base de datos
        $sql = "DELETE FROM productos WHERE id = :id_producto";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id_producto', $id_producto);
        $stmt->execute();

        header("Location: registros.php?producto_eliminado=true");
        exit();
    } catch (PDOException $e) {
        echo "Error al eliminar producto: " . $e->getMessage();
    }
}

// Eliminar usuario
if (isset($_POST['eliminar_usuario'])) {
    $id_usuario = $_POST['id_usuario'];

    try {
        // Eliminar el usuario de la base de datos
        $sql = "DELETE FROM usuarios WHERE id = :id_usuario";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id_usuario', $id_usuario);
        $stmt->execute();

        header("Location: registros.php?usuario_eliminado=true");
        exit();
    } catch (PDOException $e) {
        echo "Error al eliminar usuario: " . $e->getMessage();
    }
}

// Eliminar sucursal
if (isset($_POST['eliminar_sucursal'])) {
    $id_sucursal = $_POST['id_sucursal'];
    try {
        $sql = "DELETE FROM sucursales WHERE id = :id_sucursal";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id_sucursal', $id_sucursal);
        $stmt->execute();
        header("Location: registros.php?sucursal_eliminada=true");
        exit();
    } catch (PDOException $e) {
        echo "Error al eliminar sucursal: " . $e->getMessage();
    }
}


// Obtener los productos desde la base de datos
$stmt_productos = $pdo->query("SELECT * FROM productos");
$productos = $stmt_productos->fetchAll(PDO::FETCH_ASSOC);

// Obtener los usuarios desde la base de datos
$stmt_usuarios = $pdo->query("SELECT * FROM usuarios");
$usuarios = $stmt_usuarios->fetchAll(PDO::FETCH_ASSOC);

// Obtener las sucursales desde la base de datos
$stmt_sucursales = $pdo->query("SELECT * FROM sucursales");
$sucursales = $stmt_sucursales->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registros</title>
    <link rel="icon" href="../assets/img/icono.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
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
            padding-left: 15px;
            padding-right: 15px;
        }

        /* Ajustes para la página de registros */
        body {
            background-color: #ffffff;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
        }

        .main-content {
            flex-grow: 1;
            padding-top: 80px; /* Espacio para el navbar fijo */
        }

        .section-title {
            background-color: #ff5722;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .table th, .table td {
            text-align: center;
        }

        footer {
            background-color: #ff5722;
            color: white;
            padding: 15px 0;
            text-align: center;
            width: 100%;
            position: relative;
        }
    </style>
</head>
<body>
    <?php include '../partials/navbar.php'; ?>

    <div class="container mt-4 main-content">
        <h1 class="titulo-principal" style="text-decoration: bold; color: orange; text-align: center;">Aquí puedes administrar los usuarios, sucursales y productos.</h1>

        <!-- Sección de Productos -->
        <div class="section">
            <h3 class="section-title">Productos</h3>

            <!-- Botón para agregar un nuevo producto -->
            <a href="agregar_producto.php" class="btn btn-primary mb-3">Agregar Nuevo Producto</a>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Precio</th>
                        <th>Cantidad</th>
                        <th>Fecha de Creación</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos as $producto): ?>
                        <tr>
                            <td><?= htmlspecialchars($producto['nombre']) ?></td>
                            <td><?= htmlspecialchars($producto['descripcion']) ?></td>
                            <td><?= htmlspecialchars($producto['precio']) ?></td>
                            <td><?= htmlspecialchars($producto['cantidad']) ?></td>
                            <td><?= htmlspecialchars($producto['fecha_creacion']) ?></td>
                            <td>
                                <a href="editar_producto.php?id=<?= $producto['id'] ?>" class="btn btn-warning btn-sm">Editar</a>

                                <!-- Botón para eliminar el producto -->
                                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#confirmarEliminarModalProducto<?= $producto['id'] ?>">
                                    Eliminar
                                </button>
                            </td>
                        </tr>

                        <!-- Modal de confirmación para eliminar producto -->
                        <div class="modal fade" id="confirmarEliminarModalProducto<?= $producto['id'] ?>" tabindex="-1" aria-labelledby="confirmarEliminarModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="confirmarEliminarModalLabel">Confirmar Eliminación</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        ¿Estás seguro de que deseas eliminar este producto?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                        <!-- Confirmar eliminación -->
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="id_producto" value="<?= $producto['id'] ?>">
                                            <button type="submit" name="eliminar_producto" class="btn btn-danger">Eliminar</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Sección de Usuarios -->
        <div class="section">
            <h3 class="section-title">Usuarios</h3>

            <a href="agregar_usuario.php" class="btn btn-primary mb-3">Agregar Nuevo Usuario</a>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td><?= $usuario['nombre'] ?></td>
                            <td><?= $usuario['email'] ?></td>
                            <td>
                                <a href="editar_usuario.php?id=<?= $usuario['id'] ?>" class="btn btn-warning btn-sm">Editar</a>

                                <!-- Botón para eliminar el usuario -->
                                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#confirmarEliminarModalUsuario<?= $usuario['id'] ?>">Eliminar</button>
                            </td>
                        </tr>

                        <!-- Modal de confirmación para eliminar usuario -->
                        <div class="modal fade" id="confirmarEliminarModalUsuario<?= $usuario['id'] ?>" tabindex="-1" aria-labelledby="confirmarEliminarModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="confirmarEliminarModalLabel">Confirmar Eliminación</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        ¿Estás seguro de que deseas eliminar este usuario?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                        <!-- Confirmar eliminación -->
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="id_usuario" value="<?= $usuario['id'] ?>">
                                            <button type="submit" name="eliminar_usuario" class="btn btn-danger">Eliminar</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Sección de Sucursales -->
        <div class="section">
            <h3 class="section-title">Sucursales</h3>

            <a href="agregar_sucursal.php" class="btn btn-primary mb-3">Agregar Nueva Sucursal</a>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Ubicación</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sucursales as $sucursal): ?>
                        <tr>
                            <td><?= $sucursal['nombre'] ?></td>
                            <td><?= $sucursal['direccion'] ?></td>
                            <td>
                                <a href="editar_sucursal.php?id=<?= $sucursal['id'] ?>" class="btn btn-warning btn-sm">Editar</a>

                                <!-- Botón para eliminar la sucursal -->
                                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#confirmarEliminarModalSucursal<?= $sucursal['id'] ?>">Eliminar</button>
                            </td>
                        </tr>

                        <!-- Modal de confirmación para eliminar sucursal -->
                        <div class="modal fade" id="confirmarEliminarModalSucursal<?= $sucursal['id'] ?>" tabindex="-1" aria-labelledby="confirmarEliminarModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="confirmarEliminarModalLabel">Confirmar Eliminación</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        ¿Estás seguro de que deseas eliminar esta sucursal?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                        <!-- Confirmar eliminación -->
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="id_sucursal" value="<?= $sucursal['id'] ?>">
                                            <button type="submit" name="eliminar_sucursal" class="btn btn-danger">Eliminar</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div> <!-- /.container -->

    <!-- Footer -->
    <footer>
        <p>&copy; 2024 Panadería, Todos los derechos reservados.</p>
    </footer>

    <!-- Bootstrap JS, Popper.js, y jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
