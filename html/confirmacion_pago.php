<?php
session_start();
include 'header.php';
include 'encabezado_nav.php';

if (!isset($_SESSION['orden'])) {
    header("Location: index.php");
    exit();
}

$orden = $_SESSION['orden'];

// Generamos los nuevos datos para la confirmación
$numero_factura = 'FAC-' . date('Ymd') . '-' . rand(1000, 9999);
$fecha_factura = date('d/m/Y');
$subtotal = $orden['total'] / 1.16;
$iva = $orden['total'] - $subtotal;
?>

<div class="container">
    <h2>¡Pago procesado con éxito!</h2>
    <p>Gracias, <strong><?php echo htmlspecialchars($orden['nombre']); ?></strong>, por tu compra. Aquí tienes los detalles de tu pedido.</p>

    <h3>Detalles del pedido:</h3>
    <ul>
        <li><strong>Número de Factura:</strong> <?php echo htmlspecialchars($numero_factura); ?></li>
        <li><strong>Fecha del Pedido:</strong> <?php echo htmlspecialchars($fecha_factura); ?></li>
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
        
        <hr style="border: 0; border-top: 1px solid #00ffcc; margin: 15px 0;">

        <li><strong>Subtotal:</strong> $<?php echo number_format($subtotal, 2); ?></li>
        <li><strong>IVA (16%):</strong> $<?php echo number_format($iva, 2); ?></li>
        <li><strong>Total pagado:</strong> $<?php echo number_format($orden['total'], 2); ?></li>
        
        <hr style="border: 0; border-top: 1px solid #00ffcc; margin: 15px 0;">

        <li>
            <strong>Juegos comprados:</strong>
            <ul>
                <?php foreach ($orden['juegos_comprados_con_qr'] as $juego): ?>
                    <li style="margin-top: 10px;">
                        <span><?php echo htmlspecialchars($juego['nombre']); ?></span>
                        <br>
                        <strong style="font-size: 0.9em; color: #00ffcc;">Código:</strong> <?php echo htmlspecialchars($juego['codigo_qr']); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </li>
    </ul>

    <div style="margin-top: 30px;">
        <a href="index.php" class="add-to-cart">Volver al inicio</a>
        <a href="../php/generar_factura.php" target="_blank" class="add-to-cart">Descargar Factura en PDF</a>
    </div>
</div>

<?php
include 'footer.php';
?>