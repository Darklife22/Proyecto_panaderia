<?php
// Parámetros de conexión
$host = '127.0.0.1';          // El host de la base de datos (localhost en este caso)
$usuario = 'root';             // Usuario de MySQL (en tu caso 'root')
$contrasena = 'admin1234';     // Contraseña de MySQL (asegúrate de que esté correcta)
$nombreBaseDatos = 'panaderia'; // El nombre de tu base de datos

// Establecer la conexión
try {
    // Conexión PDO con manejo de excepciones
    $pdo = new PDO("mysql:host=$host;dbname=$nombreBaseDatos;charset=utf8mb4", $usuario, $contrasena);
    
    // Establecer atributos para manejo de errores y el modo de caracteres
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); // Deshabilita la emulación de consultas preparadas

    // Opcionalmente, puedes agregar una verificación adicional para asegurarte de que la conexión fue exitosa
    // Esto es útil si quieres ver un mensaje al conectar correctamente
    // echo "Conexión exitosa a la base de datos"; 
    
} catch (PDOException $e) {
    // En caso de error, muestra un mensaje de error detallado
    die("No se pudo conectar a la base de datos: " . $e->getMessage());
}
?>
