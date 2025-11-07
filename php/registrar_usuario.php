<?php
// MANTENER para desarrollo, pero DESACTIVAR en producción
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// --- 1. CONFIGURACIÓN DE SEGURIDAD (Simulación de credenciales seguras) ---
// En un entorno real, estas credenciales vendrían de variables de entorno o un archivo .env
$db_host = "db";
$db_user = "usuario";
$db_pass = "contrasena";
$db_name = "tienda_videojuegos";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // --- 2. VALIDACIÓN RIGUROSA DEL LADO DEL SERVIDOR ---

    if (empty($nombre) || empty($email) || empty($password)) {
        echo json_encode(["success" => false, "message" => "Todos los campos son obligatorios."]);
        exit();
    }
    
    // Validación de longitud del nombre de usuario
    if (strlen($nombre) < 3) {
        echo json_encode(["success" => false, "message" => "El nombre debe tener al menos 3 caracteres."]);
        exit();
    }
    
    // Validación de formato de correo (Doble verificación del Frontend)
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["success" => false, "message" => "El correo electrónico no es válido."]);
        exit();
    }

    // Validación de complejidad de contraseña (¡Añadiendo la regex de complejidad!)
    $passwordRegex = "/^(?=.*\d)(?=.*[A-Z])(?=.*[!@#$%^&*()_+])[A-Za-z\d!@#$%^&*()_+]{8,}$/";
    if (!preg_match($passwordRegex, $password)) {
        echo json_encode(["success" => false, "message" => "La contraseña debe tener al menos 8 caracteres, una letra mayúscula, un número y un carácter especial."]);
        exit();
    }

    // --- 3. CONEXIÓN Y PREPARACIÓN DE CONSULTAS ---
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

    if ($conn->connect_error) {
        // En producción, solo mostrar un mensaje genérico.
        echo json_encode(["success" => false, "message" => "Error de servicio. Intente más tarde."]);
        exit();
    }

    // --- 4. PREVENCIÓN DE INYECCIÓN SQL Y VERIFICACIÓN DE UNICIDAD ---
    $stmt_check = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
    if (!$stmt_check) {
        // En producción, solo mostrar un mensaje genérico.
        echo json_encode(["success" => false, "message" => "Error interno al verificar usuario."]);
        exit();
    }
    $stmt_check->bind_param("s", $email);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        echo json_encode(["success" => false, "message" => "Este correo ya está registrado."]);
        $stmt_check->close();
        $conn->close();
        exit();
    }
    $stmt_check->close();

    // --- 5. CIFRADO ROBUSTO DE CONTRASEÑA ---
    // PASSWORD_DEFAULT es Bcrypt actualmente, garantizando un hash seguro con salt automático.
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // --- 6. INSERCIÓN FINAL SEGURA (Consultas Preparadas) ---
    $stmt_insert = $conn->prepare("INSERT INTO usuarios (nombre, email, contrasena) VALUES (?, ?, ?)");
    if (!$stmt_insert) {
        echo json_encode(["success" => false, "message" => "Error interno al preparar la inserción."]);
        $conn->close();
        exit();
    }
    $stmt_insert->bind_param("sss", $nombre, $email, $hashedPassword);

    if ($stmt_insert->execute()) {
        echo json_encode(["success" => true, "message" => "Usuario registrado con éxito. Redirigiendo..."]);
    } else {
        echo json_encode(["success" => false, "message" => "Error al registrar el usuario: " . $stmt_insert->error]);
    }

    $stmt_insert->close();
    $conn->close();

} else {
    echo json_encode(["success" => false, "message" => "Acceso denegado."]);
}
?>