<?php
// Eliminar la llamada a session_start() aquí
$is_logged_in = isset($_SESSION['usuario_id']);
?>

<header class="main-header">
    <div class="contact-info">
        <span>📧 <a href="mailto:contacto@tiendavideojuegos.com">contacto@tiendavideojuegos.com</a></span>
        <span>📞 <a href="tel:+123456789">+52 33 1846 1351</a></span>
    </div>
    <div class="social-media">
        <a href="#"><img src="https://upload.wikimedia.org/wikipedia/commons/5/51/Facebook_f_logo_%282019%29.svg" alt="Facebook" class="social-icon"></a>
        <a href="#"><img src="https://cdn-icons-png.flaticon.com/512/5968/5968958.png" alt="X" class="social-icon"></a>
        <a href="#"><img src="https://upload.wikimedia.org/wikipedia/commons/a/a5/Instagram_icon.png" alt="Instagram" class="social-icon"></a>
        <a href="#"><img src="https://cdn-icons-png.flaticon.com/512/3046/3046122.png" alt="TikTok" class="social-icon"></a>
    </div>
</header>

<nav class="main-nav-container">
    <div class="main-nav">
        <a href="index.php">Inicio</a>
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
            <button class="dropbtn">reseñas</button>
            <div class="dropdown-content">
                <a href="./registrar_juego.php">Agregar Juego</a>
                <a href="./registrar_resena.php">Agregar Reseña</a>
            </div>
        </div>
        <a href="#">Contacto</a>
    </div>
    <div class="login-link">
            <?php if ($is_logged_in): ?>
                <span>Bienvenido, <?php echo $_SESSION['email']; ?>!</span> | <a href="../PHP/logout.php">Cerrar sesión</a>
            <?php else: ?>
                <a href="login.html">Iniciar sesión</a>
            <?php endif; ?>
        </div>
</nav>
