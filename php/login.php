<?php
// Desactivar en producción; solo mostrar un error genérico.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// --- CONFIGURACIÓN DE SEGURIDAD DE SESIÓN ---
// Evita que JavaScript acceda a la cookie de sesión (protección XSS)
ini_set('session.cookie_httponly', 1); 
// Si usas HTTPS, añade: ini_set('session.cookie_secure', 1);

session_start();

// --- CREDENCIALES (MOVER A VARIABLES DE ENTORNO EN PROD) ---
$db_host = "db";
$db_user = "usuario";
$db_pass = "contrasena";
$db_name = "tienda_videojuegos";

// Mensaje de error único para evitar enumeración de usuarios
$GENERIC_ERROR_MSG = "Credenciales incorrectas o usuario no encontrado.";


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validación básica de campos
    if (empty($email) || empty($password)) {
        echo json_encode(["success" => false, "message" => "Todos los campos son obligatorios."]);
        exit();
    }
    
    // Conexión segura
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
    if ($conn->connect_error) {
        echo json_encode(["success" => false, "message" => "Error de servicio. Intente más tarde."]);
        exit();
    }

    // Consulta Preparada (Blindaje contra SQLi)
    $sql = "SELECT id, nombre, email, contrasena FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        // No expone el error de SQL
        echo json_encode(["success" => false, "message" => "Error de servicio. Intente más tarde."]);
        $conn->close();
        exit();
    }
    
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();
        $stmt->close(); // Liberar el statement antes de continuar

        // --- VERIFICACIÓN DE CONTRASEÑA (Cifrado) ---
        if (password_verify($password, $usuario['contrasena'])) {
            
            // --- REGENERACIÓN DE SESIÓN (Prevención de Secuestro) ---
            session_regenerate_id(true); 
            
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['email'] = $usuario['email'];
            $_SESSION['nombre'] = $usuario['nombre'];
            
            // Lógica simple para un futuro sistema Anti-Fuerza Bruta
            // if (isset($_SESSION['login_attempts'])) unset($_SESSION['login_attempts']);

            $conn->close();
            echo json_encode(["success" => true]);
            exit();
        } 
    }
    
    // --- RESPUESTA UNIFICADA (Protección contra Enumeración) ---
    $conn->close();
    echo json_encode(["success" => false, "message" => $GENERIC_ERROR_MSG]);

} else {
    echo json_encode(["success" => false, "message" => "Acceso denegado."]);
}
?>