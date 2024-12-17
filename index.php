<?php
// Configuración inicial
include_once 'backend/db.php';
define('APP_NAME', 'Panadería XYZ');
session_start();

// Verifica si el usuario está autenticado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: views/login.php'); // Redirige al login si no está autenticado
    exit();
}

// Obtener datos de la base de datos
function obtenerDatosCentral($pdo) {
    // Consulta para obtener los productos más vendidos globalmente
    $consulta = "SELECT producto, SUM(cantidad) AS total_vendido FROM ventas GROUP BY producto ORDER BY total_vendido DESC LIMIT 5";
    $stmt = $pdo->prepare($consulta);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function obtenerDatosSucursales($pdo) {
    // Consulta para obtener sucursales y productos más vendidos por cada una
    $consulta = "SELECT sucursales.nombre AS sucursal, ventas.producto, SUM(ventas.cantidad) AS total_vendido 
                 FROM ventas 
                 JOIN sucursales ON ventas.sucursal_id = sucursales.id 
                 GROUP BY sucursales.nombre, ventas.producto 
                 ORDER BY sucursales.nombre, total_vendido DESC";
    $stmt = $pdo->prepare($consulta);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Obtener datos
$datosCentral = obtenerDatosCentral($pdo);
$datosSucursales = obtenerDatosSucursales($pdo);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/estilos.css">
    <link rel="icon" href="assets/img/icono.png">
    <title>Dashboard - <?= APP_NAME ?></title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Estilos generales */
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin-top: 60px; /* Margen para el navbar */
            min-height: 100vh; /* Asegura que el contenido ocupe al menos el 100% de la altura de la página */
            display: flex;
            flex-direction: column;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            flex: 1;
        }

        h1, h2, h3 {
            color: #343a40;
        }

        /* Estilos de las secciones */
        .section {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 40px; /* Separación entre secciones */
        }

        /* Encabezados de las secciones */
        .section h2 {
            color: #e67e22; /* Naranja para los encabezados */
            font-size: 2rem;
            border-bottom: 2px solid #e67e22;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .section h3 {
            color: #e67e22;
        }

        /* Estilo para los gráficos generales */
        .grafico-general-container {
            width: 100%;
            max-width: 800px;
            margin: 20px auto;
        }

        .grafico-general {
            width: 100% !important;
            height: auto !important;
        }

        /* Estilo para las gráficas de sucursales */
        .grafico-sucursal-container {
            margin-bottom: 30px;
            display: flex;
            justify-content: center;
            border: 1px solid #e67e22; /* Borde naranja para separar cada sucursal */
            padding: 10px;
            border-radius: 8px;
            flex-direction: column;
            align-items: center; /* Centra los elementos dentro del contenedor */
        }

        .grafico-sucursal {
            height: 250px !important;
            width: 100% !important;
            background-color: transparent;
            border-radius: 8px;
        }

        /* Estilos responsivos */
        @media (max-width: 1200px) {
            .grafico-general-container {
                width: 90%;
            }

            .grafico-sucursal-container {
                max-width: 300px;
            }

            .grafico-sucursal {
                height: 200px;
            }
        }

        @media (max-width: 768px) {
            .grafico-general-container {
                width: 90%;
                max-width: 100%;
            }

            .grafico-sucursal-container {
                width: 100%;
                margin-bottom: 20px;
            }

            .grafico-sucursal {
                height: 200px;
            }

            .section h2 {
                font-size: 1.5rem;
            }

            .section h3 {
                font-size: 1.2rem;
            }

            .grafico-row {
                display: block;
            }

            /* Hacer gráfico de barras general más grande en responsivo */
            .grafico-general-container {
                max-width: 100%;
            }

            .grafico-general {
                height: 350px !important; /* Aumenta el tamaño en dispositivos móviles */
            }
        }

        /* Disposición en columna para gráficos de las sucursales */
        .grafico-row {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center; /* Centra los gráficos */
        }

        /* Estilo para las tarjetas */
        .card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border: none;
            margin-bottom: 20px;
            background-color: #fff;
        }

        .card-body {
            padding: 20px;
        }

        /* Navbar */
        .navbar {
            background-color: #e67e22; /* Naranja */
            padding: 10px;
        }

        .navbar a {
            color: #fff;
            text-decoration: none;
            padding: 10px;
            font-size: 1.1rem;
        }

        .navbar a:hover {
            background-color: #d35400;
            border-radius: 5px;
        }

        /* Pie de página */
        .footer {
            background-color: #e67e22;
            color: white;
            padding: 10px 0;
            text-align: center;
            margin-top: auto; /* Asegura que el pie de página se quede al final */
        }
    </style>
