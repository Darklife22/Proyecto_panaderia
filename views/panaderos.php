<?php
include '../backend/panaderos.php';
$panaderos = obtenerPanaderos();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['crear'])) {
        crearPanadero($_POST['nombre'], $_POST['email'], $_POST['password']);
        header('Location: panaderos.php');
    } elseif (isset($_POST['eliminar'])) {
        eliminarPanadero($_POST['id']);
        header('Location: panaderos.php');
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/estilos.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <title>Panaderos</title>
</head>
<body>
<?php include '../partials/navbar.php'; ?>
<div class="container mt-5">
    <h2>Panaderos</h2>
    <form method="POST" class="row mb-4">
        <div class="col-md-4">
            <input type="text" name="nombre" class="form-control" placeholder="Nombre del Panadero" required>
        </div>
        <div class="col-md-4">
            <input type="email" name="email" class="form-control" placeholder="Email" required>
        </div>
        <div class="col-md-2">
            <input type="password" name="password" class="form-control" placeholder="Contraseña" required>
        </div>
        <div class="col-md-2">
            <button type="submit" name="crear" class="btn btn-primary w-100">Crear</button>
        </div>
    </form>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Email</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($panadero = $panaderos->fetch_assoc()) { ?>
                <tr>
                    <td><?= $panadero['nombre'] ?></td>
                    <td><?= $panadero['email'] ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="id" value="<?= $panadero['id'] ?>">
                            <button type="submit" name="eliminar" class="btn btn-danger btn-sm">Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
