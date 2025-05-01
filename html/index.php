<?php
session_start();

// Verificar si el usuario está logueado
$is_logged_in = isset($_SESSION['usuario_id']);

// Conectar a la base de datos
include '../PHP/conexion.php';

$sql = "SELECT juegos.*, 
    (SELECT AVG(calificacion) FROM resenas WHERE juego_id = juegos.id) AS calificacion_promedio, 
    (SELECT COUNT(*) FROM resenas WHERE juego_id = juegos.id) AS cantidad_reseñas 
    FROM juegos";

$resultado = $conn->query($sql);
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

    <div class="container">
        <h2>Juegos Disponibles</h2>
        <div class="games">
            <?php if ($resultado && $resultado->num_rows > 0): ?>
                <?php while ($fila = $resultado->fetch_assoc()): ?>
                    <div class="game">
                        <img src="<?php echo htmlspecialchars($fila['imagen_url']); ?>" alt="<?php echo htmlspecialchars($fila['nombre']); ?>">
                        <h2><?php echo htmlspecialchars($fila['nombre']); ?></h2>
                        <p><?php echo htmlspecialchars($fila['descripcion']); ?></p>
                        <p><strong>Categoría:</strong> <?php echo htmlspecialchars($fila['categoria']); ?></p>
                        <p><strong>Precio:</strong> $<?php echo number_format($fila['precio'], 2); ?></p>

                        <div class="reviews">
                            <p>Calificación promedio: <?php echo number_format($fila['calificacion_promedio'], 1); ?> (de <?php echo $fila['cantidad_resenas']; ?> reseñas)</p>
                            <div class="rating">
                                <?php
                                // Generar estrellas basadas en la calificación promedio
                                $calificacion_promedio = $fila['calificacion_promedio'];
                                for ($i = 1; $i <= 5; $i++) {
                                    if ($i <= $calificacion_promedio) {
                                        echo '⭐'; // Estrella llena
                                    } else {
                                        echo '☆'; // Estrella vacía
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No hay juegos disponibles por el momento.</p>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        <p>&copy; 2025 Tienda de Videojuegos. Todos los derechos reservados.</p>
    </footer>

</body>
</html>
