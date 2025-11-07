<?php
session_start();
include 'conexion.php'; 

// Redirige si la solicitud no es POST o si faltan datos
if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST['nombre'], $_POST['email'], $_POST['motivo'], $_POST['mensaje'])) {
    header("Location: ../soporte.php");
    exit();
}

// Opciones de motivo válidas (debe coincidir con soporte.php)
$motivos_validos = ['problema_compra', 'error_juego', 'sugerencia', 'otro'];

// 1. Validar y sanear los datos de entrada

// 🛡️ PROTECCIÓN XSS: Sanitizar nombre y motivo
$nombre = trim(htmlspecialchars($_POST['nombre'] ?? '', ENT_QUOTES, 'UTF-8'));
$motivo = trim(htmlspecialchars($_POST['motivo'] ?? '', ENT_QUOTES, 'UTF-8'));

// 🛡️ PROTECCIÓN XSS: Sanitizar el mensaje (el campo más vulnerable)
$mensaje_usuario = trim(htmlspecialchars($_POST['mensaje'] ?? '', ENT_QUOTES, 'UTF-8'));

// 🧹 Limpieza simple de email (no se usa htmlspecialchars porque lo rompe)
$email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);

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

// 🆕 Validación: Asegurar que el motivo sea uno de los predefinidos
if (!in_array($motivo, $motivos_validos)) {
    $mensaje = "❌ El motivo de contacto seleccionado no es válido.";
    $_SESSION['form_data'] = $_POST;
    header("Location: ../soporte.php?mensaje=" . urlencode($mensaje));
    exit();
}


// 2. Insertar el ticket en la base de datos
try {
    // ✅ PROTECCIÓN SQLi: Se usan Sentencias Preparadas (obligatorio)
    $stmt = $conn->prepare("INSERT INTO tickets_soporte (nombre, email, motivo, mensaje, usuario_id) VALUES (?, ?, ?, ?, ?)");
    
    // Los datos ya están sanitizados y listos para ser guardados
    $stmt->bind_param("ssssi", $nombre, $email, $motivo, $mensaje_usuario, $usuario_id);
    
    if ($stmt->execute()) {
        $mensaje_exito = "✅ ¡Gracias por contactarnos, $nombre! Te responderemos pronto a tu correo.";
        header("Location: ../soporte.php?mensaje=" . urlencode($mensaje_exito));
        
        // Cierre de recursos más limpio
        $stmt->close();
        $conn->close();

    } else {
        $mensaje_error = "❌ Error al guardar el mensaje. Inténtalo de nuevo."; // Ocultar $stmt->error por seguridad
        $_SESSION['form_data'] = $_POST;
        header("Location: ../soporte.php?mensaje=" . urlencode($mensaje_error));
        
        $stmt->close();
        $conn->close();
    }
} catch (Exception $e) {
    // Manejo de errores más robusto y oculto
    $mensaje_error = "❌ Ocurrió un error inesperado en el servidor.";
    $_SESSION['form_data'] = $_POST;
    header("Location: ../soporte.php?mensaje=" . urlencode($mensaje_error));
    $conn->close();
}

exit();
?>