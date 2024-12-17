<?php
include 'db.php';

function obtenerUsuariosPorRol($rol) {
    global $conn;
    $query = "SELECT * FROM usuarios WHERE rol = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $rol);
    $stmt->execute();
    return $stmt->get_result();
}

function crearUsuario($nombre, $email, $password, $rol) {
    global $conn;
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $query = "INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssss", $nombre, $email, $passwordHash, $rol);
    return $stmt->execute();
}

function eliminarUsuario($id) {
    global $conn;
    $query = "DELETE FROM usuarios WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}
?>
