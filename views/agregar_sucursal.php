<?php
include '../backend/db.php'; // Conexión a la base de datos

// Obtener la lista de responsables (usuarios)
$stmt_responsables = $pdo->query("SELECT id, nombre FROM usuarios");
$responsables = $stmt_responsables->fetchAll(PDO::FETCH_ASSOC);

// Procesar el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $direccion = $_POST['direccion'];
    $responsable_id = $_POST['responsable_id'];

    try {
        $sql = "INSERT INTO sucursales (nombre, direccion, responsable_id) VALUES (:nombre, :direccion, :responsable_id)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':direccion', $direccion);
        $stmt->bindParam(':responsable_id', $responsable_id);
        $stmt->execute();

        header("Location: registros.php?sucursal_agregada=true");
        exit();
    } catch (PDOException $e) {
        echo "Error al agregar sucursal: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <title>Agregar Sucursal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h1>Agregar Nueva Sucursal</h1>
    <form method="POST">
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre de la Sucursal</label>
            <input type="text" name="nombre" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="direccion" class="form-label">Dirección</label>
            <input type="text" name="direccion" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="responsable_id" class="form-label">Responsable</label>
            <select name="responsable_id" class="form-select" required>
                <option value="">Seleccione un responsable</option>
                <?php foreach ($responsables as $responsable): ?>
                    <option value="<?= $responsable['id'] ?>"><?= htmlspecialchars($responsable['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-success">Agregar Sucursal</button>
        <a href="registros.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
</body>
</html>
