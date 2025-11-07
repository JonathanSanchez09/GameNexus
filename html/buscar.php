<?php
session_start();
include 'header.php';
include 'encabezado_nav.php';
include('./php/conexion.php');

$termino_busqueda = $_GET['q'] ?? '';
$total_juegos = 0;
$total_paginas = 0;
$resultado = null;

if (!empty($termino_busqueda)) {
    // 💡 Consulta optimizada usando LEFT JOIN para obtener calificaciones
    $sql_total = "SELECT COUNT(*) AS total FROM juegos WHERE nombre LIKE ? OR categoria LIKE ?";
    $stmt_total = $conn->prepare($sql_total);
    $termino_busqueda_like = "%" . $termino_busqueda . "%";
    $stmt_total->bind_param("ss", $termino_busqueda_like, $termino_busqueda_like);
    $stmt_total->execute();
    $fila_total = $stmt_total->get_result()->fetch_assoc();
    $total_juegos = $fila_total['total'];
    $total_paginas = ceil($total_juegos / 12);
    $stmt_total->close();

    $juegos_por_pagina = 12;
    $pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
    $offset = ($pagina_actual - 1) * $juegos_por_pagina;

    $sql = "
        SELECT 
            juegos.*,
            AVG(resenas.calificacion) AS calificacion_promedio,
            COUNT(resenas.calificacion) AS cantidad_reseñas
        FROM juegos
        LEFT JOIN resenas ON juegos.id = resenas.juego_id
        WHERE juegos.nombre LIKE ? OR juegos.categoria LIKE ?
        GROUP BY juegos.id
        LIMIT ? OFFSET ?
    ";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssii", $termino_busqueda_like, $termino_busqueda_like, $juegos_por_pagina, $offset);
    $stmt->execute();
    $resultado = $stmt->get_result();
}
?>

<div class="container">
    <?php 
    // 💡 Muestra el mensaje si existe en la URL (alerta del carrito)
    if (isset($_GET['mensaje'])): ?>
        <div class="alerta-mensaje"><?= htmlspecialchars($_GET['mensaje']) ?></div>
    <?php endif; ?>

    <?php if (!empty($termino_busqueda)): ?>
        <h2>Resultados de la búsqueda para "<?= htmlspecialchars($termino_busqueda) ?>"</h2>
        <p>Se encontraron <?= $total_juegos ?> resultados.</p>

        <?php if ($resultado && $resultado->num_rows > 0): ?>
            <div class="games">
                <?php while ($fila = $resultado->fetch_assoc()): ?>
                    <div class="game">
                        <a href="recomendaciones.php?juego_id=<?php echo $fila['id']; ?>">
                            <img src="<?php echo htmlspecialchars($fila['imagen_url']); ?>" alt="<?php echo htmlspecialchars($fila['nombre']); ?>">
                        </a>
                        <h2><?php echo htmlspecialchars($fila['nombre']); ?></h2>
                        <p><strong>Categoría:</strong> <?php echo htmlspecialchars($fila['categoria']); ?></p>
                        <p><strong>Precio:</strong> $<?php echo number_format($fila['precio'], 2); ?></p>
                        
                        <div class="reviews">
                            <?php
                            $calificacion_promedio = $fila['calificacion_promedio'];
                            $cantidad_reseñas = $fila['cantidad_reseñas'];
                            $calificacion_promedio_mostrada = $calificacion_promedio !== null ? number_format($calificacion_promedio, 1) : '0.0';
                            ?>
                            <p>Calificación promedio: <?php echo $calificacion_promedio_mostrada; ?> (de <?php echo $cantidad_reseñas; ?> reseñas)</p>
                            <div class="rating">
                                <?php
                                $calificacion = $calificacion_promedio ?? 0;
                                for ($i = 1; $i <= 5; $i++) {
                                    echo ($i <= round($calificacion)) ? '⭐' : '☆';
                                }
                                ?>
                            </div>
                        </div>
                        
                        <?php 
                        $juego_id = $fila['id'];
                        $enCarrito = isset($_SESSION['carrito'][$juego_id]); 
                        ?>
                        
                        <?php if (isset($_SESSION['usuario_id'])): ?>
                            <?php if ($enCarrito): ?>
                                <p><h2>🛒 ¡Ya está en tu carrito!</h2></p>
                            <?php else: ?>
                                <a href="./php/agregar_al_carrito.php?juego_id=<?php echo $fila['id']; ?>" class="add-to-cart">
                                    🛒 Añadir al Carrito
                                </a>
                            <?php endif; ?>
                        <?php else: ?>
                            <a href="login.html" class="add-to-cart">
                                Inicia sesión para añadir al carrito
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            </div>

            <?php if ($total_paginas > 1): ?>
                <div class="pagination">
                    <?php if ($pagina_actual > 1): ?>
                        <a href="?q=<?= urlencode($termino_busqueda) ?>&pagina=<?= $pagina_actual - 1 ?>" class="page-link">&laquo; Anterior</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                        <a href="?q=<?= urlencode($termino_busqueda) ?>&pagina=<?= $i ?>" class="page-link <?= ($i == $pagina_actual) ? 'active' : '' ?>"><?= $i ?></a>
                    <?php endfor; ?>

                    <?php if ($pagina_actual < $total_paginas): ?>
                        <a href="?q=<?= urlencode($termino_busqueda) ?>&pagina=<?= $pagina_actual + 1 ?>" class="page-link">Siguiente &raquo;</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            <?php $stmt->close(); ?>
        <?php else: ?>
            <p>No se encontraron juegos que coincidan con la búsqueda.</p>
        <?php endif; ?>
    <?php else: ?>
        <p>Por favor, ingresa un término de búsqueda.</p>
    <?php endif; ?>
</div>

<?php 
$conn->close();
include 'footer.php'; 
?>