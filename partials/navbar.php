<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panadería XYZ</title>
    <style>
        /* Reinicio de márgenes y paddings */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Asegurarse de que el estilo de la barra de navegación no sea alterado por otras reglas */
        html, body {
            font-family: Arial, sans-serif;
        }

        /* Barra de navegación con un prefijo específico */
        body .navbar-inpasep {
            background-color: #FFB347 !important; /* Naranja claro */
            padding: 15px 20px !important;
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1) !important; /* Sombra */
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 100% !important;
            z-index: 1000 !important;
        }

        /* Estilo de la marca */
        body .navbar-inpasep .marca {
            font-size: 1.8rem !important;
            color: white !important;
            font-weight: bold !important;
            text-decoration: none !important;
        }

        /* Estilo del menú */
        body .navbar-inpasep .menu {
            list-style: none !important;
            display: flex !important;
        }

        body .navbar-inpasep .menu li {
            margin-left: 20px !important;
        }

        body .navbar-inpasep .menu a {
            color: white !important;
            text-decoration: none !important;
            font-size: 1rem !important;
            font-weight: 500 !important;
            transition: color 0.3s ease !important;
        }

        body .navbar-inpasep .menu a:hover {
            color: #FFB347 !important; /* Naranja más fuerte */
        }

        /* Botón hamburguesa */
        body .navbar-inpasep .boton-menu {
            display: none !important;
            flex-direction: column !important;
            justify-content: space-around !important;
            width: 30px !important;
            height: 25px !important;
            background: transparent !important;
            border: none !important;
            cursor: pointer !important;
        }

        body .navbar-inpasep .linea {
            width: 30px !important;
            height: 3px !important;
            background-color: white !important;
            border-radius: 2px !important;
            transition: all 0.3s ease !important;
        }

        /* Menú desplegable en pantallas pequeñas */
        @media (max-width: 768px) {
            body .navbar-inpasep .menu {
                display: none !important;
                flex-direction: column !important;
                width: 100% !important;
                background-color: #FFB347 !important;
                position: absolute !important;
                top: 60px !important;
                left: 0 !important;
                padding: 20px 0 !important;
            }

            body .navbar-inpasep .menu.activo {
                display: flex !important;
            }

            body .navbar-inpasep .menu li {
                margin: 10px 0 !important;
                text-align: center !important;
            }

            body .navbar-inpasep .boton-menu {
                display: flex !important;
            }
        }
    </style>
</head>
<body>

    <!-- Barra de navegación con prefijo específico -->
    <nav class="navbar-inpasep">
        <a href="/panaderia/index.php" class="marca">INPASEP</a>

        <!-- Botón hamburguesa -->
        <button class="boton-menu" onclick="toggleMenu()">
            <div class="linea"></div>
            <div class="linea"></div>
            <div class="linea"></div>
        </button>

        <!-- Menú -->
        <ul class="menu">
            <li><a href="/panaderia/index.php">Inicio</a></li>
            <li><a href="/panaderia/views/productos.php">Productos</a></li>
            <li><a href="/panaderia/views/ventas.php">Ventas</a></li>
            <li><a href="/panaderia/views/sucursales.php">Sucursales</a></li>
            <li><a href="/panaderia/views/registros.php">Registros</a></li>
            <li><a href="/panaderia/logout.php">Cerrar sesión</a></li>
        </ul>
    </nav>

    <script>
        function toggleMenu() {
            const menu = document.querySelector('.navbar-inpasep .menu');
            const boton = document.querySelector('.navbar-inpasep .boton-menu');
            menu.classList.toggle('activo');
            boton.classList.toggle('abierto');
        }
    </script>
</body>
</html>
