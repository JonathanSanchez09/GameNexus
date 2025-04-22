<?php
session_start();

// Verificar si el usuario está logueado
$is_logged_in = isset($_SESSION['usuario_id']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda de Videojuegos</title>
    <link rel="stylesheet" href="../css/styles.css"> <!-- Enlace al archivo CSS -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=VT323&display=swap" rel="stylesheet">
</head>
<body>

    <header class="main-header">
        <div class="contact-info">
            <span>📧 <a href="mailto:contacto@tiendavideojuegos.com">contacto@tiendavideojuegos.com</a></span>
            <span>📞 <a href="tel:+123456789">+52 33 1846 1351</a></span>
        </div>
        <div class="social-media">
            <a href="https://facebook.com" target="_blank">
                <img src="https://upload.wikimedia.org/wikipedia/commons/5/51/Facebook_f_logo_%282019%29.svg" alt="Facebook" class="social-icon">
            </a>
            <a href="https://twitter.com" target="_blank">
                <img src="https://cdn-icons-png.flaticon.com/512/5968/5968958.png" alt="X" class="social-icon">
            </a>
            <a href="https://instagram.com" target="_blank">
                <img src="https://upload.wikimedia.org/wikipedia/commons/a/a5/Instagram_icon.png" alt="Instagram" class="social-icon">
            </a>
            <a href="https://www.tiktok.com" target="_blank">
                <img src="https://cdn-icons-png.flaticon.com/512/3046/3046122.png" alt="TikTok" class="social-icon">
            </a>
        </div>
    </header>

    <nav class="main-nav-container">
        <div class="main-nav">
            <a href="#">Inicio</a>
            <div class="dropdown">
                <button class="dropbtn">Juegos</button>
                <div class="dropdown-content">
                    <a href="#">Acción</a>
                    <a href="#">Aventura</a>
                    <a href="#">Estrategia</a>
                    <a href="#">Deportes</a>
                </div>
            </div>
            <a href="#">Ofertas</a>
            <a href="#">Reseñas</a>
            <a href="#">Contacto</a>
        </div>
        <div class="login-link">
            <?php if ($is_logged_in): ?>
                <!-- Si el usuario está logueado, muestra un mensaje y un enlace para cerrar sesión -->
                <span>Bienvenido, <?php echo $_SESSION['email']; ?>!</span> | <a href="../PHP/logout.php">Cerrar sesión</a>
            <?php else: ?>
                <!-- Si el usuario no está logueado, muestra el enlace para iniciar sesión -->
                <a href="login.html">Iniciar sesión</a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="container">
        <h2>Juegos Disponibles</h2>

        <div class="games">
            <div class="game">
                <img src="https://via.placeholder.com/300x200" alt="Juego 1">
                <h2>Juego 1</h2>
                <p>Una breve descripción del juego 1.</p>
                <div class="reviews">
                    <p>⭐⭐⭐⭐⭐ (4.5/5) - Reseña general: ¡Increíble!</p>
                </div>
            </div>
            <div class="game">
                <img src="https://via.placeholder.com/300x200" alt="Juego 2">
                <h2>Juego 2</h2>
                <p>Una breve descripción del juego 2.</p>
                <div class="reviews">
                    <p>⭐⭐⭐⭐ (4/5) - Reseña general: Muy bueno, pero con algunos fallos.</p>
                </div>
            </div>
            <div class="game">
                <img src="https://via.placeholder.com/300x200" alt="Juego 3">
                <h2>Juego 3</h2>
                <p>Una breve descripción del juego 3.</p>
                <div class="reviews">
                    <p>⭐⭐⭐⭐⭐ (5/5) - Reseña general: Perfecto, totalmente recomendado.</p>
                </div>
            </div>
        </div>

        <div class="review-form">
            <h3>Deja tu reseña</h3>
            <form id="review-form">
                <input type="text" id="name" placeholder="Tu nombre" required>
                <textarea id="review" placeholder="Escribe tu reseña aquí..." required></textarea>
                <button type="submit">Enviar Reseña</button>
            </form>
        </div>
    </div>

    <footer>
        <p>&copy; 2025 Tienda de Videojuegos. Todos los derechos reservados.</p>
    </footer>

</body>
</html>
