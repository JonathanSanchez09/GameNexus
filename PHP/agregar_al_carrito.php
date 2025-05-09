<?php
session_start();
include 'conexion.php';

// Verificar si el juego se está agregando al carrito
if (isset($_GET['juego_id'])) {
    $juego_id = (int)$_GET['juego_id'];

    if (!isset($_SESSION['carrito'])) {
        $_SESSION['carrito'] = [];
    }

    if (isset($_SESSION['carrito'][$juego_id])) {
        $_SESSION['carrito'][$juego_id]['cantidad']++;
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
        }
    }

    // Redirigir de nuevo a la página anterior
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}
