<?php
// Configuración inicial
include_once '../backend/db.php'; // Ruta correcta desde 'views'

session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

$ventas = [];
$productos = [];
$ventasPorPagina = 10; // Número de ventas a mostrar por página
$paginaActual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($paginaActual - 1) * $ventasPorPagina;
$filtroFecha = isset($_POST['filtro_fecha']) ? $_POST['filtro_fecha'] : 'dia'; // Día, semana, mes
$error = "";

// Función para obtener las ventas
function obtenerVentas($pdo, $offset, $ventasPorPagina, $filtroFecha)
{
    $fechaCondicion = '';
    switch ($filtroFecha) {
        case 'semana':
            $fechaCondicion = "WHERE v.fecha >= CURDATE() - INTERVAL 1 WEEK";
            break;
        case 'mes':
            $fechaCondicion = "WHERE v.fecha >= CURDATE() - INTERVAL 1 MONTH";
            break;
        case 'dia':
        default:
            $fechaCondicion = "WHERE DATE(v.fecha) = CURDATE()";
            break;
    }

    try {
        $queryVentas = $pdo->prepare("SELECT v.id, v.fecha, p.nombre AS producto, v.cantidad, 
                                      COALESCE(p.precio, 0) AS precio_unitario,
                                      (v.cantidad * COALESCE(p.precio, 0)) AS precio_total,
                                      v.nit
                                      FROM ventas v
                                      JOIN productos p ON v.producto = p.id $fechaCondicion
                                      LIMIT :offset, :ventasPorPagina");
        $queryVentas->bindParam(':offset', $offset, PDO::PARAM_INT);
        $queryVentas->bindParam(':ventasPorPagina', $ventasPorPagina, PDO::PARAM_INT);
        $queryVentas->execute();
        return $queryVentas->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Error al realizar la consulta: " . $e->getMessage());
    }
}

// Obtener productos disponibles
function obtenerProductos($pdo)
{
    try {
        $queryProductos = $pdo->prepare("SELECT id, nombre, precio, cantidad FROM productos");
        $queryProductos->execute();
        return $queryProductos->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Error al obtener productos: " . $e->getMessage());
    }
}

if (isset($pdo)) {
    $ventas = obtenerVentas($pdo, $offset, $ventasPorPagina, $filtroFecha);
    $productos = obtenerProductos($pdo);
}

// Procesar formulario de venta
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['producto'], $_POST['cantidad'])) {
    $productoId = (int)$_POST['producto'];
    $cantidad = (int)$_POST['cantidad'];
    $nit = isset($_POST['nit']) ? $_POST['nit'] : null;

    if ($productoId > 0 && $cantidad > 0) {
        try {
            // Verificar cantidad disponible
            $queryProducto = $pdo->prepare("SELECT cantidad FROM productos WHERE id = ?");
            $queryProducto->execute([$productoId]);
            $producto = $queryProducto->fetch(PDO::FETCH_ASSOC);

            if ($producto && $producto['cantidad'] >= $cantidad) {
                // Registrar la venta
                $queryVenta = $pdo->prepare("INSERT INTO ventas (producto, cantidad, fecha, sucursal_id, nit) 
                                             VALUES (?, ?, NOW(), ?, ?)");
                $sucursalId = 1; // Aquí puedes cambiarlo si lo necesitas
                $queryVenta->execute([$productoId, $cantidad, $sucursalId, $nit]);

                // Actualizar cantidad del producto
                $queryActualizar = $pdo->prepare("UPDATE productos SET cantidad = cantidad - ? WHERE id = ?");
                $queryActualizar->execute([$cantidad, $productoId]);

                header('Location: ventas.php'); // Recargar para actualizar la lista
                exit();
            } else {
                $error = "Cantidad insuficiente para el producto seleccionado.";
            }
        } catch (PDOException $e) {
            die("Error al registrar la venta: " . $e->getMessage());
        }
    } else {
        $error = "Por favor, seleccione un producto y una cantidad válida.";
    }
}

// Procesar formulario de venta múltiple
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['producto'], $_POST['cantidad'])) {
    $nit = isset($_POST['nit']) ? $_POST['nit'] : null;

    try {
        $pdo->beginTransaction(); // Iniciar transacción

        // 1. Insertar en ventas_maestras
        $queryVentaMaestra = $pdo->prepare("INSERT INTO ventas_maestras (nit, fecha, sucursal_id) VALUES (?, NOW(), ?)");
        $sucursalId = 1; // Sucursal estática, ajusta si es necesario
        $queryVentaMaestra->execute([$nit, $sucursalId]);
        $idVentaMaestra = $pdo->lastInsertId(); // Obtener el ID de la venta maestra

        // 2. Procesar cada producto seleccionado
        foreach ($_POST['producto'] as $index => $productoId) {
            $productoId = (int)$productoId;
            $cantidad = (int)$_POST['cantidad'][$index];

            if ($productoId > 0 && $cantidad > 0) {
                // Verificar cantidad disponible
                $queryProducto = $pdo->prepare("SELECT cantidad FROM productos WHERE id = ?");
                $queryProducto->execute([$productoId]);
                $producto = $queryProducto->fetch(PDO::FETCH_ASSOC);

                if ($producto && $producto['cantidad'] >= $cantidad) {
                    // Registrar la venta individual
                    $queryVenta = $pdo->prepare("INSERT INTO ventas (producto, cantidad, fecha, sucursal_id, nit, id_venta_maestra) 
                                                 VALUES (?, ?, NOW(), ?, ?, ?)");
                    $queryVenta->execute([$productoId, $cantidad, $sucursalId, $nit, $idVentaMaestra]);

                    // Actualizar cantidad del producto
                    $queryActualizar = $pdo->prepare("UPDATE productos SET cantidad = cantidad - ? WHERE id = ?");
                    $queryActualizar->execute([$cantidad, $productoId]);
                } else {
                    throw new Exception("Cantidad insuficiente para el producto ID: $productoId.");
                }
            }
        }

        $pdo->commit(); // Confirmar transacción
        header('Location: ventas.php'); // Redirigir
        exit();
    } catch (Exception $e) {
        $pdo->rollBack(); // Revertir transacción
        die("Error al registrar la venta: " . $e->getMessage());
    }
}

$queryVentasMaestras = $pdo->prepare("
    SELECT 
        vm.id AS id_venta_maestra,
        vm.fecha,
        vm.nit,
        GROUP_CONCAT(CONCAT(p.nombre, ' (', v.cantidad, ')') SEPARATOR ', ') AS productos_vendidos,
        SUM(v.cantidad * p.precio) AS precio_total
    FROM 
        ventas_maestras vm
    LEFT JOIN 
        ventas v ON vm.id = v.id_venta_maestra
    LEFT JOIN 
        productos p ON v.producto = p.id
    GROUP BY 
        vm.id, vm.fecha, vm.nit
");
$queryVentasMaestras->execute();
$ventasMaestras = $queryVentasMaestras->fetchAll(PDO::FETCH_ASSOC);


// Obtener filtro seleccionado
$filtroFecha = $_POST['filtro_fecha'] ?? '';

// Determinar rango de fechas según el filtro
$fechaInicio = '';
$fechaFin = date('Y-m-d'); // Fecha actual por defecto

switch ($filtroFecha) {
    case 'dia':
        $fechaInicio = $fechaFin; // Solo hoy
        break;
    case 'semana':
        $fechaInicio = date('Y-m-d', strtotime('monday this week'));
        break;
    case 'mes':
        $fechaInicio = date('Y-m-01'); // Primer día del mes
        break;
    default:
        $fechaInicio = ''; // Sin filtro
}

// Consulta para obtener ventas múltiples con el filtro de fecha
try {
    $queryVentasMultiples = "
        SELECT vm.id, vm.fecha, vm.nit, s.nombre AS sucursal
        FROM ventas_maestras vm
        JOIN sucursales s ON vm.sucursal_id = s.id
    ";

    // Agregar filtro de fecha a la consulta si se seleccionó
    if ($fechaInicio) {
        $queryVentasMultiples .= " WHERE vm.fecha BETWEEN :fechaInicio AND :fechaFin";
    }

    $stmtVentasMultiples = $pdo->prepare($queryVentasMultiples);

    // Vincular parámetros si hay filtro
    if ($fechaInicio) {
        $stmtVentasMultiples->bindParam(':fechaInicio', $fechaInicio);
        $stmtVentasMultiples->bindParam(':fechaFin', $fechaFin);
    }

    $stmtVentasMultiples->execute();
    $ventasMultiples = $stmtVentasMultiples->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener ventas múltiples: " . $e->getMessage());
}

// Definir la condición de fecha para la consulta total
$fechaCondicion = '';
switch ($filtroFecha) {
    case 'semana':
        $fechaCondicion = "WHERE v.fecha >= CURDATE() - INTERVAL 1 WEEK";
        break;
    case 'mes':
        $fechaCondicion = "WHERE v.fecha >= CURDATE() - INTERVAL 1 MONTH";
        break;
    case 'dia':
    default:
        $fechaCondicion = "WHERE DATE(v.fecha) = CURDATE()";
        break;
}

// Obtener el total de ventas para la paginación
$queryTotalVentas = $pdo->prepare("SELECT COUNT(*) AS total FROM ventas v JOIN productos p ON v.producto = p.id $fechaCondicion");
$queryTotalVentas->execute();
$totalVentas = $queryTotalVentas->fetch(PDO::FETCH_ASSOC)['total'];
$totalPaginas = ceil($totalVentas / $ventasPorPagina);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/estilos.css">
    <link rel="icon" href="../assets/img/icono.png">
    <title>Ventas - <?= isset($APP_NAME) ? htmlspecialchars($APP_NAME) : 'Panadería' ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin-top: 60px;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            flex: 1;
        }

        h1 {
            color: #343a40;
            margin-bottom: 20px;
        }

        .table {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            overflow-x: auto;
        }

        .table-striped tbody tr:nth-child(odd) {
            background-color: #f2f2f2;
        }

        .form-container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .form-container h2 {
            margin-bottom: 15px;
        }

        .form-container form {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }

        .form-container form select,
        .form-container form input,
        .form-container form button {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            flex: 1 1 100%;
        }

        .form-container form button {
            background-color: #28a745;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }

        .form-container form button:hover {
            background-color: #218838;
        }

        .footer {
            background-color: #e67e22;
            color: white;
            padding: 10px 0;
            text-align: center;
            margin-top: auto;
        }

        @media (min-width: 768px) {
            .form-container form select,
            .form-container form input {
                flex: 1;
            }

            .form-container form button {
                flex: 0 0 auto;
                margin-left: auto;
            }
        }

        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }
            table {
                font-size: 14px;
            }
            form input, form select, form button {
                width: 100%;
            }
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
        }

        .pagination {
            display: flex;
            justify-content: center;
            margin: 20px 0;
        }

        .pagination a {
            padding: 8px 16px;
            margin: 0 5px;
            text-decoration: none;
            color: #007bff;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .pagination a:hover {
            background-color: #f2f2f2;
        }
        
        .nit-column {
            width: 200px; /* Ajusta el valor según lo necesites */
        }
        /* Estilos aquí */
    </style>
