<?php
session_start();
if ($_SESSION['rol'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/estilos.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <title>Dashboard - Administrador</title>
</head>
<body>
<?php include '../partials/navbar.php'; ?>
<div class="container mt-5">
    <h2>Panel de Control del Administrador</h2>
    <div class="row">
        <div class="col-md-4">
            <a href="sucursales.php" class="btn btn-primary w-100 mb-3">Gestión de Sucursales</a>
        </div>
        <div class="col-md-4">
            <a href="panaderos.php" class="btn btn-primary w-100 mb-3">Gestión de Panaderos</a>
        </div>
        <div class="col-md-4">
            <a href="usuarios.php" class="btn btn-primary w-100 mb-3">Gestión de Usuarios</a>
        </div>
    </div>
</div>
<?php include '../partials/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/scripts.js"></script>
</body>
</html>
