<?php
include 'header.php';
include 'encabezado_nav.php';
include '../PHP/conexion.php';

$sql = "SELECT juegos.*, 
    (SELECT AVG(calificacion) FROM resenas WHERE juego_id = juegos.id) AS calificacion_promedio, 
    (SELECT COUNT(*) FROM resenas WHERE juego_id = juegos.id) AS cantidad_reseñas 
    FROM juegos";

$resultado = $conn->query($sql);
?>

<div class="container">
    <h2>Juegos Disponibles</h2>
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
