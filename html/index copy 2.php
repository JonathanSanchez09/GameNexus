<?php
include 'header.php';
include 'encabezado_nav.php';
include '../PHP/conexion.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$usuario_id = $_SESSION['usuario_id'] ?? null;
$recomendados = [];

// Obtener juegos recomendados a partir de calificaciones altas en resenas
if ($usuario_id) {
    $sql_recom = "
        SELECT j.*
        FROM resenas r
        INNER JOIN juegos j ON r.juego_id = j.id
        WHERE r.usuario_id = ? AND r.calificacion >= 4
        GROUP BY j.id
        ORDER BY r.calificacion DESC
        LIMIT 5
    ";
    $stmt = $conn->prepare($sql_recom);
    if ($stmt) {
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        $resultado_recom = $stmt->get_result();
        while ($fila = $resultado_recom->fetch_assoc()) {
            $recomendados[] = $fila;
        }
        $stmt->close();
    } else {
        echo "Error al preparar la consulta: " . $conn->error;
    }
}

// Consulta para todos los juegos
$sql = "SELECT juegos.*, 
        (SELECT AVG(calificacion) FROM resenas WHERE juego_id = juegos.id) AS calificacion_promedio, 
        (SELECT COUNT(*) FROM resenas WHERE juego_id = juegos.id) AS cantidad_reseñas 
        FROM juegos";
$resultado = $conn->query($sql);
?>

<!-- Mostrar juegos recomendados -->
<?php if ($usuario_id && count($recomendados) > 0): ?>
    <div class="container">
        <h2>🎯 Juegos Recomendados para Ti</h2>
        <div class="games">
            <?php foreach ($recomendados as $juego): ?>
                <div class="game">
                    <a href="recomendaciones.php?juego_id=<?php echo $juego['id']; ?>">
                        <img src="<?php echo htmlspecialchars($juego['imagen_url']); ?>" alt="<?php echo htmlspecialchars($juego['nombre']); ?>">
                    </a>
                    <h2><?php echo htmlspecialchars($juego['nombre']); ?></h2>
                    <p><?php echo htmlspecialchars($juego['descripcion']); ?></p>
                    <p><strong>Categoría:</strong> <?php echo htmlspecialchars($juego['categoria']); ?></p>
                    <p><strong>Precio:</strong> $<?php echo number_format($juego['precio'], 2); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<!-- Mostrar todos los juegos -->
<div class="container">
    <h2>🎮 Juegos Disponibles</h2>
    <div class="games">
        <?php if ($resultado && $resultado->num_rows > 0): ?>
            <?php while ($fila = $resultado->fetch_assoc()): ?>
                <div class="game">
                    <a href="recomendaciones.php?juego_id=<?php echo $fila['id']; ?>">
                        <img src="<?php echo htmlspecialchars($fila['imagen_url']); ?>" alt="<?php echo htmlspecialchars($fila['nombre']); ?>">
                    </a>
                    <h2><?php echo htmlspecialchars($fila['nombre']); ?></h2>
                    <p><?php echo htmlspecialchars($fila['descripcion']); ?></p>
                    <p><strong>Categoría:</strong> <?php echo htmlspecialchars($fila['categoria']); ?></p>
                    <p><strong>Precio:</strong> $<?php echo number_format($fila['precio'], 2); ?></p>
                    <div class="reviews">
                        <p>Calificación promedio: <?php echo number_format($fila['calificacion_promedio'], 1); ?> (de <?php echo $fila['cantidad_reseñas']; ?> reseñas)</p>
                        <div class="rating">
                            <?php
                            $calificacion_promedio = $fila['calificacion_promedio'];
                            for ($i = 1; $i <= 5; $i++) {
                                echo ($i <= $calificacion_promedio) ? '⭐' : '☆';
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

<?php include 'footer.php'; ?>
