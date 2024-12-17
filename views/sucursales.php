<?php
include '../backend/sucursales.php';

$sucursales = obtenerSucursales();  // Obtener las sucursales

// Manejo de formularios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['crear'])) {
        // Crear sucursal
        crearSucursal($_POST['nombre'], $_POST['direccion'], $_POST['responsable']);
        header('Location: sucursales.php'); 
        exit();
    } elseif (isset($_POST['eliminar'])) {
        // Eliminar sucursal
        eliminarSucursal($_POST['id']);
        header('Location: sucursales.php'); 
        exit();
    } elseif (isset($_POST['editar'])) {
        // Editar sucursal
        actualizarSucursal($_POST['id'], $_POST['nombre'], $_POST['direccion'], $_POST['responsable']);
        header('Location: sucursales.php'); 
        exit();
    }
}

// Verificar si se está editando una sucursal
$sucursalParaEditar = null;
if (isset($_GET['editar'])) {
    $id = $_GET['editar'];
    $sucursalParaEditar = obtenerSucursalPorId($id);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/estilos.css">
    <link rel="icon" href="../assets/img/icono.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <title>Sucursales</title>
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
            padding-left: 1px;
            padding-right: 1px;
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
        table th, table td {
            text-align: center;
        }
        .btn-danger-custom {
            background-color: #f44336;
            color: white;
            font-size: 0.75rem;
            padding: 3px 8px;
            border-radius: 4px;
            line-height: 1.3;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: auto;
            max-width: 70px;
            border: none;
            outline: none;
            box-shadow: none;
            margin: 0; /* Eliminar cualquier margen */
        }

        .btn-danger-custom:focus {
            box-shadow: none;
            border: none;
            margin: 0; /* Asegurar que no haya margen al hacer clic o enfocar */
        }



        .btn-danger-custom:hover {
            background-color: #e53935;
        }
        .form-container {
            margin-top: 50px;
        }
        .form-container input, .form-container select, .form-container button {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
        }
        .form-container .row {
            margin-bottom: 15px;
        }
        .form-container .col-md-4, .form-container .col-md-3 {
            padding: 0 10px;
        }
        .form-container input, .form-container select {
            margin-bottom: 10px;
        }
        .table-responsive {
            margin-top: 20px;
        }
        .table th, .table td {
            font-size: 0.9rem;
        }
        .form-container form {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            padding: 20px;
        }
        .btn {
            font-size: 0.95rem;
        }
        .btn-warning {
            font-size: 0.85rem;
            padding: 5px 10px;
        }
    </style>
</head>
<body>
<?php include '../partials/navbar.php'; ?>

<div class="container mt-5 form-container">
    <h1 class="text-center mb-4" style="text-decoration: bold; color: orange; text-align: center;">Administración de Sucursales</h1>
    
    <!-- Formulario para crear o editar sucursal -->
    <!-- <form method="POST">
        <div class="row">
            <div class="col-md-6">
                <input type="text" name="nombre" class="form-control" placeholder="Nombre de la Sucursal" value="<?= $sucursalParaEditar['nombre'] ?? '' ?>" required>
            </div>
            <div class="col-md-6">
                <input type="text" name="direccion" class="form-control" placeholder="Dirección" value="<?= $sucursalParaEditar['direccion'] ?? '' ?>" required>
            </div>
            <div class="col-md-6">
                <select name="responsable" class="form-select" required>
                    <option value="" selected disabled>Responsable</option>
                    <option value="4" <?= isset($sucursalParaEditar) && $sucursalParaEditar['responsable_id'] == 4 ? 'selected' : '' ?>>Responsable Centro</option>
                    <option value="5" <?= isset($sucursalParaEditar) && $sucursalParaEditar['responsable_id'] == 5 ? 'selected' : '' ?>>Responsable Norte</option>
                    <option value="6" <?= isset($sucursalParaEditar) && $sucursalParaEditar['responsable_id'] == 6 ? 'selected' : '' ?>>Responsable Sur</option>
                </select>
            </div>
            <div class="col-md-6 mt-2">
                <?php if ($sucursalParaEditar): ?>
                    <input type="hidden" name="id" value="<?= $sucursalParaEditar['id'] ?>">
                    <button type="submit" name="editar" class="btn btn-primary w-100">Editar Sucursal</button>
                <?php else: ?>
                    <button type="submit" name="crear" class="btn btn-success w-100">Crear Sucursal</button>
                <?php endif; ?>
            </div>
        </div>
    </form> -->

    <!-- Tabla de sucursales -->
    <div class="table-responsive mt-4">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Dirección</th>
                    <th>Responsable</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($sucursal = $sucursales->fetch(PDO::FETCH_ASSOC)): ?>
                    <tr>
                        <td><?= htmlspecialchars($sucursal['nombre']) ?></td>
                        <td><?= htmlspecialchars($sucursal['direccion']) ?></td>
                        <td><?= htmlspecialchars($sucursal['responsable_nombre'] ?? 'Sin responsable') ?></td>
                        <td>
                            <!-- <a href="sucursales.php?editar=<?= $sucursal['id'] ?>" class="btn btn-warning btn-sm">Editar</a> -->
                            <a href="detalles_sucursal.php?id=<?= $sucursal['id'] ?>" class="btn btn-info btn-sm">Ver Detalles</a>
                            <!-- <form method="POST" style="display:inline; margin: 0; padding: 0;">
                                <input type="hidden" name="id" value="<?= $sucursal['id'] ?>">
                                <button type="button" class="btn btn-danger-custom" data-bs-toggle="modal" data-bs-target="#confirmarEliminarModal">Eliminar</button>
                            </form> -->
                        </td>

                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal de confirmación para eliminar -->
<div class="modal fade" id="confirmarEliminarModal" tabindex="-1" aria-labelledby="confirmarEliminarModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmarEliminarModalLabel">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                ¿Estás seguro de que deseas eliminar esta sucursal? Esta acción no se puede deshacer.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="id" value="<?= $sucursal['id'] ?>">
                    <button type="submit" name="eliminar" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
