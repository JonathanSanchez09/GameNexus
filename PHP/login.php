<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        header("Location: ../html/login.html?error=" . urlencode("Todos los campos son obligatorios."));
        exit();
    }
    
    // Conexión a la base de datos
    $conn = new mysqli("localhost", "root", "", "tienda_videojuegos");
    if ($conn->connect_error) {
        die("Error en la conexión: " . $conn->connect_error);
    }
    
    $sql = "SELECT * FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if ($resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();
        
        if (password_verify($password, $usuario['password'])) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['email'] = $usuario['email'];
            header("Location: ../html/index.php");
            exit();
        } else {
            header("Location: ../html/login.html?error=" . urlencode("Contraseña incorrecta."));
            exit();
        }
    } else {
        header("Location: ../html/login.html?error=" . urlencode("El usuario no existe."));
        exit();
    }
    
    $conn->close();
    
} else {
    echo "Acceso denegado.";
}
