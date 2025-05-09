<?php
session_start();
include 'header.php';
include 'encabezado_nav.php';
?>

<div class="container">
    <h2>Tu Carrito de Compras</h2>

    <div id="carrito-contenido">
        <?php if (!empty($_SESSION['carrito'])): ?>
            <div class="games">
                <?php foreach ($_SESSION['carrito'] as $id => $item): ?>
                    <div class="game" data-id="<?= $id ?>">
                        <img src="<?= $item['imagen'] ?>" alt="<?= $item['nombre'] ?>">
                        <h2><?= $item['nombre'] ?></h2>
                        <p><strong>Precio:</strong> $<?= number_format($item['precio'], 2) ?></p>
                        <p><strong>Cantidad:</strong> <?= $item['cantidad'] ?></p>
                        <a href="#" class="remove-item add-to-cart" data-id="<?= $id ?>">❌ Eliminar</a>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="cart-actions">
                <p><strong>Total: $<span id="total-carrito"><?= number_format(calcularTotal(), 2) ?></span></strong></p>
                <a href="#" id="vaciar-carrito" class="add-to-cart">Vaciar Carrito</a>
                <a href="pagar.php" class="add-to-cart">Pagar</a>
            </div>
        <?php else: ?>
            <p>No hay juegos en tu carrito.</p>
        <?php endif; ?>
    </div>
</div>

<?php
// Función para calcular el total del carrito
function calcularTotal() {
    return array_reduce($_SESSION['carrito'] ?? [], fn($total, $item) => $total + ($item['precio'] * $item['cantidad']), 0);
}

include 'footer.php';
?>
