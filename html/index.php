<?php
session_start();
include 'header.php';
include 'encabezado_nav.php';
include('./php/conexion.php');

// 💡 Nuevo: Muestra de mensajes del carrito guardados en la sesión
if (isset($_SESSION['mensaje'])) {
    $mensaje = htmlspecialchars($_SESSION['mensaje']);
    $clase_mensaje = strpos($mensaje, '✅') !== false ? 'exito' : 'error';
    
    echo "<div class='alerta-{$clase_mensaje}'>{$mensaje}</div>";
    
    // Limpiar el mensaje de la sesión para que no se muestre de nuevo
    unset($_SESSION['mensaje']);
}

// Lógica de paginación
$juegos_por_pagina = 12; 
$pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($pagina_actual - 1) * $juegos_por_pagina;

// Contar el número total de juegos
$sql_total = "SELECT COUNT(*) AS total FROM juegos";
$resultado_total = $conn->query($sql_total);
$fila_total = $resultado_total->fetch_assoc();
$total_juegos = $fila_total['total'];
$total_paginas = ceil($total_juegos / $juegos_por_pagina);

// 💡 Consulta optimizada para el catálogo usando LEFT JOIN
$sql = "
    SELECT 
        juegos.*, 
        AVG(resenas.calificacion) AS calificacion_promedio,
        COUNT(resenas.calificacion) AS cantidad_reseñas 
    FROM juegos
    LEFT JOIN resenas ON juegos.id = resenas.juego_id
    GROUP BY juegos.id
    LIMIT ? OFFSET ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $juegos_por_pagina, $offset);
$stmt->execute();
$resultado = $stmt->get_result();
?>

<div class="container">

    <?php 
    if (isset($_SESSION['usuario_id'])):
        $usuario_id = $_SESSION['usuario_id'];
        
        // 💡 Consulta optimizada para recomendaciones usando LEFT JOIN
        $sql_recomendaciones = "
            SELECT 
                j.id, j.nombre, j.descripcion, j.categoria, j.precio, j.imagen_url,
                AVG(resenas.calificacion) AS calificacion_promedio,
                COUNT(resenas.calificacion) AS cantidad_reseñas
            FROM recomendaciones r
            JOIN juegos j ON r.id_juego = j.id
            LEFT JOIN resenas ON j.id = resenas.juego_id
            WHERE r.id_usuario = ?
            GROUP BY j.id
        ";
        
        $stmt_rec = $conn->prepare($sql_recomendaciones);
        $stmt_rec->bind_param("i", $usuario_id);
        $stmt_rec->execute();
        $resultado_recomendaciones = $stmt_rec->get_result();

        if ($resultado_recomendaciones->num_rows > 0): ?>
            <h2 class="section-title">✨ Juegos Recomendados para ti</h2>
            <div class="games">
                <?php while ($fila_rec = $resultado_recomendaciones->fetch_assoc()): ?>
                    <div class="game">
                        <a href="recomendaciones.php?juego_id=<?php echo $fila_rec['id']; ?>">
                            <img src="<?php echo htmlspecialchars($fila_rec['imagen_url']); ?>" alt="<?php echo htmlspecialchars($fila_rec['nombre']); ?>">
                        </a>
                        <h2><?php echo htmlspecialchars($fila_rec['nombre']); ?></h2>
                        <p><strong>Categoría:</strong> <?php echo htmlspecialchars($fila_rec['categoria']); ?></p>
                        <p><strong>Precio:</strong> $<?php echo number_format($fila_rec['precio'], 2); ?></p>
                        <div class="reviews">
                            <?php
                            $calificacion_promedio = $fila_rec['calificacion_promedio'];
                            $cantidad_reseñas = $fila_rec['cantidad_reseñas'];
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
                        $juego_id_rec = $fila_rec['id'];
                        $enCarrito = isset($_SESSION['carrito'][$juego_id_rec]); 
                        ?>
                        <?php if ($enCarrito): ?>
                            <p> <h2>🛒 ¡Ya está en tu carrito!</h2></p> 
                        <?php else: ?>
                            <a href="./php/agregar_al_carrito.php?juego_id=<?php echo $fila_rec['id']; ?>" class="add-to-cart">
                                🛒 Agregar al carrito
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            </div>
            <hr>
        <?php else: ?>
            <p></p>
            <hr>
        <?php endif; ?>
        <?php $stmt_rec->close(); // 💡 Cierre de la declaración preparada de recomendaciones ?>
    <?php endif; ?>

    <h2>Catálogo de juegos</h2>
    <?php if ($resultado->num_rows > 0): ?>
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
                            <p> <h2>🛒 ¡Ya está en tu carrito!</h2></p> 
                        <?php else: ?>
                            <a href="./php/agregar_al_carrito.php?juego_id=<?php echo $fila['id']; ?>" class="add-to-cart">
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
                    <a href="?pagina=<?= $pagina_actual - 1 ?>" class="page-link">&laquo; Anterior</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                    <?php if ($i == $pagina_actual): ?>
                        <span class="page-link active"><?= $i ?></span>
                    <?php else: ?>
                        <a href="?pagina=<?= $i ?>" class="page-link"><?= $i ?></a>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ($pagina_actual < $total_paginas): ?>
                    <a href="?pagina=<?= $pagina_actual + 1 ?>" class="page-link">Siguiente &raquo;</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <p>No hay juegos disponibles en este momento.</p>
    <?php endif; ?>
</div>

<?php 
// Asegúrate de cerrar la conexión a la base de datos al final del script
$stmt->close();
$conn->close();
include 'footer.php'; 
?>