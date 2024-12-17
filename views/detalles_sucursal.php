<?php
include '../backend/sucursales.php';

if (!isset($_GET['id'])) {
    header('Location: sucursales.php'); // Redirigir si no hay ID
    exit();
}

$id = $_GET['id'];
$sucursal = obtenerSucursalPorId($id);

if (!$sucursal) {
    die('Sucursal no encontrada.');
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
    <title>Detalles de la Sucursal</title>
</head>
<body>
<?php include '../partials/navbar.php'; ?>

<div class="container mt-5">
    <h2 class="text-center mb-4">Detalles de la Sucursal</h2>
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title"><?= htmlspecialchars($sucursal['nombre']) ?></h5>
            <p class="card-text"><strong>Dirección:</strong> <?= htmlspecialchars($sucursal['direccion']) ?></p>
            <p class="card-text"><strong>Responsable:</strong> <?= htmlspecialchars($sucursal['responsable_nombre'] ?? 'Sin responsable') ?></p>
            <a href="sucursales.php" class="btn btn-primary">Volver</a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