</head>
<body>
<?php include 'partials/navbar.php'; ?>
<div class="container mt-5">
    <!-- Sección Central -->
    <section class="section">
        <h2>Resumen General</h2>
        <div class="row">
            <div class="col-12 col-md-6 grafico-general-container">
                <canvas id="graficoCentral" class="grafico-general"></canvas>
            </div>
            <div class="col-12 col-md-6">
                <ul>
                    <?php foreach ($datosCentral as $producto): ?>
                        <li><?= $producto['producto'] ?>: <?= $producto['total_vendido'] ?> vendidos</li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </section>

    <!-- Sección de Sucursales -->
    <section class="section">
        <h2>Rendimiento por Sucursal</h2>
        <div class="grafico-row">
            <?php 
            $sucursales = [];
            foreach ($datosSucursales as $dato) {
                $sucursales[$dato['sucursal']][] = $dato;
            }
            ?>
            <?php foreach ($sucursales as $sucursal => $productos): ?>
                <div class="grafico-sucursal-container">
                    <h3><?= $sucursal ?></h3>
                    <div class="grafico-sucursal">
                        <canvas id="graficoSucursal_<?= md5($sucursal) ?>" class="grafico-sucursal"></canvas>
                    </div>
                    <script>
                        const ctx<?= md5($sucursal) ?> = document.getElementById('graficoSucursal_<?= md5($sucursal) ?>').getContext('2d');
                        new Chart(ctx<?= md5($sucursal) ?>, {
                            type: 'pie', // Gráfico de pastel
                            data: {
                                labels: <?= json_encode(array_column($productos, 'producto')) ?>,
                                datasets: [{
                                    data: <?= json_encode(array_column($productos, 'total_vendido')) ?>,
                                    backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF'],
                                }]
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    legend: {
                                        position: 'top',
                                    },
                                    tooltip: {
                                        enabled: true, // Activar el tooltip
                                        callbacks: {
                                            label: function(tooltipItem) {
                                                return tooltipItem.label + ': ' + tooltipItem.raw;
                                            }
                                        }
                                    }
                                },
                                maintainAspectRatio: true, // Mantiene la relación de aspecto
                            }
                        });
                    </script>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Indicadores Clave -->
    <section class="section">
        <h2>Indicadores Clave</h2>
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Clientes Frecuentes</h5>
                        <p class="card-text">120 clientes frecuentes este mes.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Ventas del Mes</h5>
                        <p class="card-text">Bs 50,000 en ventas este mes.</p> <!-- Precios en Bolivianos -->
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Metas Alcanzadas</h5>
                        <p class="card-text">4 de 5 metas alcanzadas.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Gráfico General -->
<script>
    const ctxCentral = document.getElementById('graficoCentral').getContext('2d');
    new Chart(ctxCentral, {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_column($datosCentral, 'producto')) ?>,
            datasets: [{
                label: 'Productos más vendidos',
                data: <?= json_encode(array_column($datosCentral, 'total_vendido')) ?>,
                backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF'],
                borderColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top'
                }
            },
            maintainAspectRatio: true
        }
    });
</script>

<!-- Pie de página -->
<div class="footer">
    <p>&copy; 2024 <?= APP_NAME ?>. Todos los derechos reservados.</p>
</div>

</body>
</html>
