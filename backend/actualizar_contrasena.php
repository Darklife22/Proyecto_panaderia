<?php
// Parámetros de conexión
require_once '../backend/db.php'; // Ruta correcta al archivo db.php

try {
    // Obtener todos los usuarios y sus contraseñas
    $sql = "SELECT id, password FROM usuarios";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    // Iterar sobre cada usuario
    while ($usuario = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $usuario['id'];
        $password_actual = $usuario['password'];

        // Verificar si la contraseña ya está encriptada (opcional)
        if (!password_get_info($password_actual)['algo']) {
            // Encriptar la contraseña si no está encriptada
            $nueva_contrasena = password_hash($password_actual, PASSWORD_BCRYPT);

            // Actualizar la contraseña en la base de datos
            $update_sql = "UPDATE usuarios SET password = :password WHERE id = :id";
            $update_stmt = $pdo->prepare($update_sql);
            $update_stmt->bindParam(':password', $nueva_contrasena);
            $update_stmt->bindParam(':id', $id);
            $update_stmt->execute();

            echo "Contraseña para usuario con ID $id actualizada correctamente.<br>";
        } else {
            echo "Contraseña para usuario con ID $id ya está encriptada.<br>";
        }
    }

    echo "Todas las contraseñas han sido procesadas.";
} catch (PDOException $e) {
    echo "Error al procesar las contraseñas: " . $e->getMessage();
}
?>
