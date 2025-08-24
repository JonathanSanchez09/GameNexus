<?php
session_start();
include 'conexion.php'; 

// Redirige si la solicitud no es POST o si faltan datos
if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST['nombre'], $_POST['email'], $_POST['motivo'], $_POST['mensaje'])) {
    header("Location: ../soporte.php");
    exit();
}

// 1. Validar y sanear los datos de entrada
$nombre = trim($_POST['nombre']);
$email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
$motivo = trim($_POST['motivo']);
$mensaje_usuario = trim($_POST['mensaje']);
$usuario_id = $_SESSION['usuario_id'] ?? null; // Captura el ID del usuario si está logueado

// Validaciones básicas
if (empty($nombre) || empty($email) || empty($motivo) || empty($mensaje_usuario)) {
    $mensaje = "❌ Todos los campos son obligatorios.";
    $_SESSION['form_data'] = $_POST; // Guarda los datos para rellenar el formulario
    header("Location: ../soporte.php?mensaje=" . urlencode($mensaje));
    exit();
}

// Validación específica para el email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $mensaje = "❌ El correo electrónico no es válido.";
    $_SESSION['form_data'] = $_POST;
    header("Location: ../soporte.php?mensaje=" . urlencode($mensaje));
    exit();
}

// 2. Insertar el ticket en la base de datos
try {
    $stmt = $conn->prepare("INSERT INTO tickets_soporte (nombre, email, motivo, mensaje, usuario_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $nombre, $email, $motivo, $mensaje_usuario, $usuario_id);
    
    if ($stmt->execute()) {
        $mensaje_exito = "✅ ¡Gracias por contactarnos, $nombre! Te responderemos pronto a tu correo.";
        header("Location: ../soporte.php?mensaje=" . urlencode($mensaje_exito));
        
        $stmt->close();
        $conn->close();

    } else {
        $mensaje_error = "❌ Error al guardar el mensaje: " . $stmt->error;
        $_SESSION['form_data'] = $_POST;
        header("Location: ../soporte.php?mensaje=" . urlencode($mensaje_error));
        
        $stmt->close();
        $conn->close();
    }
} catch (Exception $e) {
    // Manejo de errores más robusto para errores de conexión o de la base de datos
    $mensaje_error = "❌ Ocurrió un error inesperado. Por favor, inténtalo de nuevo.";
    $_SESSION['form_data'] = $_POST;
    header("Location: ../soporte.php?mensaje=" . urlencode($mensaje_error));
    $conn->close();
}

exit();
?>