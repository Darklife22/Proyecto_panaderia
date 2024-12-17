<?php
session_start();
if ($_SESSION['rol'] !== 'maestro_panadero') {
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
    <title>Maestro Panadero</title>
</head>
<body>
<?php include '../partials/navbar.php'; ?>
<div class="container mt-5">
    <h2>Panel del Maestro Panadero</h2>
    <p>Aquí puedes gestionar los panaderos de las sucursales asignadas.</p>
    <a href="panaderos.php" class="btn btn-primary">Gestionar Panaderos</a>
</div>
<?php include '../partials/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/scripts.js"></script>
</body>
</html>
