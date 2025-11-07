<?php
session_start();
include 'header.php';
include 'encabezado_nav.php';

// Mostrar mensaje si viene por GET
$mensaje = $_GET['mensaje'] ?? '';

// Recuperar datos de la sesión si existen
$form_data = $_SESSION['form_data'] ?? [];
?>

<div class="container">
    <h2>Soporte al Cliente</h2>

    <?php if (!empty($mensaje)): ?>
        <div class="alerta-mensaje"><?= htmlspecialchars($mensaje) ?></div>

        <?php if (str_starts_with($mensaje, '✅')): ?>
            <script>
                setTimeout(() => {
                    // Limpiar datos de sesión al redirigir por éxito
                    <?php unset($_SESSION['form_data']); ?> 
                    window.location.href = 'index.php';
                }, 3000);
            </script>
        <?php endif; ?>
    <?php endif; ?>

    <p>¿Tienes un problema o una duda? Rellena el siguiente formulario y te ayudaremos lo antes posible.</p>

    <form class="review-form" method="POST" action="../php/procesar_soporte.php">
        <input type="text" name="nombre" placeholder="Tu nombre" 
               value="<?= htmlspecialchars($form_data['nombre'] ?? '') ?>" required>
               
        <input type="email" name="email" placeholder="Tu correo electrónico" 
               value="<?= htmlspecialchars($form_data['email'] ?? '') ?>" required>
               
        <select name="motivo" required>
            <option value="">Motivo del contacto</option>
            <?php 
            $motivo_seleccionado = $form_data['motivo'] ?? '';
            // Función simple para verificar si la opción es la que estaba seleccionada
            $is_selected = fn($value) => $motivo_seleccionado === $value ? 'selected' : '';
            ?>
            <option value="problema_compra" <?= $is_selected('problema_compra') ?>>Problemas con una compra</option>
            <option value="error_juego" <?= $is_selected('error_juego') ?>>Error en un juego</option>
            <option value="sugerencia" <?= $is_selected('sugerencia') ?>>Sugerencia</option>
            <option value="otro" <?= $is_selected('otro') ?>>Otro</option>
        </select>
        
        <textarea name="mensaje" placeholder="Escribe aquí tu mensaje..." required><?= htmlspecialchars($form_data['mensaje'] ?? '') ?></textarea>
        
        <button type="submit">Enviar Mensaje</button>
    </form>
</div>

<?php 
// 🧹 Limpiar la variable de sesión una vez que los datos han sido utilizados.
if (isset($_SESSION['form_data'])) {
    unset($_SESSION['form_data']);
}
include 'footer.php'; 
?>