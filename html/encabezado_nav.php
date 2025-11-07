<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$is_logged_in = isset($_SESSION['usuario_id']);
?>

<header class="main-header">
    <div class="contact-info">
        <span>📧 <a href="mailto:contacto@gamenexus.com">contacto@gamenexus.com</a></span>
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
        <!-- CAMBIO: Ahora el logo redirige al mini-juego -->
        <a href="chat.php" class="logo-link">
            <img src="/img/logo.png" alt="GameNexus Logo" class="header-logo">
        </a>
        <a href="index.php">Inicio</a>
        <div class="dropdown">
            <button class="dropbtn">Juegos</button>
            <div class="dropdown-content">
                <a href="juegos_categoria.php?categoria=Accion">Acción</a>
                <a href="juegos_categoria.php?categoria=Aventura">Aventura</a>
                <a href="juegos_categoria.php?categoria=Estrategia">Estrategia</a>
                <a href="juegos_categoria.php?categoria=Deportes">Deportes</a>
            </div>
        </div>
        <a href="soporte.php">Contacto</a>
        <form class="search-form" action="buscar.php" method="GET">
            <input type="text" name="q" placeholder="Buscar juegos..." required>
            <button type="submit">🔍</button>
        </form>
        
        <?php if ($is_logged_in): ?>
            <a href="./registrar_resena.php">Agregar Reseña</a>
            <a href="./registrar_juego.php">Agregar Juego</a>

            <div class="dropdown">
                <button class="dropbtn user-menu-btn">
                    Bienvenido, <?php echo htmlspecialchars($_SESSION['email']); ?>!
                </button>
                <div class="dropdown-content">
                    <a href="perfil.php">👤 Mi perfil</a>
                    <a href="carrito.php">🛒 Mi carrito</a>
                    <a href="./php/logout.php">🚪 Cerrar sesión</a>
                </div>
            </div>
        <?php else: ?>
            <div class="login-link">
                <a href="login.html">Iniciar sesión</a>
            </div>
        <?php endif; ?>
    </div>
</nav>
