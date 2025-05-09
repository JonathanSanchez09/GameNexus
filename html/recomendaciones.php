<?php
include 'header.php';
include 'encabezado_nav.php';
include '../PHP/conexion.php';

if (!isset($_GET['juego_id'])) {
    echo "<div class='container'><p class='mensaje error'>ID del juego no proporcionado.</p></div>";
    include 'footer.php';
    exit();
}

$juego_id = (int)$_GET['juego_id'];

// Obtener datos del juego
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

// Obtener juegos recomendados
$stmt_rec = $conn->prepare("SELECT * FROM juegos WHERE categoria = ? AND id != ? LIMIT 4");
$stmt_rec->bind_param("si", $juego['categoria'], $juego_id);
$stmt_rec->execute();
$recomendaciones = $stmt_rec->get_result();
?>

<div class="container">
    <div class="game">
        <img src="<?php echo htmlspecialchars($juego['imagen_url']); ?>" alt="Imagen del juego">
        <h2><?php echo htmlspecialchars($juego['nombre']); ?></h2>
        <p><strong>Descripción:</strong> <?php echo htmlspecialchars($juego['descripcion']); ?></p>
        <p><strong>Categoría:</strong> <?php echo htmlspecialchars($juego['categoria']); ?></p>
        <p><strong>Precio:</strong> $<?php echo number_format($juego['precio'], 2); ?></p>
    </div>

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
</div>

<?php include 'footer.php'; ?>
