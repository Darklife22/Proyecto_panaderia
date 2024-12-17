<footer class="bg-dark text-white text-center py-3 mt-5">
    <p>&copy; <?= date('Y') ?> <?= APP_NAME ?> - Todos los derechos reservados</p>
</footer>

<!-- Estilos para el pie de página -->
<style>
    /* Establece que el pie de página siempre esté al final, pero solo cuando el contenido no ocupa toda la pantalla */
    body, html {
        height: 100%;
    }

    .content {
        min-height: 100%;
        padding-bottom: 50px; /* Ajuste para que no se sobreponga al pie de página */
    }
    
    footer {
        position: relative;
        bottom: 0;
        width: 100%;
    }
</style>
