<?php
// Configuración inicial
include_once '../backend/db.php'; // Ruta correcta desde 'views'

session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

$ventaId = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
if (!$ventaId) {
    die("<p style='color: red; text-align: center;'>Error: ID de venta no válido.</p>");
}

try {
    // Conexión a la base de datos y consulta
    $queryVenta = $pdo->prepare(
        "SELECT v.id, v.fecha, p.nombre AS producto, v.cantidad, 
                COALESCE(p.precio, 0) AS precio_unitario,
                (v.cantidad * COALESCE(p.precio, 0)) AS precio_total,
                v.nit
         FROM ventas v
         JOIN productos p ON v.producto = p.id
         WHERE v.id = :ventaId"
    );
    $queryVenta->bindParam(':ventaId', $ventaId, PDO::PARAM_INT);
    $queryVenta->execute();
    $venta = $queryVenta->fetch(PDO::FETCH_ASSOC);

    if (!$venta) {
        die("<p style='color: red; text-align: center;'>Error: No se encontró la venta con el ID especificado.</p>");
    }
} catch (PDOException $e) {
    die("<p style='color: red; text-align: center;'>Error al obtener los datos: " . htmlspecialchars($e->getMessage()) . "</p>");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura de Venta</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f9f9f9;
        }
        .factura {
            max-width: 700px;
            margin: auto;
            background-color: white;
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .encabezado, .footer {
            text-align: center;
            margin-bottom: 20px;
        }
        .detalle {
            margin-top: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        .total {
            text-align: right;
            font-size: 1.2em;
            margin-top: 20px;
        }
        @media print {
            body {
                background: none;
            }
            .factura {
                box-shadow: none;
                border: none;
            }
            button {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="factura">
        <div class="encabezado">
            <h1>Panadería INPASEP</h1>
            <p>Av. Siempre Viva, 123 - Tel: (+591) 555-555</p>
        </div>

        <h2>Factura de Venta</h2>
        <div class="detalle">
            <table>
                <tr>
                    <th>ID de Venta</th>
                    <td><?= htmlspecialchars($venta['id']) ?></td>
                </tr>
                <tr>
                    <th>Fecha</th>
                    <td><?= htmlspecialchars($venta['fecha']) ?></td>
                </tr>
                <tr>
                    <th>Producto</th>
                    <td><?= htmlspecialchars($venta['producto']) ?></td>
                </tr>
                <tr>
                    <th>Cantidad</th>
                    <td><?= htmlspecialchars($venta['cantidad']) ?></td>
                </tr>
                <tr>
                    <th>Precio Unitario (Bs)</th>
                    <td><?= number_format($venta['precio_unitario'], 2) ?></td>
                </tr>
                <tr>
                    <th>Precio Total (Bs)</th>
                    <td><?= number_format($venta['precio_total'], 2) ?></td>
                </tr>
                <tr>
                    <th>NIT</th>
                    <td><?= htmlspecialchars($venta['nit']) ?></td>
                </tr>
            </table>
        </div>

        <div class="total">
            <p><strong>Total a pagar:</strong> <?= number_format($venta['precio_total'], 2) ?> Bs</p>
        </div>

        <div class="footer">
            <p>Gracias por su compra. ¡Vuelva pronto!</p>
        </div>

        <button onclick="window.print()">Imprimir Factura</button>
    </div>
</body>
</html>
