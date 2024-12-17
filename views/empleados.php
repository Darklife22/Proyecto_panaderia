<?php
include '../backend/empleados.php';

if (isset($_GET['sucursal_id'])) {
    $sucursal_id = $_GET['sucursal_id'];
    $empleados = obtenerEmpleadosPorSucursal($sucursal_id);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['crear'])) {
        crearEmpleado($_POST['nombre'], $sucursal_id);
        header("Location: empleados.php?sucursal_id=$sucursal_id");
    } elseif (isset($_POST['eliminar'])) {
        eliminarEmpleado($_POST['id']);
        header("Location: empleados.php?sucursal_id=$sucursal_id");
    }
}
?>
