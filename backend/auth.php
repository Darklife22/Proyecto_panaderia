<?php
include 'db.php';

function login($email, $password) {
    global $conn;
    $query = "SELECT * FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $usuario = $result->fetch_assoc();
        if (password_verify($password, $usuario['password'])) {
            session_start();
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['rol'] = $usuario['rol'];
            return true;
        }
    }
    return false;
}

function logout() {
    session_start();
    session_destroy();
    header('Location: ../views/login.php');
}
?>
