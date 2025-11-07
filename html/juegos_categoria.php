<?php
session_start(); // Asegúrate de iniciar la sesión al principio
include 'header.php';
include 'encabezado_nav.php';
include('./php/conexion.php');

// Lógica de paginación
$juegos_por_pagina = 12; 
$pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($pagina_actual - 1) * $juegos_por_pagina;

// Verificar si se recibió una categoría
if (!isset($_GET['categoria']) || empty($_GET['categoria'])) {
    echo "<div class='container'><p class='mensaje error'>❌ Categoría no especificada.</p></div>";
    include 'footer.php';
    exit();
}

$categoria = htmlspecialchars($_GET['categoria']);

// Contar el número total de juegos para la categoría
$sql_total = "SELECT COUNT(*) AS total FROM juegos WHERE categoria = ?";
$stmt_total = $conn->prepare($sql_total);
$stmt_total->bind_param("s", $categoria);
$stmt_total->execute();
$resultado_total = $stmt_total->get_result();
$fila_total = $resultado_total->fetch_assoc();
$total_juegos = $fila_total['total'];
$total_paginas = ceil($total_juegos / $juegos_por_pagina);
$stmt_total->close();

// 💡 Consulta optimizada para obtener los juegos de la página actual
$sql = "
    SELECT 
        juegos.*, 
        AVG(resenas.calificacion) AS calificacion_promedio,
        COUNT(resenas.calificacion) AS cantidad_reseñas 
    FROM juegos
    LEFT JOIN resenas ON juegos.id = resenas.juego_id
    WHERE juegos.categoria = ?
    GROUP BY juegos.id
    LIMIT ? OFFSET ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sii", $categoria, $juegos_por_pagina, $offset);
$stmt->execute();
$resultado = $stmt->get_result();
?>

<div class="container">
    <h2>Juegos de la categoría: <?php echo $categoria; ?></h2>
    
    <?php if ($resultado->num_rows > 0): ?>
        <div class="games">
            <?php while ($juego = $resultado->fetch_assoc()): ?>
                <div class="game">
                    <a href="recomendaciones.php?juego_id=<?php echo $juego['id']; ?>">
                        <img src="<?php echo htmlspecialchars($juego['imagen_url']); ?>" alt="<?php echo htmlspecialchars($juego['nombre']); ?>">
                    </a>
                    <h2><?php echo htmlspecialchars($juego['nombre']); ?></h2>
                    <p><strong>Precio:</strong> $<?php echo number_format($juego['precio'], 2); ?></p>
                    
                    <div class="reviews">
                        <?php
                        $calificacion_promedio = $juego['calificacion_promedio'];
                        $cantidad_reseñas = $juego['cantidad_reseñas'];
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
                    $juego_id = $juego['id'];
                    $enCarrito = isset($_SESSION['carrito'][$juego_id]); 
                    ?>
                    
                    <?php if (isset($_SESSION['usuario_id'])): ?>
                        <?php if ($enCarrito): ?>
                            <p><h2>🛒 ¡Ya está en tu carrito!</h2></p>
                        <?php else: ?>
                            <a href="./php/agregar_al_carrito.php?juego_id=<?php echo $juego['id']; ?>" class="add-to-cart">
                                🛒 Agregar al carrito
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
                    <a href="?categoria=<?= urlencode($categoria) ?>&pagina=<?= $pagina_actual - 1 ?>" class="page-link">&laquo; Anterior</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                    <?php if ($i == $pagina_actual): ?>
                        <span class="page-link active"><?= $i ?></span>
                    <?php else: ?>
                        <a href="?categoria=<?= urlencode($categoria) ?>&pagina=<?= $i ?>" class="page-link"><?= $i ?></a>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ($pagina_actual < $total_paginas): ?>
                    <a href="?categoria=<?= urlencode($categoria) ?>&pagina=<?= $pagina_actual + 1 ?>" class="page-link">Siguiente &raquo;</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <p class="mensaje">No hay juegos disponibles en esta categoría.</p>
    <?php endif; ?>

</div>

<?php 
$stmt->close();
$conn->close();
include 'footer.php'; 
?>