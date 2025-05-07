<!-- header.php -->
<?php
session_start();
$is_logged_in = isset($_SESSION['usuario_id']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda de Videojuegos</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=VT323&display=swap" rel="stylesheet">
    <script src="../js/validacion_resena.js" defer></script>
    <script src="../js/validacion_juego.js" defer></script>
    <script src="../js/validacion_soporte.js" defer></script>
</head>
<body>

<header class="main-header">
    <div class="contact-info">
        <span>📧 <a href="mailto:contacto@tiendavideojuegos.com">contacto@tiendavideojuegos.com</a></span>
        <span>📞 <a href="tel:+123456789">+52 33 1846 1351</a></span>
    </div>
    <div class="social-media">
        <a href="https://facebook.com" target="_blank"><img src="https://upload.wikimedia.org/wikipedia/commons/5/51/Facebook_f_logo_%282019%29.svg" alt="Facebook" class="social-icon"></a>
        <a href="https://twitter.com" target="_blank"><img src="https://cdn-icons-png.flaticon.com/512/5968/5968958.png" alt="X" class="social-icon"></a>
        <a href="https://instagram.com" target="_blank"><img src="https://upload.wikimedia.org/wikipedia/commons/a/a5/Instagram_icon.png" alt="Instagram" class="social-icon"></a>
        <a href="https://www.tiktok.com" target="_blank"><img src="https://cdn-icons-png.flaticon.com/512/3046/3046122.png" alt="TikTok" class="social-icon"></a>
    </div>
</header>

<nav class="main-nav-container">
    <div class="main-nav">
        <a href="../html/index.php">Inicio</a>
        <div class="dropdown">
            <button class="dropbtn">Juegos</button>
            <div class="dropdown-content">
                <a href="#">Acción</a>
                <a href="#">Aventura</a>
                <a href="#">Estrategia</a>
                <a href="#">Deportes</a>
            </div>
        </div>
        <div class="dropdown">
            <button class="dropbtn">Reseñas</button>
            <div class="dropdown-content">
                <a href="../html/registrar_juego.php">Agregar Juego</a>
                <a href="../html/registrar_resena.php">Agregar Reseña</a>
            </div>
        </div>
        <a href="../HTML/soporte.php">Contacto</a>
    </div>
    <div class="login-link">
        <?php if ($is_logged_in): ?>
            <span>Bienvenido, <?php echo $_SESSION['email']; ?>!</span> | <a href="../PHP/logout.php">Cerrar sesión</a>
        <?php else: ?>
            <a href="login.html">Iniciar sesión</a>
        <?php endif; ?>
    </div>
</nav>
