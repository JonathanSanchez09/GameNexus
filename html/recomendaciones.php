<?php
session_start();
include 'header.php';
include 'encabezado_nav.php';
include('./php/conexion.php');

// Asegúrate de que la conexión a la base de datos use UTF-8
// Agrega esta línea si no lo tienes en tu archivo de conexión:
// $conn->set_charset("utf8mb4");

if (!isset($_GET['juego_id'])) {
    echo "<div class='container'><p class='mensaje error'>ID del juego no proporcionado.</p></div>";
    include 'footer.php';
    exit();
}

$juego_id = (int)$_GET['juego_id'];

// Obtener datos del juego, incluyendo el campo 'video_url'
$stmt = $conn->prepare("SELECT * FROM juegos WHERE id = ?");
$stmt->bind_param("i", $juego_id);
$stmt->execute();
$resultado_juego = $stmt->get_result();

if ($resultado_juego->num_rows === 0) {
    echo "<div class='container'><p class='mensaje error'>Juego no encontrado.</p></div>";
    include 'footer.php';
    exit();
}

$juego = $resultado_juego->fetch_assoc();

// Obtener reseñas del juego
$stmt_resenas = $conn->prepare("
    SELECT r.comentario, r.calificacion, u.email 
    FROM resenas r 
    JOIN usuarios u ON r.usuario_id = u.id 
    WHERE r.juego_id = ?
");
$stmt_resenas->bind_param("i", $juego_id);
$stmt_resenas->execute();
$resenas = $stmt_resenas->get_result();

// Obtener juegos recomendados de la misma categoría
$stmt_rec = $conn->prepare("SELECT * FROM juegos WHERE categoria = ? AND id != ? LIMIT 4");
$stmt_rec->bind_param("si", $juego['categoria'], $juego_id);
$stmt_rec->execute();
$recomendaciones = $stmt_rec->get_result();
?>

<div class="container">
    <?php if (isset($_GET['mensaje'])): ?>
        <div class="alerta-mensaje"><?= htmlspecialchars($_GET['mensaje']) ?></div>
    <?php endif; ?>

    <div class="game-details-container">
        <div class="game-info-left">
            <img src="<?php echo htmlspecialchars($juego['imagen_url']); ?>" alt="Imagen del juego">
        </div>
        <div class="game-info-right">
            <h1><?php echo htmlspecialchars($juego['nombre']); ?></h1>
            <p><strong>Descripción:</strong> <?php echo htmlspecialchars($juego['descripcion']); ?></p>
            <p><strong>Categoría:</strong> <?php echo htmlspecialchars($juego['categoria']); ?></p>
            <p><strong>Precio:</strong> $<?php echo number_format($juego['precio'], 2); ?></p>
            
            <div class="add-to-cart-container">
                <?php if (isset($_SESSION['usuario_id'])): ?>
                    <?php 
                    $juego_id_actual = $juego['id'];
                    $enCarrito = isset($_SESSION['carrito'][$juego_id_actual]); 
                    ?>
                    <?php if ($enCarrito): ?>
                        <p><h2>🛒 ¡Ya está en tu carrito!</h2></p> 
                    <?php else: ?>
                        <a href="./php/agregar_al_carrito.php?juego_id=<?php echo $juego['id']; ?>" class="add-to-cart">
                            🛒 Añadir al Carrito
                        </a>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="login.html" class="add-to-cart">
                        Inicia sesión para añadir al carrito
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if (!empty($juego['video_url'])): ?>
        <div class="video-trailer">
            <h3 class="section-title">Tráiler del Juego</h3>
            <div class="video-container">
                <iframe src="<?php echo htmlspecialchars($juego['video_url']); ?>" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            </div>
        </div>
    <?php endif; ?>

    <h2 style="margin-top: 40px;">Reseñas de jugadores</h2>
    <div class="reviews">
        <?php if ($resenas->num_rows > 0): ?>
            <?php while ($resena = $resenas->fetch_assoc()): ?>
                <div class="review">
                    <p class="rating"><?php echo str_repeat('⭐', $resena['calificacion']) . str_repeat('☆', 5 - $resena['calificacion']); ?></p>
                    <p><strong><?php echo htmlspecialchars($resena['email']); ?>:</strong> <?php echo htmlspecialchars($resena['comentario']); ?></p>
                    <hr style="border: 0; border-top: 1px solid #00ffcc;">
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="mensaje">Este juego aún no tiene reseñas.</p>
        <?php endif; ?>
    </div>
    
    <h2 style="margin-top: 40px;">🎮 Juegos Recomendados en la misma categoría</h2>
    <div class="games">
        <?php if ($recomendaciones->num_rows > 0): ?>
            <?php while ($juego_rec = $recomendaciones->fetch_assoc()): ?>
                <div class="game">
                    <a href="recomendaciones.php?juego_id=<?php echo $juego_rec['id']; ?>">
                        <img src="<?php echo htmlspecialchars($juego_rec['imagen_url']); ?>" alt="<?php echo htmlspecialchars($juego_rec['nombre']); ?>">
                    </a>
                    <h3><?php echo htmlspecialchars($juego_rec['nombre']); ?></h3>
                    <p><strong>Precio:</strong> $<?php echo number_format($juego_rec['precio'], 2); ?></p>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="mensaje">No hay juegos recomendados en esta categoría.</p>
        <?php endif; ?>
    </div>
</div>

<?php 
$stmt->close();
$stmt_resenas->close();
$stmt_rec->close();
$conn->close();
include 'footer.php'; 
?>