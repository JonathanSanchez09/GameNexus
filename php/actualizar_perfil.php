<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_id = $_SESSION['usuario_id'];
    
    // Verificamos si los campos están vacíos y asignamos null si es así
    $nombre_completo = trim($_POST['nombre_completo'] ?? '');
    $fecha_nacimiento_input = trim($_POST['fecha_nacimiento'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');

    // Esta es la línea clave que corrige el problema de la fecha
    $fecha_nacimiento = !empty($fecha_nacimiento_input) ? $fecha_nacimiento_input : null;

    $stmt = $conn->prepare("UPDATE usuarios SET nombre_completo = ?, fecha_nacimiento = ?, direccion = ? WHERE id = ?");
    
    // Usamos 'sssi' para indicar que los primeros tres son strings y el último un entero
    // MySQLi puede manejar valores NULL para tipos 's' (string) si la columna lo permite
    $stmt->bind_param("sssi", $nombre_completo, $fecha_nacimiento, $direccion, $usuario_id);
    
    if ($stmt->execute()) {
        header('Location: ../perfil.php?exito=1');
    } else {
        // Redirigir con un error si la ejecución falla
        header('Location: ../perfil.php?error=1');
    }

    $stmt->close();
    $conn->close();
} else {
    header('Location: ../perfil.php');
}
?>