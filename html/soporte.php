<?php
session_start();
include 'header.php';
include 'encabezado_nav.php';

// Mostrar mensaje si viene por GET
$mensaje = $_GET['mensaje'] ?? '';
?>

<div class="container">
    <h2>Soporte al Cliente</h2>

    <?php if (!empty($mensaje)): ?>
        <div class="alerta-mensaje"><?= htmlspecialchars($mensaje) ?></div>

        <?php if (str_starts_with($mensaje, '✅')): ?>
            <script>
                setTimeout(() => {
                    window.location.href = 'index.php';
                }, 3000);
            </script>
        <?php endif; ?>
    <?php endif; ?>

    <p>¿Tienes un problema o una duda? Rellena el siguiente formulario y te ayudaremos lo antes posible.</p>

    <form class="review-form" method="POST" action="../php/procesar_soporte.php">
        <input type="text" name="nombre" placeholder="Tu nombre" required>
        <input type="email" name="email" placeholder="Tu correo electrónico" required>
        <select name="motivo" required>
            <option value="">Motivo del contacto</option>
            <option value="problema_compra">Problemas con una compra</option>
            <option value="error_juego">Error en un juego</option>
            <option value="sugerencia">Sugerencia</option>
            <option value="otro">Otro</option>
        </select>
        <textarea name="mensaje" placeholder="Escribe aquí tu mensaje..." required></textarea>
        <button type="submit">Enviar Mensaje</button>
    </form>
</div>

<?php include 'footer.php'; ?>
