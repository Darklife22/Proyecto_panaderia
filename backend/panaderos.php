<?php
include 'db.php';

function obtenerPanaderos() {
    global $conn;
    $query = "SELECT * FROM usuarios WHERE rol = 'panadero'";
    return $conn->query($query);
}

function crearPanadero($nombre, $email, $password) {
    return crearUsuario($nombre, $email, $password, 'panadero');
}

function eliminarPanadero($id) {
    return eliminarUsuario($id);
}
?>
