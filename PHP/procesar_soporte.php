<?php
$conn = new mysqli("localhost", "root", "", "tienda_videojuegos");
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $motivo = trim($_POST['motivo'] ?? '');
    $mensaje_usuario = trim($_POST['mensaje'] ?? '');

    if ($nombre && $email && $motivo && $mensaje_usuario) {
        $stmt = $conn->prepare("INSERT INTO soporte (nombre, email, motivo, mensaje) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nombre, $email, $motivo, $mensaje_usuario);

        if ($stmt->execute()) {
            $mensaje = "✅ Gracias por contactarnos, $nombre. Te responderemos pronto al correo: $email.";
        } else {
            $mensaje = "❌ Error al guardar el mensaje: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $mensaje = "❌ Todos los campos son obligatorios.";
    }

    $conn->close();
    header("Location: ../HTML/soporte.php?mensaje=" . urlencode($mensaje));
    exit;
} else {
    header("Location: ../HTML/soporte.php?mensaje=" . urlencode("❌ Acceso no válido."));
    exit;
}
