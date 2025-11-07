<?php
// Incluye los archivos de cabecera y navegación para mantener la estructura de la página
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'header.php';
include 'encabezado_nav.php';
?>

<div class="container">
    <div class="chat-container">
        <h3>Bot de Preguntas de GameNexus</h3>
        <p>¡Hola! Pregúntame sobre cualquier videojuego de GameNexus. Por ahora solo me haré eco de lo que escribas.</p>
        <div id="chat-box"></div>
        <div class="chat-input-container">
            <input type="text" id="chat-input" placeholder="Escribe tu pregunta aquí...">
            <button id="send-button-chat">Enviar</button>
        </div>
    </div>
</div>

<!-- Carga el archivo de JavaScript como un módulo para que funcione con Firebase -->
<script type="module" src="../js/chat.js"></script>

<?php include 'footer.php'; ?>
