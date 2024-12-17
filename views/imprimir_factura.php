<?php
// Configuración inicial
include_once '../backend/db.php'; // Ajusta la ruta según tu estructura

if (!isset($_GET['id_venta_maestra'])) {
    die("ID de venta maestra no proporcionado.");
}

$idVentaMaestra = (int)$_GET['id_venta_maestra'];

// Obtener detalles de la venta maestra
try {
    $queryVentaMaestra = $pdo->prepare("
        SELECT vm.id AS id_venta_maestra, vm.fecha, vm.nit, s.nombre AS sucursal
        FROM ventas_maestras vm
        JOIN sucursales s ON vm.sucursal_id = s.id
        WHERE vm.id = ?
    ");
    $queryVentaMaestra->execute([$idVentaMaestra]);
    $ventaMaestra = $queryVentaMaestra->fetch(PDO::FETCH_ASSOC);

    if (!$ventaMaestra) {
        die("Venta no encontrada.");
    }

    // Obtener los productos vendidos asociados a la venta maestra
    $queryProductos = $pdo->prepare("
        SELECT p.nombre AS producto, v.cantidad, p.precio, (v.cantidad * p.precio) AS total
        FROM ventas v
        JOIN productos p ON v.producto = p.id
        WHERE v.id_venta_maestra = ?
    ");
    $queryProductos->execute([$idVentaMaestra]);
    $productos = $queryProductos->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener la factura: " . $e->getMessage());
}

// Calcular el total general
$totalGeneral = array_reduce($productos, function ($carry, $item) {
    return $carry + $item['total'];
}, 0);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura - Venta #<?= htmlspecialchars($ventaMaestra['id_venta_maestra']) ?></title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 20px;
            background-color: #f8f8f8;
            color: #333;
        }
        .factura {
            max-width: 700px;
            margin: 0 auto;
            background-color: #fff;
            border: 1px solid #ddd;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
        .header, .footer {
            text-align: center;
            margin-bottom: 10px;
        }
        .logo {
            max-width: 100px;
            margin-bottom: 10px;
        }
        h1, h2 {
            margin: 0;
        }
        .info {
            margin-bottom: 20px;
        }
        .info p {
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
        .total {
            font-weight: bold;
            font-size: 1.2em;
        }
        .footer {
            font-size: 0.9em;
            color: #777;
        }
        .btn-imprimir {
            display: block;
            width: 150px;
            margin: 20px auto;
            padding: 10px;
            background-color: #007BFF;
            color: white;
            text-align: center;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            font-size: 1em;
        }
        .btn-imprimir:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="factura">
        <div class="header">
            <img src="../assets/img/logo.png" alt="Logo de la empresa" class="logo"> <!-- Ajusta la ruta de la imagen -->
            <h1>Panadería INPASEP</h1>
            <h2>Factura</h2>
        </div>

        <div class="info">
            <p><strong>NIT:</strong> <?= htmlspecialchars($ventaMaestra['nit'] ?: 'No proporcionado') ?></p>
            <p><strong>Sucursal:</strong> <?= htmlspecialchars($ventaMaestra['sucursal']) ?></p>
            <p><strong>Fecha:</strong> <?= htmlspecialchars($ventaMaestra['fecha']) ?></p>
            <p><strong>Venta #:</strong> <?= htmlspecialchars($ventaMaestra['id_venta_maestra']) ?></p>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario (Bs)</th>
                    <th>Total (Bs)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($productos as $producto): ?>
                    <tr>
                        <td><?= htmlspecialchars($producto['producto']) ?></td>
                        <td><?= htmlspecialchars($producto['cantidad']) ?></td>
                        <td><?= number_format($producto['precio'], 2) ?></td>
                        <td><?= number_format($producto['total'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="3" class="total">Total General</td>
                    <td class="total"><?= number_format($totalGeneral, 2) ?> Bs</td>
                </tr>
            </tbody>
        </table>

        <div class="footer">
            <p>Gracias por su compra.</p>
            <p>Sucursal: <?= htmlspecialchars($ventaMaestra['sucursal']) ?></p>
        </div>

        <button class="btn-imprimir" onclick="window.print();">Imprimir Factura</button>
    </div>
</body>
</html>
