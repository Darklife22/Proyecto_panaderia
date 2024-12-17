<?php
include 'db.php';

function obtenerEmpleadosPorSucursal($sucursal_id) {
    global $conn;
    $query = "SELECT * FROM empleados WHERE sucursal_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $sucursal_id);
    $stmt->execute();
    return $stmt->get_result();
}

function crearEmpleado($nombre, $sucursal_id) {
    global $conn;
    $query = "INSERT INTO empleados (nombre, sucursal_id) VALUES (?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $nombre, $sucursal_id);
    return $stmt->execute();
}

function eliminarEmpleado($id) {
    global $conn;
    $query = "DELETE FROM empleados WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}
?>
