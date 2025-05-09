<?php
session_start();
include 'header.php';
include 'encabezado_nav.php';

// Verificar si hay productos en el carrito
if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
    header("Location: carrito.php");
    exit();
}

// Calcular el total
$total = 0;
foreach ($_SESSION['carrito'] as $item) {
    $total += $item['precio'] * $item['cantidad'];
}
?>

<div class="container">
    <h2>Procesar Pago</h2>
    <p>Total a pagar: <strong>$<?php echo number_format($total, 2); ?></strong></p>
    
    <form action="procesar_pago.php" method="post">
        <label for="nombre">Nombre Completo:</label>
        <input type="text" name="nombre" required>

        <label for="direccion">Dirección:</label>
        <input type="text" name="direccion" required>

        <label for="metodo_pago">Método de Pago:</label>
        <select name="metodo_pago" required>
            <option value="tarjeta">Tarjeta de Crédito/Débito</option>
            <option value="paypal">PayPal</option>
        </select>

        <button type="submit" class="checkout-button">Pagar</button>
    </form>
</div>

<?php include 'footer.php'; ?>