</head>
<body>
    <?php include '../partials/navbar.php'; ?>

    <div class="container">
        <h1 style="text-decoration: bold; color: orange; text-align: center;">Ventas</h1>

         <!-- Formulario para registrar ventas -->
       <!--  <div class="form-container">
            <h2>Registrar Venta</h2>
            <?php if (isset($error)): ?>
                <p style="color: red;">
                    <?= htmlspecialchars($error) ?>
                </p>
            <?php endif; ?>
            <form method="POST">
                <select name="producto" required>
                    <option value="">Seleccione un producto</option>
                    <?php foreach ($productos as $producto): ?>
                        <option value="<?= htmlspecialchars($producto['id']) ?>">
                            <?= htmlspecialchars($producto['nombre']) ?> - Bs <?= htmlspecialchars($producto['precio']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="number" name="cantidad" placeholder="Cantidad" min="1" required>
                <label for="nit">NIT (opcional)</label>
                <input type="text" name="nit" placeholder="NIT para factura (opcional)">
                <button type="submit">Vender</button>
            </form>
        </div> -->

        <!-- Filtros de fecha -->
        <!-- <form method="POST">
            <label for="filtro_fecha">Filtrar ventas por:</label>
            <select name="filtro_fecha" id="filtro_fecha">
                <option value="dia" <?= $filtroFecha == 'dia' ? 'selected' : '' ?>>Hoy</option>
                <option value="semana" <?= $filtroFecha == 'semana' ? 'selected' : '' ?>>Esta semana</option>
                <option value="mes" <?= $filtroFecha == 'mes' ? 'selected' : '' ?>>Este mes</option>
            </select>
            <button type="submit" style="margin-top: 5px;">Filtrar</button>
        </form> -->

        <!-- Tabla de ventas registradas -->
        <!-- <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Fecha</th>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario (Bs)</th>
                    <th>Precio Total (Bs)</th>
                    <th>NIT</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($ventas)): ?>
                    <?php foreach ($ventas as $venta): ?>
                        <tr>
                            <td><?= htmlspecialchars($venta['id']) ?></td>
                            <td><?= htmlspecialchars($venta['fecha']) ?></td>
                            <td><?= htmlspecialchars($venta['producto']) ?></td>
                            <td><?= htmlspecialchars($venta['cantidad']) ?></td>
                            <td><?= htmlspecialchars($venta['precio_unitario']) ?></td>
                            <td><?= htmlspecialchars($venta['precio_total']) ?></td>
                            <td><?= htmlspecialchars($venta['nit']) ?></td>
                            <td><a href="imprimir_venta.php?id=<?= $venta['id'] ?>" target="_blank">Imprimir</a></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8">No hay ventas registradas.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table> -->

        <!-- Paginación -->
        <!-- <div class="pagination">
            <?php if ($paginaActual > 1): ?>
                <a href="?pagina=<?= $paginaActual - 1 ?>&filtro_fecha=<?= $filtroFecha ?>">« Anterior</a>
            <?php endif; ?>
            <span>Página <?= $paginaActual ?> de <?= $totalPaginas ?></span>
            <?php if ($paginaActual < $totalPaginas): ?>
                <a href="?pagina=<?= $paginaActual + 1 ?>&filtro_fecha=<?= $filtroFecha ?>">Siguiente »</a>
            <?php endif; ?>
        </div> -->
    </div>
    <!-- Formulario para registrar ventas múltiples -->
    <div class="form-container">
        <h2>Registrar Venta - Varios Productos</h2>
        <form id="form-venta-multiple" method="POST">
            <div id="productos-container">
                <div class="producto-item">
                    <select name="producto[]" required>
                        <option value="">Seleccione un producto</option>
                        <?php foreach ($productos as $producto): ?>
                            <option value="<?= htmlspecialchars($producto['id']) ?>">
                                <?= htmlspecialchars($producto['nombre']) ?> - Bs <?= htmlspecialchars($producto['precio']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <input type="number" name="cantidad[]" placeholder="Cantidad" min="1" value="0" required>
                </div>
            </div>
            <button type="button" id="agregar-producto">Agregar Otro Producto</button>
            <label for="nit">NIT (opcional):</label>
            <input type="text" name="nit" placeholder="NIT para factura (opcional)">
            <br>
            <button type="submit">Registrar Venta</button>
            <button type="button" id="cancelar-venta" style="background-color: red; color: white;">Cancelar Venta</button>
        </form>
    </div>

    <script>
        // Script para agregar dinámicamente otro producto
        document.getElementById('agregar-producto').addEventListener('click', function () {
            const nuevoProducto = document.createElement('div');
            nuevoProducto.classList.add('producto-item');
            nuevoProducto.innerHTML = `
                <select name="producto[]" required>
                    <option value="">Seleccione un producto</option>
                    <?php foreach ($productos as $producto): ?>
                        <option value="<?= htmlspecialchars($producto['id']) ?>">
                            <?= htmlspecialchars($producto['nombre']) ?> - Bs <?= htmlspecialchars($producto['precio']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="number" name="cantidad[]" placeholder="Cantidad" min="1" value="0" required>
            `;
            document.getElementById('productos-container').appendChild(nuevoProducto);
        });

        // Script para cancelar la venta
        document.getElementById('cancelar-venta').addEventListener('click', function () {
            const formulario = document.getElementById('form-venta-multiple');
            if (confirm("¿Estás seguro de que deseas cancelar la venta? Se perderán todos los datos ingresados.")) {
                formulario.reset(); // Resetea todos los campos del formulario
                document.getElementById('productos-container').innerHTML = `
                    <div class="producto-item">
                        <select name="producto[]" required>
                            <option value="">Seleccione un producto</option>
                            <?php foreach ($productos as $producto): ?>
                                <option value="<?= htmlspecialchars($producto['id']) ?>">
                                    <?= htmlspecialchars($producto['nombre']) ?> - Bs <?= htmlspecialchars($producto['precio']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="number" name="cantidad[]" placeholder="Cantidad" min="1" value="0" required>
                    </div>
                `;
            }
        });
    </script>

     <!-- Filtros de fecha -->
     <form method="POST" style="margin-bottom: 5px;">
        <label for="filtro_fecha">Filtrar ventas por:</label>
        <select name="filtro_fecha" id="filtro_fecha">
            <option value="dia" <?= $filtroFecha == 'dia' ? 'selected' : '' ?>>Hoy</option>
            <option value="semana" <?= $filtroFecha == 'semana' ? 'selected' : '' ?>>Esta semana</option>
            <option value="mes" <?= $filtroFecha == 'mes' ? 'selected' : '' ?>>Este mes</option>
        </select>
        <button type="submit" style="margin-top: 10px;">Filtrar</button>
    </form>

    <div class="form-container">
        <h2>Ventas Múltiples Realizadas</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID Venta Maestra</th>
                    <th>Fecha</th>
                    <th>NIT</th>
                    <th>Productos Vendidos</th>
                    <th>Precio Total (Bs)</th>
                    <th>Acciones</th> <!-- Nueva columna para imprimir -->
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($ventasMaestras)): ?>
                    <?php foreach ($ventasMaestras as $venta): ?>
                        <tr>
                            <td><?= htmlspecialchars($venta['id_venta_maestra']) ?></td>
                            <td><?= htmlspecialchars($venta['fecha']) ?></td>
                            <td><?= htmlspecialchars($venta['nit']) ?></td>
                            <td><?= htmlspecialchars($venta['productos_vendidos']) ?></td>
                            <td><?= number_format($venta['precio_total'], 2) ?></td>
                            <td>
                                <!-- Botón para imprimir factura -->
                                <form method="GET" action="imprimir_factura.php" target="_blank">
                                    <input type="hidden" name="id_venta_maestra" value="<?= $venta['id_venta_maestra'] ?>">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        Imprimir Factura
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">No hay ventas múltiples registradas.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>


    <!-- <script>
        document.getElementById('agregar-producto').addEventListener('click', function() {
            const container = document.getElementById('productos-container');
            const nuevoProducto = document.querySelector('.producto-item').cloneNode(true);
            container.appendChild(nuevoProducto);
        });
    </script> -->
</body>
</html>
