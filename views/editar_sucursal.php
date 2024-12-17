<?php
include '../backend/db.php'; // Conexión a la base de datos

$id = $_GET['id'];

// Obtener los datos de la sucursal
$stmt = $pdo->prepare("SELECT * FROM sucursales WHERE id = :id");
$stmt->bindParam(':id', $id);
$stmt->execute();
$sucursal = $stmt->fetch(PDO::FETCH_ASSOC);

// Obtener la lista de responsables
$stmt_responsables = $pdo->query("SELECT id, nombre FROM usuarios");
$responsables = $stmt_responsables->fetchAll(PDO::FETCH_ASSOC);

// Procesar el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $direccion = $_POST['direccion'];
    $responsable_id = $_POST['responsable_id'];

    try {
        $sql = "UPDATE sucursales SET nombre = :nombre, direccion = :direccion, responsable_id = :responsable_id WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':direccion', $direccion);
        $stmt->bindParam(':responsable_id', $responsable_id);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        header("Location: registros.php?sucursal_actualizada=true");
        exit();
    } catch (PDOException $e) {
        echo "Error al actualizar sucursal: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <title>Editar Sucursal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h1>Editar Sucursal</h1>
    <form method="POST">
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre de la Sucursal</label>
            <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($sucursal['nombre']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="direccion" class="form-label">Dirección</label>
            <input type="text" name="direccion" class="form-control" value="<?= htmlspecialchars($sucursal['direccion']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="responsable_id" class="form-label">Responsable</label>
            <select name="responsable_id" class="form-select" required>
                <option value="">Seleccione un responsable</option>
                <?php foreach ($responsables as $responsable): ?>
                    <option value="<?= $responsable['id'] ?>" <?= $sucursal['responsable_id'] == $responsable['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($responsable['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-success">Guardar Cambios</button>
        <a href="registros.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
</body>
</html>
