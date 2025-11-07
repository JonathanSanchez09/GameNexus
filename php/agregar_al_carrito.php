<?php
session_start();
include 'conexion.php';

// 💡 Nuevo: Redirige si el usuario no ha iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php?mensaje=" . urlencode("❌ Debes iniciar sesión para agregar productos al carrito."));
    exit();
}

// Verificar si el juego se está agregando al carrito
if (isset($_GET['juego_id'])) {
    $juego_id = (int)$_GET['juego_id'];
    $mensaje = "";

    if (!isset($_SESSION['carrito'])) {
        $_SESSION['carrito'] = [];
    }

    if (isset($_SESSION['carrito'][$juego_id])) {
        $mensaje = "❌ ¡Este juego ya está en tu carrito!";
    } else {
        $stmt = $conn->prepare("SELECT nombre, precio, imagen_url FROM juegos WHERE id = ?");
        $stmt->bind_param("i", $juego_id);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            $juego = $resultado->fetch_assoc();
            $_SESSION['carrito'][$juego_id] = [
                'nombre' => htmlspecialchars($juego['nombre']),
                'precio' => (float)$juego['precio'],
                'imagen' => htmlspecialchars($juego['imagen_url']),
                'cantidad' => 1
            ];
        } else {
            $mensaje = "❌ No se encontró el juego.";
        }
    }

    // Usar la sesión para almacenar el mensaje y evitar que se muestre en la URL
    $_SESSION['mensaje'] = $mensaje;
    
    // Redirigir de nuevo a la página anterior
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}
// 💡 Nuevo: Redirigir si no hay un ID de juego
header("Location: ../index.php");
exit();
?>