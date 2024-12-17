<?php
include '../backend/db.php'; // Conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar que todos los campos están presentes y no vacíos
    $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : null;
    $descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : null;
    $precio = isset($_POST['precio']) ? trim($_POST['precio']) : null;
    $cantidad = isset($_POST['cantidad']) ? trim($_POST['cantidad']) : null;

    // Comprobar si los campos requeridos no están vacíos
    if ($nombre && $descripcion && $precio !== null && $cantidad !== null) {
        try {
            // Inserción en la base de datos
            $sql = "INSERT INTO productos (nombre, descripcion, precio, cantidad, fecha_creacion) 
                    VALUES (:nombre, :descripcion, :precio, :cantidad, NOW())";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':descripcion', $descripcion);
            $stmt->bindParam(':precio', $precio);
            $stmt->bindParam(':cantidad', $cantidad);
            $stmt->execute();

            header("Location: registros.php?producto_agregado=true");
            exit();
        } catch (PDOException $e) {
            echo "Error al agregar producto: " . $e->getMessage();
        }
    } else {
        echo "Todos los campos son requeridos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Producto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Agregar Producto</h1>
        <form method="POST">
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre del Producto</label>
                <input type="text" class="form-control" id="nombre" name="nombre" required>
            </div>
            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción</label>
                <textarea class="form-control" id="descripcion" name="descripcion" required></textarea>
            </div>
            <div class="mb-3">
                <label for="precio" class="form-label">Precio</label>
                <input type="number" step="0.01" class="form-control" id="precio" name="precio" required>
            </div>
            <div class="mb-3">
                <label for="cantidad" class="form-label">Cantidad</label>
                <input type="number" class="form-control" id="cantidad" name="cantidad" required>
            </div>
            <button type="submit" class="btn btn-primary">Agregar</button>
            <a href="registros.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</body>
</html>
