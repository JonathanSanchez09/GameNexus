<?php
session_start();
include 'header.php';
include 'encabezado_nav.php';

if (!isset($_SESSION['orden'])) {
    header("Location: index.php");
    exit();
}

$orden = $_SESSION['orden'];
?>

<div class="container">
    <h2>¡Pago procesado con éxito!</h2>
    <p>Gracias, <strong><?php echo htmlspecialchars($orden['nombre']); ?></strong>, por tu compra.</p>

    <h3>Detalles del pedido:</h3>
    <ul>
        <li><strong>Email:</strong> <?php echo htmlspecialchars($orden['email']); ?></li>
        <li><strong>Teléfono:</strong> <?php echo htmlspecialchars($orden['telefono']); ?></li>
        <li><strong>Dirección:</strong> 
            <?php 
            echo htmlspecialchars($orden['direccion']['calle']) . ", " .
                 htmlspecialchars($orden['direccion']['ciudad']) . ", " .
                 htmlspecialchars($orden['direccion']['estado']) . ", " .
                 htmlspecialchars($orden['direccion']['codigo_postal']) . ", " .
                 htmlspecialchars($orden['direccion']['pais']); 
            ?>
        </li>
        <li><strong>Método de pago:</strong> <?php echo htmlspecialchars(ucfirst($orden['metodo_pago'])); ?></li>
        <li><strong>Total pagado:</strong> $<?php echo number_format($orden['total'], 2); ?></li>
    </ul>

    <a href="index.php" class="add-to-cart">Volver al inicio</a>
</div>

<?php
// Limpiar orden para evitar repetir la confirmación si refrescan
unset($_SESSION['orden']);
include 'footer.php';
?>
