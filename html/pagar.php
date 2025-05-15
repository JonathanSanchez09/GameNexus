<?php
session_start();
include 'header.php';
include 'encabezado_nav.php';

// Verificar carrito
if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
    header("Location: carrito.php");
    exit();
}

// Calcular total
$total = 0;
foreach ($_SESSION['carrito'] as $item) {
    $total += $item['precio'] * $item['cantidad'];
}
?>

<div class="container">
    <h2>Procesar Pago</h2>
    <p>Total a pagar: <strong>$<?php echo number_format($total, 2); ?></strong></p>

    <form class="review-form" action="../PHP/procesar_pago.php" method="post" id="formPago">
        <!-- Datos personales -->
        <input type="text" name="nombre" placeholder="Nombre Completo" required>
        <input type="email" name="email" placeholder="Correo Electrónico" required>
        <input type="tel" name="telefono" placeholder="Teléfono" required pattern="[0-9]{7,15}" title="Solo números, mínimo 7 y máximo 15 dígitos">

        <!-- Dirección de envío -->
        <input type="text" name="calle" placeholder="Calle y número" required>
        <input type="text" name="ciudad" placeholder="Ciudad" required>
        <input type="text" name="estado" placeholder="Estado/Provincia" required>
        <input type="text" name="codigo_postal" placeholder="Código Postal" required pattern="[0-9]{4,10}" title="Solo números">
        <input type="text" name="pais" placeholder="País" required>

        <!-- Método de pago -->
        <select name="metodo_pago" id="metodo_pago" required>
            <option value="" disabled selected>Método de Pago</option>
            <option value="tarjeta">Tarjeta de Crédito/Débito</option>
            <option value="paypal">PayPal</option>
        </select>

        <!-- Datos tarjeta, solo si es tarjeta -->
        <div id="datos_tarjeta" style="display:none;">
            <input type="text" name="numero_tarjeta" placeholder="Número de Tarjeta" pattern="\d{13,19}" title="13 a 19 dígitos" >
            <input type="month" name="fecha_expiracion" placeholder="Fecha de Expiración" >
            <input type="text" name="cvv" placeholder="CVV" pattern="\d{3,4}" title="3 o 4 dígitos" >
        </div>

        <button type="submit">Pagar</button>
    </form>
</div>

<?php include 'footer.php'; ?>
