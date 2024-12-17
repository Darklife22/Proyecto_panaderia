<?php
include 'db.php';  // Incluir la conexión a la base de datos

function obtenerSucursales() {
    global $pdo;
    // Consulta con JOIN para obtener el nombre del responsable
    $query = "SELECT s.id, s.nombre, s.direccion, u.nombre AS responsable_nombre
              FROM sucursales s
              LEFT JOIN usuarios u ON s.responsable_id = u.id";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    
    return $stmt;  // Retorna el objeto PDOStatement con los resultados
}

function crearSucursal($nombre, $direccion, $responsable_id) {
    global $pdo;
    $query = "INSERT INTO sucursales (nombre, direccion, responsable_id) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($query);
    
    $stmt->bindValue(1, $nombre, PDO::PARAM_STR);
    $stmt->bindValue(2, $direccion, PDO::PARAM_STR);
    $stmt->bindValue(3, $responsable_id, PDO::PARAM_INT);
    
    return $stmt->execute();
}

function eliminarSucursal($id) {
    try {
        // Eliminar las ventas asociadas a la sucursal
        $query = "DELETE FROM ventas WHERE sucursal_id = :id";
        $stmt = $GLOBALS['pdo']->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        // Luego eliminar la sucursal
        $query = "DELETE FROM sucursales WHERE id = :id";
        $stmt = $GLOBALS['pdo']->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        echo "Sucursal eliminada correctamente.";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

function obtenerSucursalPorId($id) {
    global $pdo;
    // Modificar la consulta para incluir el nombre del responsable
    $query = "SELECT s.id, s.nombre, s.direccion, u.nombre AS responsable_nombre
              FROM sucursales s
              LEFT JOIN usuarios u ON s.responsable_id = u.id
              WHERE s.id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->bindValue(1, $id, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetch(PDO::FETCH_ASSOC);  // Retorna un array con la sucursal y el nombre del responsable
}

function actualizarSucursal($id, $nombre, $direccion, $responsable_id) {
    global $pdo;
    $query = "UPDATE sucursales SET nombre = ?, direccion = ?, responsable_id = ? WHERE id = ?";
    $stmt = $pdo->prepare($query);
    
    $stmt->bindValue(1, $nombre, PDO::PARAM_STR);
    $stmt->bindValue(2, $direccion, PDO::PARAM_STR);
    $stmt->bindValue(3, $responsable_id, PDO::PARAM_INT);
    $stmt->bindValue(4, $id, PDO::PARAM_INT);
    
    return $stmt->execute();
}
?>
