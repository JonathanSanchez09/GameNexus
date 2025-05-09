<?php
include 'header.php';
include 'encabezado_nav.php';
include '../PHP/conexion.php';

// Verificar si se recibió una categoría
if (!isset($_GET['categoria'])) {
    echo "<div class='container'><p class='mensaje error'>Categoría no especificada.</p></div>";
    include 'footer.php';
    exit();
}

$categoria = htmlspecialchars($_GET['categoria']);

// Consultar los juegos de la categoría seleccionada
$stmt = $conn->prepare("SELECT * FROM juegos WHERE categoria = ?");
$stmt->bind_param("s", $categoria);
$stmt->execute();
$resultado = $stmt->get_result();
?>

<div class="container">
    <h2>Juegos de la categoría: <?php echo $categoria; ?></h2>
    
    <?php if ($resultado->num_rows > 0): ?>
        <div class="games">
            <?php while ($juego = $resultado->fetch_assoc()): ?>
                <div class="game">
                    <img src="<?php echo htmlspecialchars($juego['imagen_url']); ?>" alt="Imagen del juego">
                    <h2><?php echo htmlspecialchars($juego['nombre']); ?></h2>
                    <p><?php echo htmlspecialchars($juego['descripcion']); ?></p>
                    <p><strong>Precio:</strong> $<?php echo number_format($juego['precio'], 2); ?></p>

                    <?php
                    // Verificar si el juego ya está en el carrito
                    $juego_id = $juego['id'];
                    $enCarrito = isset($_SESSION['carrito'][$juego_id]);  // Verificar si el juego está en el carrito
                    ?>

                    <?php if ($enCarrito): ?>
                        <p> <h2>🛒 ¡Ya está en tu carrito!</h2></p>  <!-- Mostrar que ya está en el carrito -->
                    <?php else: ?>
                        <a href="../PHP/agregar_al_carrito.php?juego_id=<?php echo $juego['id']; ?>" class="add-to-cart">
                            🛒 Agregar al carrito
                        </a>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p class="mensaje">No hay juegos disponibles en esta categoría.</p>
    <?php endif; ?>

</div>

<?php include 'footer.php'; ?>
